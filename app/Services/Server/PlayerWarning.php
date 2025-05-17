<?php
/**
 * Copyright (c) 2016 'Arrow768'
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

namespace App\Services\Server;

use DB;
use Carbon\Carbon;

class PlayerWarning
{
    private $player_ckey = NULL;

    public function __construct($player_ckey)
    {
        $this->player_ckey = $player_ckey;
    }

    public function get_warnings_object()
    {
        //Get the users warnings from the Database
        $warnings = DB::connection('server')->table('warnings')->where('ckey', '=', $this->player_ckey)->where('visible', '=', '1')->get();

        //Transform the timestamps to Ago strings
        foreach ($warnings as $warning) {
            $carbon = new Carbon($warning->time);
            $warning->diff = $carbon->diffForHumans();
        }

        return $warnings;
    }

    public function get_total_count()
    {
        $warning_count = DB::connection('server')->table('warnings')->where('ckey', '=', $this->player_ckey)->where('visible', '=', '1')->count();
        return $warning_count;
    }

    public function get_unack_count()
    {
        $warning_count = DB::connection('server')->table('warnings')->where('ckey', '=', $this->player_ckey)->where('visible', '=', '1')->where('acknowledged', '=', '0')->count();
        return $warning_count;
    }

    public function get_major_count()
    {
        $warning_count = DB::connection('server')->table('warnings')->where('ckey', '=', $this->player_ckey)->where('visible', '=', '1')->where('severity', '=', '1')->count();
        return $warning_count;
    }

    public function get_warning_status_string()
    {
        //You have a total of {{$playerwarning->get_total_count()}} warnings. {{$playerwarning->get_unack_count()}} are unacknowledged and {{$playerwarning->get_major_count()}} major warnings

        $total = $this->get_total_count();
        $unack = $this->get_unack_count();
        $major = $this->get_major_count();

        $warning_string = '';


        //Check if the player has any warning at all
        if ($total == 0) return '<p>You have no warnings</p>';

        //Generate warning string
        //Check if player has more than 1 warning
        $string = '<p>You have ' . ($total > 1 ? ' a single warning. ' : 'a total of ' . $total . ' warnings. ') . '</p>';

        //Tell the player his unacknowledged and major warnings
        $string .= '<p>'.($major == 0 ? 'You have no major warnings on record': 'This includes '.$major.' major'.($major > 1 ? ' warnings':' warning')).'</p>';

        $string .= '<p>'.($unack == 0 ? 'There are no unacknowledged warnings' : 'There ' . ($unack == 1 ? 'is ' : 'are ') . $unack . ' unacknowledged warnings').'</p>';

        return $string;
    }
}