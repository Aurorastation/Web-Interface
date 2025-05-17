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

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            //If the users byond account is not linked and he doesnt have permission to edit the library -> Abort
            if($request->user()->byond_linked == False && $request->user()->cannot('server_stats_show'))
            {
                abort('403','Your byond account is not linked to your forum account.');
            }
            return $next($request);
        });
    }

    /**
     * Index File - With search function for details for a single game id and a time range
     * Single Game ID - Shows detailed stats gathered for a single game id
     * Time Range - Shows statistics for a time range
     */

    public function index()
    {
        return view("server.stats.index");
    }

    public function round(Request $request, $game_id = null)
    {
        if($game_id == null)
            $game_id = $request->input("game_id");

        //Query the db for the game id
        $game_details = \DB::connection('server')->table('feedback')->where('game_id',$game_id)->get();
        //Prep the stats for display
//        dd($game_details);

        $details = array();
        $details[""] = $game_details->contains('var_name','blackbox_destroyed');

        $details["game_details"] = $game_details;
        return view("server.stats.round",$details);
    }

    public function antag(Request $request, $game_id = null)
    {
        if($game_id == null)
            $game_id = $request->input("game_id");

        //Query the db for the game id
        $antags = \DB::connection('server')->table('antag_log')->where('game_id',$game_id)->orderby('char_name','desc')->get();

        return view("server.stats.antag",["antags"=>$antags, "game_id"=>$game_id]);
    }

    public function duration(Request $request)
    {
        //Get a total of rounds in the duration
        //Get counts of the various game_modes for the duration
        //Get how often the blackbox has been destroyed
    }
}
