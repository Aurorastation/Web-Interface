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
                <div class="panel-heading">Edit a CCIA Report</div>

                <div class="panel-body">

                    {{Form::model($report, array('route' => array('ccia.report.edit.post', $report->id),'method' => 'post')) }}
                    {{Form::token()}}

                    {{Form::bsText('title')}}
                    {{Form::bsDate('report_date')}}
                    {{Form::bsText('public_topic')}}
                    {{Form::bsText('internal_topic')}}
                    {{Form::bsText('game_id')}}
                    {{Form::bsSelectList('status',array('new'=>'New','approved'=> 'Approved','rejected'=>'Rejected','completed' => 'Completed'))}}

                    @can('ccia_report_edit'){{Form::submit('Submit', array('class'=>'btn btn-default'))}}@endcan()


                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection