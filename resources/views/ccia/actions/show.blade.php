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
        <div class="col-md-10 col-md-offset-1">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Action #{{$action->id}}: <b>{{$action->title}}</b></h4></div>

                <div class="panel-body">
                    @parsedown($action->details)
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            @can('ccia_action_edit')
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Do not forget to link the action to the correct characters or the action will not be shown on the employment console
            </div>
            @endcan
            <div class="panel panel-default">
                <div class="panel-heading">Linked Chars: </div>

                <table class="table table-bordered">
                    <thead>
                    <td><b>Name</b></td>
                    @can('ccia_action_edit')<td><b>Action</b></td>@endcan()
                    </thead>
                    <tbody>
                    @can('ccia_action_edit')
                    <tr>
                        <form action="{{route('ccia.actions.linkchar',['action_id'=>$action->id])}}" method="post">
                            <td><input type="text" class="form-control" id="char_id" name="char_id" placeholder="Character ID(numeric)"></td>
                            <td>
                                {{Form::token()}}
                                <button type="submit" class="btn btn-success">Add Character</button>
                            </td>
                        </form>
                    </tr>
                    @endcan()
                    @foreach($linked_chars as $char)
                        <tr>
                            <form action="{{route('ccia.actions.unlinkchar',['action_id'=>$action->id])}}" method="post">
                                <td><input class="form-control" type="text" disabled value="{{$char->name}}"></td>
                                @can('ccia_action_edit')
                                <td>
                                    <input type="hidden" name="char" value="{{$char->id}}">
                                    {{Form::token()}}
                                    <button type="submit" class="btn btn-danger">Remove Char</button>
                                </td>
                                @endcan()
                            </form>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Action Details</div>

                <table class="table table-bordered">
                    <thead>
                    <td><b>Name</b></td>
                    <td><b>Action</b></td>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Type</td>
                            <td>{{$action->type}}</td>
                        </tr>
                        <tr>
                            <td>Issued By</td>
                            <td>{{$action->issuedby}}</td>
                        </tr>
                        <tr>
                            <td>Url</td>
                            <td>{{$action->url}}</td>
                        </tr>
                        <tr>
                            <td>Expires At</td>
                            <td>{{$action->expires_at}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection