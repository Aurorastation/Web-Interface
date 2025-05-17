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
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Report Details</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Title:</b></td>
                        <td>{{$report->title}}</td>
                    </tr>
                    <tr>
                        <td><b>Report Date:</b></td>
                        <td>{{$report->report_date}}</td>
                    </tr>
                    <tr>
                        <td><b>Status:</b></td>
                        <td>{{$report->status}}</td>
                    </tr>
                    <tr>
                        <td><b>Game ID:</b></td>
                        @if(isset($report->game_id))
                            @can('server_logs_show')
                                <td><a href="{{route('server.log.show.getbygame',['game_id'=>$report->game_id])}}" class="btn btn-success" role="button">{{$report->game_id}} - Download Log</a></td>
                            @else()
                                <td>{{$report->game_id}}</td>
                            @endcan()
                        @else
                            <td>Not Set</td>
                        @endif()
                    </tr>
                    @if(isset($report->public_topic) || isset($report->internal_topic))
                    <tr>
                        <td colspan="2">
                            <div class="btn-group">
                                @if(isset($report->public_topic))
                                    <a href="{{$report->public_topic}}" target="_blank" class="btn btn-success" role="button">Public Topic</a>
                                @endif
                                @if(isset($report->internal_topic))
                                    <a href="{{$report->internal_topic}}" target="_blank" class="btn btn-warning" role="button">Internal Topic</a>
                                @endif()
                            </div>
                        </td>
                    </tr>
                    @endif()
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Report Transcripts</div>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Transcript ID</th>
                        <th>Character Name</th>
                        <th>Interviewer</th>
                        <th>Claimed Antag Involvement</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($transcripts as $transcript)
                    <tr>
                        <td>{{$transcript->id}}</td>
                        @if($transcript->character)
                            <td><a href="{{route('server.chars.show.get',['char_id'=>$transcript->character->id])}}">{{$transcript->character->name}}</a></td>
                        @else()
                            <td>Error - Unable to determine character name</td>
                        @endif()
                        <td>{{$transcript->interviewer}}</td>
                        <td>
                            @if($transcript->antag_involvement === 0)
                                No
                            @else
                                <a href="{{route('ccia.report.claim.get',['transcript_id'=>$transcript->id])}}" class="btn btn-success" role="button">Yes - View Claim</a>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('ccia.report.transcript.get',['transcript_id'=>$transcript->id])}}" class="btn btn-success" role="button">View Interview</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Antag ID</th>
                    <th>Ckey</th>
                    <th>Character Name (Actual)</th>
                    <th>Character Name (Assumed)</th>
                    <th>Antag Type</th>
                    <th>Added At</th>
                </tr>
                </thead>

                <tbody>
                @if(count($report->antagonists) > 0)
                    @foreach($report->antagonists as $antag)
                        <tr>
                            <td>{{$antag->id}}</td>
                            <td>{{$antag->ckey}}</td>
                            @if($antag->char_name)
                                <td>{{$antag->char_name}}</td>
                            @else
                                <td>- Unavailable -</td>
                            @endif()
                            @if($antag->character)
                                <td>{{$antag->character->name}}</td>
                            @else
                                <td>- Unavailable -</td>
                            @endif()
                            <td>{{$antag->special_role_name}}</td>
                            <td>{{$antag->special_role_added}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No Antags Found - Either there were no antags or the game id is invalid</td>
                    </tr>
                @endif()
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection