{{--Copyright (c) 2016 "Arrow768"--}}

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
            <div class="col-md-4 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">User Details</div>

                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>ID</td>
                            <td>{{$player->id}}</td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>{{$player->ckey}}</td>
                        </tr>
                        <tr>
                            <td>First Seen</td>
                            <td>{{$player->firstseen}}</td>
                        </tr>
                        <tr>
                            <td>Last Seen</td>
                            <td>{{$player->lastseen}}</td>
                        </tr>
                        <tr>
                            <td>IP</td>
                            <td>{{$player->ip}}</td>
                        </tr>
                        <tr>
                            <td>Rank</td>
                            {{--<td>@if(count($player->serverrank)) {{$player->serverrank->rank}} @else Player @endif</td>--}}
                            <td>{{$player->rank}}</td>
                        </tr>
                        <tr>
                            <td>Forum User</td>
                            <td>@if($player_name = $player->check_forum_linked())<span class="label label-success">{{$player_name}}</span>@else<span class="label label-danger">Not Linked</span>@endif</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @can('server_players_whitelists_show')
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">User Whitelists</div>

                    <table class="table table-bordered">
                        <tbody>
                        @foreach($whitelists as $whitelist)
                            <tr>
                                <td>@if($whitelist->subspecies)-->  @endif{{$whitelist->status_name}}</td>
                                @if($whitelist->active == TRUE)
                                    <td>
                                        <span class="label label-success">
                                            <span class="glyphicon glyphicon-ok"></span>
                                        </span>
                                    </td>
                                    @can('server_players_whitelists_edit')
                                        <td>
                                            @if($whitelist->subspecies == 0)
                                                <a href="{{route('server.players.whitelist.remove',['player_id'=>$player->id,'whitelist'=>$whitelist->flag])}}"
                                                   class="btn btn-danger" role="button">Remove Whitelist</a>
                                            @endif
                                        </td>
                                    @endcan
                                @else
                                    <td>
                                        <span class="label label-danger">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </span>
                                    </td>
                                    @can('server_players_whitelists_edit')
                                    <td>
                                        @if($whitelist->subspecies == 0)
                                            <a href="{{route('server.players.whitelist.add',['player_id'=>$player->id,'whitelist'=>$whitelist->flag])}}"
                                               class="btn btn-success" role="button">Add Whitelist</a>
                                        @endif
                                    </td>
                                    @endcan
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endcan()
        </div>
        {{-- Warnings and Notes Row--}}
        @can('server_players_warnings_show')
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">User Warnings</div>

                    <div class="panel-body">
                        <table id="user-warnings-table" class="table table-condensed">
                            <thead>
                            <tr>
                                <th width="5px">#</th>
                                <th>Date</th>
                                <th width="30px">Srv</th>
                                <th width="30px" >Ack</th>
                                <th>Admin</th>
                                <th>Reason</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan()
        @can('server_players_notes_show')
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">User Notes</div>

                    <div class="panel-body">
                        <table id="user-notes-table" class="table table-condensed">
                            <thead>
                            <tr>
                                <th width="5px">#</th>
                                <th>Date</th>
                                <th>Admin</th>
                                <th>Content</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan()
        @can('server_chars_show')
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Char Overview</div>

                    <div class="panel-body">
                        <table id="char-table" class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan()
    </div>
@endsection

@section('javascripts')

    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(function() {
            @can('server_players_warnings_show')
            $('#user-warnings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('server.players.warnings.data',["player_id"=>$player->id]) }}',
                columns:[
                    { data: 'id', name: 'id'},
                    { data: 'time', name: 'time'},
                    { data: 'severity', name: 'severity'},
                    { data: 'acknowledged', name: 'acknowledged'},
                    { data: 'a_ckey', name: 'a_ckey'},
                    { data: 'reason', name: 'reason'}
                ]
            });
            @endcan()
            @can('server_players_notes_show')
            $('#user-notes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('server.players.notes.data',["player_id"=>$player->id]) }}',
                columns:[
                    { data: 'id', name: 'id'},
                    { data: 'adddate', name: 'adddate'},
                    { data: 'a_ckey', name: 'a_ckey'},
                    { data: 'content', name: 'content'}
                ]
            });
            @endcan()
            @can('server_chars_show')
            $('#char-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('server.chars.data.ckey',['ckey'=>$player->ckey]) }}',
                columns:[
                    { data: 'name', name: 'name'},
                    { data: 'ckey', name: 'ckey'}
                ]
            });
            @endcan()
        });
    </script>
@endsection