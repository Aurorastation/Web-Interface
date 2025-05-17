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
                <div class="panel-heading"><h4><b>{{$document->name}}</b></h4></div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Title:</b></td>
                        <td>{{$document->title}}</td>
                    </tr>
                    <tr>
                        <td><b>Chance:</b></td>
                        <td>{{$document->chance}}</td>
                    </tr>
                    <tr>
                        <td><b>Tags:</b></td>
                        <td>{{$document->tags}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading">Read Document</div>

                <div class="panel-body">
                    {{ $document->content }}
                </div>
                @if($canedit)
                <div class="panel-footer">
                    <a href="{{route('server.documents.edit.get',['document'=>$document->id])}}" class="btn btn-info" role="button">Edit</a>
                    <a href="{{route('server.documents.delete',['document'=>$document->id])}}" class="btn btn-danger" role="button">Delete</a>
                </div>
                @endif()
            </div>
        </div>
    </div>
</div>
@endsection