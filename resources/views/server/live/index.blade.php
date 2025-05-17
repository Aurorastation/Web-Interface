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
    <div class="container" id="app">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Server Options</div>
                    <div class="panel-body">
                        <p>Please select one of the options below to use Live Control</p>
                        @can('server_remote_coms')
                            <p><a href="{{route("server.live.coms")}}">Communication Options</a></p>
                        @endcan

                        @can('server_remote_ghosts')
                            <p><a href="{{route("server.live.ghosts")}}">Ghost Options</a></p>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
@endsection
