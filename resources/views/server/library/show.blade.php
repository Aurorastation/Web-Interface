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
                <div class="panel-heading"><h4><b>{{$book->title}}</b></h4></div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Author:</b></td>
                        <td>{{$book->author}}</td>
                    </tr>
                    <tr>
                        <td><b>Category:</b></td>
                        <td>{{$book->category}}</td>
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
                <div class="panel-heading">Read Book</div>

                <div class="panel-body">
                    {!! $book->content !!}
                </div>
                @if($canedit)
                <div class="panel-footer">
                    <a href="{{route('server.library.edit.get',['book_id'=>$book->id])}}" class="btn btn-info" role="button">Edit</a>
                    @can("server_library_edit")<a href="{{route('server.library.delete',['book_id'=>$book->id])}}" class="btn btn-danger" role="button">Delete</a>@endcan()
                    Author Ckey: {{$book->uploader}}
                </div>
                @endif()
            </div>
        </div>
    </div>
</div>
@endsection