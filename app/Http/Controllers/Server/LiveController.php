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
namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Server\ServerQuery;

class LiveController extends Controller
{
    public function index()
    {
        return view('server.live.index');
    }

    public function getComoptions(Request $request)
    {
        if ($request->user()->cannot('server_remote_coms')) {
            abort('403', 'You do not have the required permission');
        }

        return view('server.live.coms');
    }

    public function getGhostoptions(Request $request)
    {
        if ($request->user()->cannot('server_remote_ghosts')) {
            abort('403', 'You do not have the required permission');
        }

        return view('server.live.ghosts');
    }

    public function getFaxmachines(Request $request)
    {
        if ($request->user()->cannot('server_remote_coms')) {
            abort('403', 'You do not have the required permission');
        }
        $query = New ServerQuery();
        try {
            $query->setUp(config('aurora.gameserver_address'),config('aurora.gameserver_port'),config('aurora.gameserver_auth'));
            $query->runQuery([
                "query" => "get_faxmachines"
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == "200") {
            return json_encode($query->response->data);
        } else {
            abort($query->response->statuscode,$query->response);
        }
    }

    public function getGhosts(Request $request)
    {
        if ($request->user()->cannot('server_remote_ghosts')) {
            abort('403', 'You do not have the required permission');
        }
        $query = New ServerQuery();
        try {
            $query->setUp(config('aurora.gameserver_address'), config('aurora.gameserver_port'), config('aurora.gameserver_auth'));
            $query->runQuery([
                "query" => "get_ghosts"
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == "200") {
            return json_encode($query->response->data);
        } else {
            abort($query->response->statuscode);
        }
    }

    public function postSendfax(Request $request)
    {
        if ($request->user()->cannot('server_remote_coms')) {
            abort('403', 'You do not have the required permission');
        }
        $query = New ServerQuery;
        try {
            $query->setUp(config('aurora.gameserver_address'), config('aurora.gameserver_port'), config('aurora.gameserver_auth'));

            $query->runQuery([
                "query" => "send_fax",
                "senderkey" => $request->user()->username,
                "title" => $request->input('faxtitle'),
                "body" => nl2br($request->input('faxbody')),
                "announce" => $request->input('faxannounce'),
                "target" => $request->input('faxtargets'),
                "stamptext" => $request->input('faxstamptext')
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == 200) {
            return json_encode($query->response->data);
        } else {
            abort($query->response->statuscode);
        }
    }

    public function postSendreport(Request $request)
    {
        if ($request->user()->cannot('server_remote_coms')) {
            abort('403', 'You do not have the required permission');
        }

        $query = New ServerQuery;

        try {
            $query->setUp(config('aurora.gameserver_address'), config('aurora.gameserver_port'), config('aurora.gameserver_auth'));

            $query->runQuery([
                "query" => "send_commandreport",
                "senderkey" => $request->user()->username,
                "title" => $request->input('reporttitle'),
                "body" => $request->input('reportbody'),
                "announce" => $request->input('reportannounce'),
                "type" => $request->input('reporttype'),
                "sendername" => $request->input('reportsender'),
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == "200") {
            return json_encode($query->response->data);
        } else {
            abort($query->response->statuscode);
        }
    }

    public function postGrantrespawn(Request $request)
    {
        if ($request->user()->cannot('server_remote_ghosts')) {
            abort('403', 'You do not have the required permission');
        }

        $query = New ServerQuery;

        try {
            $query->setUp(config('aurora.gameserver_address'), config('aurora.gameserver_port'), config('aurora.gameserver_auth'));

            $query->runQuery([
                "query" => "grant_respawn",
                "senderkey" => $request->user()->username,
                "target" => $request->input('target')
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == "200") {
            return json_encode($query->response->data);
        } else {
            abort($query->response->statuscode);
        }
    }
}
