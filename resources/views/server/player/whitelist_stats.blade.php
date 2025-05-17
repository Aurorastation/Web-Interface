{{--Copyright (c) 2016-2017 "Arrow768"--}}

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
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Whitelist Stats</div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Species</th>
                                <th>Whitelist</th>
                                <th>Characters</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats as $stat)
                                <tr @if($stat->subspecies == 1)class="bg-info"@endif()>
                                    <td>{{$stat->status_name}}</td>
                                    <td>{{$stat->player_count}}</td>
                                    <td>{{$stat->char_count}}</td>
                                    <td><a href="{{route('server.players.whitelist_stats.jobs',['species'=>$stat->status_name])}}" class="btn btn-success">Jobs</a></td>
                                </tr>
                            @endforeach()
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script src="//cdn.ckeditor.com/4.5.11/standard/ckeditor.js"></script>
    <script>CKEDITOR.replace('content');</script>
@endsection