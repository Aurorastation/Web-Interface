<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\ServerPlayer;
use App\Models\SiteRole;
use App\Services\Server\Helpers;

class User extends Authenticatable
{
    use Notifiable;
    use Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'formatted_name', 'email', 'password', 'refresh_token', 'byond_key', 'photo_url', 'linked_accounts', 'primary_group', 'secondary_groups'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getLinkedAccountsAttribute($value)
    {
        return unserialize(base64_decode($value));
    }

    public function setLinkedAccountsAttribute($value)
    {
        $this->attributes['linked_accounts'] =  base64_encode(serialize($value));
    }

    public function getPrimaryGroupAttribute($value)
    {
        return unserialize(base64_decode($value));
    }

    public function setPrimaryGroupAttribute($value)
    {
        $this->attributes['primary_group'] = base64_encode(serialize($value));
    }

    public function getSecondaryGroupsAttribute($value)
    {
        return unserialize(base64_decode($value));
    }

    public function setSecondaryGroupsAttribute($value)
    {
        $this->attributes['secondary_groups'] = base64_encode(serialize($value));
    }

    public function getByondLinkedAttribute()
    {
        return !!$this->byond_key;
    }

    public function getUserIdAttribute()
    {
        return $this->id;
    }

    public function getUsernameAttribute()
    {
        return $this->name;
    }

    /**
     * Checks if the user has a role. String or object accepted
     *
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Links the Forum User with the website roles
     */
    public function roles()
    {
        return $this->belongsToMany(SiteRole::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Returns the Numeric Player ID from the Server
     *
     * @return integer
     */
    public function getServerPlayerID()
    {
        if ($this->byond_linked == True) {
            $player = ServerPlayer::where('ckey', $this->byond_key)->first();
            return $player->id;
        } else {
            return NULL;
        }
    }

    /**
     * Returns the ServerPlayer if the byond account is linked
     *
     * @return ServerPlayer
     */
    public function serverplayer()
    {
        if ($this->byond_linked == True) {
            return ServerPlayer::where('ckey', $this->byond_key)->first();
        } else {
            return NULL;
        }
    }

    /**
     * Checks if a user has a specific char
     *
     * @param $char_id
     *
     * @return bool|null
     */
    public function checkPlayerChar($char_id)
    {
        if ($this->byond_linked == True) {
            $char_count = ServerCharacter::where('id', $char_id)->where('ckey', $this->byond_key)->count();
            if ($char_count == 1)
                return TRUE;
            else
                return FALSE;
        } else {
            return NULL;
        }
    }

    /**
     * Linked the user account to a specified ckey. Will also create the link in the forums.
     * 
     * Will throw if the user is already linked with a byond key.
     * 
     * @param $byond_key
     */
    public function linkWithCkey($byond_key)
    {
        if ($this->byond_linked == True) {
            throw new \Exception("User already linked with a ckey.");
        }

        $sanitized_key = Helpers::sanitize_ckey($byond_key);
        $this->byond_key = $sanitized_key;
        $this->save();

        //Update the forum
        $base_url = config('aurora.forum_url');
        if ($base_url) {
            $request_url = $base_url . 'api/core/members/' . $this->id .
                '?key=' . config('aurora.forum_api_key') .
                '&customFields[' . config('aurora.forum_byond_attribute') . ']=' . $sanitized_key;
            $client = new \GuzzleHttp\Client();
            $client->request('POST', $request_url);
        }
    }
}
