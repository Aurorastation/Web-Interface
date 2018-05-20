{{--Copyright (c) 2018 "Werner Maisl"--}}

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
                <div class="panel-heading">Edit a news Story</div>

                <div class="panel-body">

                    {{Form::model($news, array('route' => array('server.news.edit.post', $news->id),'method' => 'post')) }}
                    {{Form::token()}}

                    {{Form::bsText('author')}}
                    {{Form::bsText('timestamp')}}
                    {{Form::bsText('message_type')}}
                    {{Form::bsSelectList('channel_id',$channels)}}
                    {{Form::bsTextArea('body')}}

                    {{Form::submit('Submit', array('class'=>'btn btn-default'))}}

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection