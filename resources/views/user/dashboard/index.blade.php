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
            <div class="alert alert-success">This is the User Dashboard, here you can find various infos about your
                useraccount on the server
            </div>

            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Warning Status</div>

                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>Total Warnings</td>
                            <td><span class="label label-default">{{$warnings->get_total_count()}}</span></td>
                        </tr>
                        <tr>
                            <td>Major Warnings</td>
                            <td><span class="label label-warning">{{$warnings->get_major_count()}}</span></td>
                        </tr>
                        <tr>
                            <td>Unackd Warnings</td>
                            <td><span class="label label-danger">{{$warnings->get_unack_count()}}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Whitelist Status</div>
                    @include('components.whiteliststatustable')
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Pending CCIA Actions per Character</div>

                    <table class="table table-bordered">
                        <tbody>
                        @foreach($chars as $char)
                            @if($char->active_ccia_action_count() > 0)
                            <tr>
                                <td colspan="2"><b>{{$char->name}}</b></td>
                            </tr>
                            @foreach($char->cciaactions()->where(function($query) {$query->where("expires_at", ">=", DB::raw("NOW()"))->orWhereNull('expires_at');})->get() as $cciaaction)
                                <tr>
                                    <td>{{$cciaaction->id}}</td>
                                    <td><a href="{{route('ccia.actions.show.get',$cciaaction->id)}}">{{$cciaaction->title}}</a></td>
                                </tr>
                            @endforeach
                            @endif()
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
