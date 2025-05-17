{{--Copyright (c) 2017 "Arrow768"--}}

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
                @if($submitted)
                <div class="alert alert-success">
                    Extermination Request <a href="https://www.youtube.com/watch?v=h67JpMyrOVE" target="_blank">Submitted</a>.
                </div>
                @endif()

                <div class="panel panel-default">
                    <div class="panel-heading">Exterminatus Control Panel</div>

                    <div class="panel-body">
                        {{Form::open(array('route' => 'server.exterminatus.index','method' => 'post')) }}
                        {{Form::token()}}

                        {{Form::bsText('Name')}}
                        {{Form::bsSelectList('Race',array(
                            'Tajara'=>'Tajara',
                            'Unathi'=>'Unathi',
                            'Skrell'=>'Skrell',
                            'Bugs'=>'Bugs',
                            'Trees'=>'Trees',
                            'Mice'=>'Mice'))}}
                        {{Form::bsText('Reason')}}

                        {{Form::submit('Exterminate now', array('class'=>'btn btn-danger disabled'))}}
                        {{Form::submit('Submit Extermination Request', array('class'=>'btn btn-warning'))}}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection