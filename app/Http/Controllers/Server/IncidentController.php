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
use App\Http\Controllers\Controller;
use App\Models\ServerIncident;
use Illuminate\Support\Collection;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        //If the users byond account is not linked and he doesnt have permission to edit the library -> Abort
        if ($request->user()->byond_linked == False && $request->user()->cannot('server_players_incidents_show')) {
            abort('403', 'Your byond account is not linked to your forum account.');
        }
        return view('server.incidents.index');
    }

    public function getShow($incident_id, Request $request)
    {
        //Also show deleted Incidents to mins
        if ($request->user()->can('server_players_incidents_show')) {
            $incident = ServerIncident::withTrashed()->findOrFail($incident_id);
        } else {
            $incident = ServerIncident::findOrFail($incident_id);
        }

        if (!$this->can_see($incident->char_id, $request->user())) {
            abort('403', 'Your are not authorized to view this incident.');
        }

        $can_edit = $this->can_edit($incident->char_id, $request->user());

        return view('server.incidents.show', ['incident' => $incident, 'can_edit' => $can_edit]);
    }

    public function getDelete($incident_id, Request $request)
    {
        $incident = ServerIncident::findOrFail($incident_id);

        if (!$this->can_edit($incident->char_id, $request->user())) {
            abort('403', 'You do not have the required permission');
        }

        $char_id = $incident->char_id;

        Log::notice('perm.incidents.delete - Incident has been deleted', ['user_id' => $request->user()->user_id, 'incident_id' => $incident->id]);
        $incident->delete();

        if($request->user()->can('server_players_incidents_show')){
            return redirect()->route('server.incidents.show.get', ['incident_id' => $incident->id]);
        } else {
            return redirect()->route('server.chars.show.get', ['char_id' => $char_id]);
        }

    }

    public function getRestore($incident_id, Request $request)
    {
        $incident = ServerIncident::withTrashed()->findOrFail($incident_id);

        if (!$request->user()->can('server_players_incidents_edit')){
            abort('403', 'You do not have the required permission');
        }
        $char_id = $incident->char_id;

        Log::notice('perm.incidents.restore - Incident has been restored', ['user_id' => $request->user()->user_id, 'incident_id' => $incident->id]);
        $incident->restore();

        if($request->user()->can('server_players_incidents_show')){
            return redirect()->route('server.incidents.show.get', ['incident_id' => $incident->id]);
        } else {
            return redirect()->route('server.chars.show.get', ['char_id' => $char_id]);
        }
    }

    public function getIncidentDataChar($char_id, Request $request)
    {

        if ($request->user()->can('server_players_incidents_show')) { //Is admin -> Can see deleted incidents
            $incidents = ServerIncident::withTrashed()->where('char_id', $char_id)->select(['id', 'char_id', 'datetime', 'notes', 'brig_sentence', 'fine', 'deleted_at']);
        } else if ($request->user()->checkPlayerChar($char_id) == TRUE) { //Is owner -> Can active incidents
            $incidents = ServerIncident::where('char_id', $char_id)->select(['id', 'char_id', 'datetime', 'notes', 'brig_sentence', 'fine', 'deleted_at']);
        } else { //Is soneone else -> Can see no incidents
            $incidents = new Collection;
        }


        return Datatables::of($incidents)
            ->removeColumn('char_id')
            ->removeColumn('deleted_at')
            ->editColumn('datetime', '@if(isset($deleted_at))<s>@endif()<a href="{{route(\'server.incidents.show.get\',[\'incident_id\'=>$id])}}">{{$datetime}}</a>@if(isset($deleted_at))</s>@endif()')
            ->editColumn('notes', '{{substr($notes,0,150)}}')
            ->editColumn('brig_sentence', '{{$brig_sentence}} minutes')
            ->editColumn('fine', '{{$fine}} Credits')
            ->addColumn('status','@if(isset($deleted_at)) Deleted @else() Active @endif()')
            ->addColumn('action', '<p><a href="{{route(\'server.incidents.show.get\',[\'incident_id\'=>$id])}}" class="btn btn-success" role="button">Show</a></p>')
            ->rawColumns(['datetime', 'action'])
            ->make();
    }

    private function can_edit($char_id, $user)
    {
        //Check if user has char edit persm or is the owner of the car
        if ($user->can('server_players_incidents_edit'))
            return TRUE;
        if ($user->checkPlayerChar($char_id) == TRUE)
            return TRUE;

        return FALSE;
    }

    private function can_see($char_id, $user)
    {
        //Check if user has char show perms or is the owner of the char
        if ($user->can('server_players_incidents_show'))
            return TRUE;
        if ($user->checkPlayerChar($char_id) == TRUE)
            return TRUE;

        return FALSE;
    }
}
