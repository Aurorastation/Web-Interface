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
                    <div class="panel-heading">Add a new CCIA General Notice</div>

                    <div class="panel-body">
                        {{Form::open(array('route' => 'ccia.generalnotice.add.post','method' => 'post')) }}
                        {{Form::token()}}

                        {{Form::bsText('title')}}
                        {{Form::bsTextArea('message')}}
                        {{Form::bsCheckbox('automatic')}}

                        @can('ccia_general_notice_edit'){{Form::submit('Submit', array('class'=>'btn btn-default'))}}@endcan()

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
