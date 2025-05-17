{{--Copyright (c) 2018 "Arrow768"--}}

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
        @include('server.poll._polldata')
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Answers</div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>Option</th>
                            <th>Minimum</th>
                            <th>Average</th>
                            <th>Maximum</th>
                            <th>Votes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($question->options()->get() as $option)
                            <tr>
                                <td>{{$option->text}}</td>
                                <td>{{$option->descmin}} ({{$option->minval}}) - {{$option->votes()->min('rating')}}</td>
                                <td>{{$option->descmid}} - {{$option->votes()->avg('rating')}}</td>
                                <td>{{$option->descmax}} ({{$option->maxval}}) - {{$option->votes()->max('rating')}}</td>
                                <td>{{$option->votes()->count()}}</td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
