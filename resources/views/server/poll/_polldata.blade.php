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

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">Poll {{$question->id}} Data</div>

            <table class="table">
                <tbody>
                <tr>
                    <td><b>Type:</b></td>
                    <td>{{$question->polltype}}</td>
                </tr>
                @if($question->multiplechoiceoptions != 0)
                    <tr>
                        <td><b>Vote-Count:</b></td>
                        <td>{{$question->multiplechoiceoptions}}</td>
                    </tr>
                @endif()
                <tr>
                    <td><b>Question:</b></td>
                    <td>{{$question->question}}</td>
                </tr>
                <tr>
                    <td><b>Starttime:</b></td>
                    <td>{{$question->starttime}}</td>
                </tr>
                <tr>
                    <td><b>Endtime:</b></td>
                    <td>{{$question->endtime}}</td>
                </tr>
                <tr>
                    <td><b>Admin Only:</b></td>
                    <td>@if($question->adminonly) True @else False @endif()</td>
                </tr>
                <tr>
                    <td><b>Public Result:</b></td>
                    <td>@if($question->publicresult) True @else False @endif()</td>
                </tr>
                <tr>
                    <td><b>Info Link:</b></td>
                    <td><a href="{{$question->link}}">{{$question->link}}</a></td>
                </tr>
                <tr>
                    <td><b>Created By:</b></td>
                    <td>{{$question->createdby_ckey}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>