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
                <div class="panel-heading">Add a Role: {{$role->label}}</div>

                <div class="panel-body">
                    {{Form::model($role, array('route' => array('site.roles.edit.post', $role->id),'method' => 'post')) }}
                    {{Form::token()}}

                    {{Form::bsText('name')}}
                    {{Form::bsText('label')}}
                    {{Form::bsText('description')}}

                    {{Form::submit('Submit', array('class'=>'btn btn-default'))}}

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Assigned Permissions</div>

                <div class="panel-body">
                    <table class="table table-bordered">
                        <thead>
                        <td>Name</td>
                        <td>Action</td>
                        </thead>
                        <tbody>
                        <tr>
                            <form action="{{route('site.roles.addperm',['role_id'=>$role->id])}}" method="post">
                                <td>
                                    <select class="form-control" name="permission">
                                        @foreach($avail_permissions as $perm_id => $perm_name)
                                            <option value="{{$perm_id}}">{{$perm_name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    {{Form::token()}}
                                    <button type="submit" class="btn btn-success">Add Permission</button>
                                </td>
                            </form>
                        </tr>
                        @foreach($role->permissions()->get() as $permission)
                            <tr>
                                <form action="{{route('site.roles.remperm',['role_id'=>$role->id])}}" method="post">
                                    <td><input class="form-control" type="text" disabled value="{{$permission->name}}"></td>
                                    <td>
                                        <input type="hidden" name="permission" value="{{$permission->id}}">
                                        {{Form::token()}}
                                        <button type="submit" class="btn btn-danger">Remove Permission</button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Assigned Users</div>

                <div class="panel-body">
                    <table class="table table-bordered">
                        <thead>
                        <td>Name</td>
                        <td>Action</td>
                        </thead>
                        <tbody>
                        <tr>
                            <form action="{{route('site.roles.adduser',['role_id'=>$role->id])}}" method="post">
                                <td><input type="text" class="form-control" id="user_id" name="user_id" placeholder="Forum user id (numeric)"></td>
                                <td>
                                    {{Form::token()}}
                                    <button type="submit" class="btn btn-success">Add User</button>
                                </td>
                            </form>
                        </tr>
                        @foreach($assigned_users as $user)
                            <tr>
                                <form action="{{route('site.roles.remuser',['role_id'=>$role->id])}}" method="post">
                                    <td><input class="form-control" type="text" disabled value="{{$user->username}}"></td>
                                    <td>
                                        <input type="hidden" name="user" value="{{$user->user_id}}">
                                        {{Form::token()}}
                                        <button type="submit" class="btn btn-danger">Remove User</button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
