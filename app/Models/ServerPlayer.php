<?php
/**
 * Copyright (c) 2016 "Werner Maisl"
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
use DB;
use Auth;

class ServerPlayer extends Model
{
    public $timestamps = FALSE;
    protected $connection = 'server';
    protected $table = 'player';
    protected $fillable = ['ckey', 'ip', 'whitelist_status'];
    protected $primaryKey = 'id';
    protected $dates = ['firstseen', 'lastseen'];

    public function serverrank()
    {
        return $this->hasOne('App\Models\ServerAdmin', 'ckey', 'ckey');
    }

    /**
     * Adds flags to a player
     *
     * Accepts either a string with the flag value or a array with the falg values as parameter
     * The player_ckey needs to be set
     *
     * The adminname needs to be set or the admin needs to be logged in
     *
     * @param $flag_input
     * @param $adminname
     *
     * @return bool|null
     */
    public function add_player_whitelist_flag($flag_input, $adminname)
    {
        if ($this->ckey == NULL) return NULL;


        //Check if its a string or a arrray
        if (intval($flag_input != 0)) {

            $this->whitelist_status |= $flag_input;
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Added whitelistflag: ' . $flag_input . ' to player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } elseif (is_array($flag_input)) {

            foreach ($flag_input as $flag_value) {
                $this->whitelist_status |= $flag_value;
            }
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Added whitelist flags: ' . impode(";", $flag_input) . ' to player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } elseif (is_string($flag_input)) {

            //Get Whitelist ID for Whitelist Name
            $whitelists = array_flip($this->get_available_whitelists());

            $whitelist_flag = $whitelists[$flag_input];

            $this->whitelist_status |= $whitelist_flag;
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Added whitelist: ' . $flag_input . ' to player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } else {
            return NULL;
        }
    }

    /**
     * Returns a array with all available whitelists (name and status flag)
     *
     * @return array
     */
    public function get_available_whitelists()
    {
        //Get Whitelist Status
        return DB::connection('server')->table('whitelist_statuses')->pluck('status_name', 'flag')->toArray();
    }

    /**
     * Removes flags from a player
     *
     * Accepts either a string with the flag value or a array with the falg values as parameter
     * The player_ckey needs to be set
     *
     * The adminname needs to be set or the admin needs to be logged in
     *
     * @param $flag_input
     * @param $adminname
     *
     * @return bool|null
     */
    public function strip_player_whitelist_flag($flag_input, $adminname)
    {
        if ($this->ckey == NULL) return NULL;

        //Check if its a string or a arrray
        if (intval($flag_input != 0)) {

            $this->whitelist_status -= $flag_input;
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Removed whitelistflag: ' . $flag_input . ' from player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } elseif (is_array($flag_input)) {

            foreach ($flag_input as $flag_value) {
                $this->whitelist_status -= $flag_value;
            }
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Removed whitelist flags: ' . impode(";", $flag_input) . ' from player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } elseif (is_string($flag_input)) {

            //Get Whitelist ID for Whitelist Name
            $whitelists = array_flip($this->get_available_whitelists());

            $whitelist_flag = $whitelists[$flag_input];

            $this->whitelist_status -= $whitelist_flag;
            $this->save();

            //Write Log
            DB::connection('server')->table('whitelist_log')->insert(
                [
                    'datetime' => date("Y-m-d H:i:s", time()),
                    'user' => $adminname,
                    'action_method' => 'Website2',
                    'action' => 'Remove whitelist: ' . $flag_input . ' from player: ' . $this->ckey,
                ]
            );

            return TRUE;

        } else {
            return NULL;
        }
    }

    /**
     * Returns a array with all available whitelists and a true/false value for each one
     *
     * @param bool $with_subspecies
     *
     * @return array|null
     */
    public function get_player_whitelists($with_subspecies = TRUE)
    {
        if ($this->ckey == NULL) return NULL;
        if($with_subspecies){
            $whitelists = DB::connection('server')->table('whitelist_statuses')->orderBy('flag')->orderBy('subspecies')->get()->toArray();
        }else{
            $whitelists = DB::connection('server')->table('whitelist_statuses')->orderBy('flag')->orderBy('subspecies')->where('subspecies',0)->get()->toArray();
        }

        foreach ($whitelists as $whitelist) {
            if (($this->whitelist_status & $whitelist->flag) != 0) {
                $whitelist->active = 1;
            } else {
                $whitelist->active = 0;
            }
        }
        return $whitelists;
    }

    /**
     * Checks if the player holds a specific whitelist
     *
     * @param string|int $required_whitelist String of the required whitelist or integer of the required flag
     *
     * @return bool True if the player holds the specified whitelist
     */
    public function check_whitelist($required_whitelist)
    {
        if (!$this->whitelist_status) return NULL;
        if ($this->ckey == NULL) return NULL;

        if (is_string($required_whitelist)) {
            $whitelist = DB::connection('server')->table('whitelist_statuses')->where('status_name', $required_whitelist)->first();
            if (!isset($whitelist->flag)) return FALSE;
            if (($this->whitelist_status & $whitelist->flag) != 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif (is_int($required_whitelist)) {
            if (($this->whitelist_status & $required_whitelist) != 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Get the chars of a player
     */
    public function get_chars()
    {
        return \App\Models\ServerCharacter::where('ckey', $this->ckey)->get();
    }

    /**
     * Get the char ids of a player
     */
    public function get_char_ids()
    {
        return \App\Models\ServerCharacter::where('ckey', $this->ckey)->pluck('id');
    }

    /**
     * Check if the player "owns" a specific char
     *
     * @param int char_id The id of the char that should be checked
     *
     * @returns bool
     */
    public function check_player_char($char_id)
    {
        return \App\Models\ServerCharacter::where('ckey', $this->ckey)->where('id', $char_id)->count() > 0;
    }

    /**
     * Check if the Player has a forum account linked
     *
     * @returns bool
     */
    public function check_forum_linked() //TODO: Fix this
    {
        $forum_user = User::where('byond_key',$this->ckey)->first();
        if($forum_user != NULL)
            return $forum_user->name;
        else
            return false;
    }

    public function is_perma_banned()
    {
        $permabancount = \Illuminate\Support\Facades\DB::connection('server')
            ->table('bans')
            ->where('ckey', '=', $this->ckey)
            ->where('bantype','=','PERMABAN')
            ->whereNull('unbanned_datetime')
            ->count();

        return $permabancount > 0;
    }
}
