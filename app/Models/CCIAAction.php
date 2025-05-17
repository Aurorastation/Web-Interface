<?php
/**
 * Copyright (c) 2016 "Arrow768"
 *
 * This file is part of Aurorastation-Wi
 * Aurorastation-Wi is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CCIAAction extends Model
{
    use SoftDeletes;

    protected $dates      = ['deleted_at','expires_at'];
    protected $connection = 'server';
    protected $table      = 'ccia_actions';
    protected $fillable   = ['type', 'issuedby', 'details', 'url'];
    protected $primaryKey = 'id';

    public function characters()
    {
        return $this->belongsToMany(ServerCharacter::class,'ccia_action_char','action_id','char_id');
    }

    public function has_linked_char(User $user)
    {
        if($user->byond_linked != True)
            return false;

        //Get the chars from the user
        $player = ServerPlayer::where('ckey',$user->byond_key)->first();
        $char_ids_player = $player->get_char_ids();

        //Chet the chars from the action
        $char_ids_action = $this->characters()->pluck('id');

        //Check if any of that chars is in the char ids of the action
        return $char_ids_player->intersect($char_ids_action)->isNotEmpty();
    }
}
