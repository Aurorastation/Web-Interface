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

@section('styles')
    <link href="{{ asset('/DataTables/datatables.min.css') }}" rel="stylesheet">
    <style>
        th.rotate {
            /* Something you can count on */
            height: 80px;
            white-space: nowrap;
        }

        th.rotate > div {
            transform:
                /* Magic Numbers */
                    translate(0px, 30px)
                        /* 45 is really 360 - 45 */
                    rotate(300deg);
            width: 20px;
            font-size: 80%;
        }
        th.rotate > div > span {
            border-bottom: 1px solid #ccc;
            padding: 5px 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Search Filter</div>
            <div class="panel-body">
                {{Form::open(array('route' => 'server.permissions.index','method' => 'post','class' =>'form-inline')) }}
                {{Form::token()}}
                {{Form::hidden('s','1')}}

                {{Form::bsText('ckey')}}
                {{Form::bsText('rank')}}
                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}

                {{ Form::close() }}
                @can('server_permissions_edit')<p><a href="{{route('server.library.add.post')}}" class="btn btn-success" role="button">Add new Permission</a></p>@endcan()
            </div>
        </div>
    </div>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Permission Matrix</div>
            <div class="panel-body">
                <table>
                    <thead>
                    <tr>
                        <th class="rotate"><div><span>ckey</span></div></th>
                        <th class="rotate"><div><span>rank</span></div></th>
                        @foreach($flags as $flag => $value)
                            <th class="rotate"><div><span>{{$flag}}</span></div></th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>{{$admin->ckey}}</td>
                            <td>{{$admin->rank}}</td>
                            @foreach($flags as $flag => $value)
                                @if($admin->has_flag($flag))
                                    <td>
                                        <a href="{{route('servers.permissions.flag.remove',['permission_id'=>$admin->id,'flag'=>$flag])}}"
                                           class="btn-xs btn-success" role="button"><span class="glyphicon glyphicon-ok"></span></a>
                                    </td>
                                @else()
                                    <td>
                                        <a href="{{route('servers.permissions.flag.add',['permission_id'=>$admin->id,'flag'=>$flag])}}"
                                           class="btn-xs btn-danger" role="button"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                @endif()
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')

    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
@endsection