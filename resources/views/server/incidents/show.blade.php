{{--Copyright (c) 2016 "Werner Maisl"--}}

{{--This file is part of the Aurora Webinterface--}}

{{--The Aurora Webinterface is free software: you can redistribute it and/or modify--}}
{{--it under the terms of the GNU Affero General Public License as--}}
{{--published by the Free Software Foundation, either version 3 of the--}}
{{--License, or (at your option) any later version.--}}

{{--This program is distributed in the hope that it will be useful,--}}
{{--but WITHOUT ANY WARRANTY; without even the implied warranty of--}}
{{--MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the--}}
{{--GNU Affero General Public License for more details.--}}

{{--You should have received a copy of the GNU Affero General Public License--}}
{{--along with this program. If not, see <http://www.gnu.org/licenses/>.<!DOCTYPE html>--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if(isset($incident->deleted_at))
            <div class="alert alert-danger"><b>This incident has been deleted by (the) {{$incident->deleted_by}} and is only displayed to staff members</b></div>
        @endif()
        <div class="col-md-6 col-md-offset-1">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading"><b>Incident: </b>{{$incident->UID}}</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Date Time:</b></td>
                        <td>{{$incident->datetime}}</td>
                    </tr>
                    <tr>
                        <td><b>Notes:</b></td>
                        @if($incident->notes != "")
                            <td>{{$incident->notes}}</td>
                        @else
                            <td>- No Notes on Record -</td>
                        @endif
                    </tr>
                    @if($incident->brig_sentence != 0)
                        <tr>
                            @if($incident->brig_sentence >= 90)
                                <td><b>Brig Sentence:</b></td>
                                <td>Holding Until Transfer</td>
                            @else
                                <td><b>Brig Sentence:</b></td>
                                <td>{{$incident->brig_sentence}} minutes</td>
                            @endif
                        </tr>
                    @endif()
                    @if($incident->fine != 0)
                        <tr>
                            <td><b>Fine:</b></td>
                            <td>{{$incident->fine}} Credits</td>
                        </tr>
                    @endif
                    <tr>
                        <td><b>Game ID:</b></td>
                        <td>{{$incident->game_id}}</td>
                    </tr>
                    @if($can_edit)
                    <tr>
                        <td><b>Actions</b></td>
                        @if(!isset($incident->deleted_at))
                            <td><a href="{{route('server.incidents.delete.get',['incident_id'=>$incident->id])}}" class="btn btn-danger" role="button">Delete</a></td>
                        @else
                            <td><a href="{{route('server.incidents.restore.get',['incident_id'=>$incident->id])}}" class="btn btn-success @cannot("server_players_incidents_edit") disabled @endcan()" role="button">Restore</a></td>
                        @endif
                    </tr>
                    @endif()
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading"><b>Charges:</b></div>

                <table class="table">
                    <tbody>
                    @foreach(json_decode($incident->charges) as $charge)
                        <tr>
                            <td>{{$charge}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Evidence:</b></div>

                <table class="table">
                    <tbody>
                    @foreach(json_decode($incident->evidence) as $evidence_name=>$evidence_description)
                        <tr>
                            <td><b>{{$evidence_name}}</b></td>
                            <td>{{$evidence_description}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading"><b>Witnesses:</b></div>

                <table class="table">
                    <tbody>
                    @if(json_decode($incident->arbiters,true)["Witness"] != NULL)
                        @foreach(json_decode($incident->arbiters,true)["Witness"] as $witness=>$statement)
                            <tr>
                                <td><b>{{$witness}}</b></td>
                                <td>{{$statement}}</td>
                            </tr>
                        @endforeach
                    @endif()
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection