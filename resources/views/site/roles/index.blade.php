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
                <div class="panel panel-default">
                    <div class="panel-heading">Roles</div>
                    <table class="table table-bordered">
                        <thead>
                        <td>ID</td>
                        <td>Label</td>
                        <td>Description</td>
                        <td>Actions</td>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td><a href="{{route('site.roles.edit.get',["role_id" => $role->id])}}">{{$role->label}}</a></td>
                                <td>{{$role->description}}</td>
                                <td>
                                    <a class="btn btn-info" href="{{route('site.roles.edit.get',["role_id" => $role->id])}}">Edit</a>
                                    <a class="btn btn-danger" href="{{route('site.roles.delete',["role_id" => $role->id])}}">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr><td><a class="btn btn-success" href="{{route('site.roles.add.get')}}">Add a Role</a></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
