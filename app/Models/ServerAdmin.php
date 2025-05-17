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

class ServerAdmin extends Model
{
    public static $server_flags = [
        "buildmode" => 1,
        "admin" => 2,
        "ban" => 4,
        "fun" => 5,
        "server" => 16,
        "debug" => 32,
        "possess" => 64,
        "permissions" => 128,
        "stealth" => 256,
        "rejuvinate" => 512,
        "varedit" => 1024,
        "sounds" => 2048,
        "spawn" => 4096,
        "mod" => 8192,
        "dev" => 16384,
        "ccia" => 32768,
    ];

    protected $connection = 'server';
    protected $table      = 'admin';
    protected $fillable   = ['ckey', 'rank', 'flags', 'discord_id'];
    protected $primaryKey = 'id';

    /**
     * Checks if a Admin has the supplied permission
     * @param string $flag
     *
     * @return int|null
     */
    public function has_flag($flag)
    {
        if($this->flags == NULL)
            return NULL;

        if(!isset(self::$server_flags[$flag]))
            return NULL;

        return ($this->flags & self::$server_flags[$flag]) > 0;
    }

    /**
     * Returns all the flags a User has
     * or NULL if Failed
     *
     * @return array|null
     */
    public function get_flags()
    {
        if($this->ckey == NULL|| $this->flags == NULL)
            return NULL;

        $flags = array();
        foreach(self::$server_flags as $flag=>$value)
        {
            $flags[$flag] = $this->has_flag($flag);
        }
        return $flags;
    }

    public function add_server_flag($flag)
    {
        //Check if flag is set

        //Add flag to the users current flags

    }

    public function remove_server_flag($flag)
    {
        //Check if flag is set

        //Remove flags from the users flags

    }
}
