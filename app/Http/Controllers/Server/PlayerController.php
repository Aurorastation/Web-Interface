<?php
/**
 * Copyright (c) 2016-2017 "Werner Maisl"
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
use Yajra\Datatables\Datatables;
use App\Models\ServerPlayer;
use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PlayerController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->cannot('server_players_show')) {
                abort('403', 'You do not have the required permission');
            }
            return $next($request);
        });
    }

    /**
     * Displays all the players and their whitelists in a laravel datatable
     */
    public function index()
    {
        return view('server.player.index');
    }

    public function getCkey($ckey, Request $request)
    {
        $id = ServerPlayer::where('ckey', $ckey)->select('id')->first();
        if ($id != NULL) {
            return redirect()->route('server.players.show', ['player_id' => $id]);
        } else {
            return redirect()->route('server.players.index');
        }
    }

    /**
     * Shows the whitelist Status of a single player
     * Displays a form to add / remove a whitelist
     *
     * @param         $player_id
     * @param Request $request
     */
    public function show($player_id, Request $request)
    {
        $player = ServerPlayer::findOrFail($player_id);

        return view('server.player.show', ['player' => $player, 'whitelists' => $player->get_player_whitelists(0)]);
    }

    /**
     * Show the number of chars and players per whitelist
     */
    public function showWhitelistStats(Request $request)
    {
        if ($request->user()->cannot('server_players_whitelists_stats')) {
            abort('403', 'You do not have the required permission');
        }

        $stats = DB::connection('server')
            ->table('whitelist_statuses')
            ->select(DB::raw('status_name, (SELECT COUNT(*) FROM ss13_player WHERE whitelist_status & flag) as player_count, (SELECT COUNT(*) FROM ss13_characters where species = status_name AND deleted_at IS NULL) as char_count, subspecies, flag'))
            ->orderBy('flag')
            ->get();

        return view('server.player.whitelist_stats',["stats"=>$stats]);
    }

    public function showWhitelistJobStats(Request $request, $species)
    {
        if ($request->user()->cannot('server_players_whitelists_stats')) {
            abort('403', 'You do not have the required permission');
        }


        $stats = DB::connection('server')
            ->table('characters_log')
            ->select('job_name',DB::raw('COUNT(*) as count'))
            ->join('characters','characters.id','=','characters_log.char_id')
            ->groupby('job_name')
            ->where('characters.species',$species)
            ->whereNull('characters.deleted_at')
            ->orderby('count','desc')
            ->get();

        return view('server.player.whitelist_jobs_stats',["stats"=>$stats,'species'=>$species]);
    }


    public function addWhitelist($player_id, $whitelist, Request $request)
    {
        if ($request->user()->cannot('server_players_whitelists_edit')) {
            abort('403', 'You do not have the required permission');
        }

        //Get Server Player
        $player = ServerPlayer::findOrFail($player_id);

        $player->add_player_whitelist_flag($whitelist, $request->user()->name);

        Log::notice('perm.whitelist.add - Whitelist has been added', ['user_id' => $request->user()->user_id, 'whitelist' => $whitelist, 'player_ckey' => $player->ckey]);

        return redirect()->route('server.players.show', ['player_id' => $player_id]);
    }

    public function removeWhitelist($player_id, $whitelist, Request $request)
    {
        if ($request->user()->cannot('server_players_whitelists_edit')) {
            abort('403', 'You do not have the required permission');
        }

        //Get Server Player
        $player = ServerPlayer::findOrFail($player_id);
        $player->strip_player_whitelist_flag($whitelist, $request->user()->name);

        Log::notice('perm.whitelist.remove - Whitelist has been removed', ['user_id' => $request->user()->user_id, 'whitelist' => $whitelist, 'player_ckey' => $player->ckey]);
        return redirect()->route('server.players.show', ['player_id' => $player_id]);
    }


    public function getPlayerWarningsData($player_id, Request $request)
    {
        if ($request->user()->cannot('server_players_warnings_show')) {
            abort('403', 'You do not have the required permission');
        }

        $player = ServerPlayer::findOrFail($player_id);

        $warnings = DB::connection('server')
            ->table('warnings')
            ->where('ckey', '=', $player->ckey)
            ->select(['id', 'time', 'severity', 'acknowledged', 'a_ckey', 'reason']);

        return Datatables::of($warnings)
            ->make();
    }

    public function getPlayerNotesData($player_id, Request $request)
    {
        if ($request->user()->cannot('server_players_notes_show')) {
            abort('403', 'You do not have the required permission');
        }

        $player = ServerPlayer::findOrFail($player_id);

        $notes = DB::connection('server')
            ->table('notes')
            ->where('ckey', '=', $player->ckey)
            ->select(['id', 'adddate', 'a_ckey', 'content']);

        return Datatables::of($notes)
            ->make();

    }

    public function getPlayerData()
    {
        $players = ServerPlayer::select(['id', 'ckey']);

        return Datatables::of($players)
            ->editColumn('ckey', '<a href="{{route(\'server.players.show\',[\'player_id\'=>$id])}}">{{$ckey}}</a>')
            ->rawColumns(['ckey'])
            ->make();
    }
}
