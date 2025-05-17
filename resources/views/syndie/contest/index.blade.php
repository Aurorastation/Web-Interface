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
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Chars per Faction</div>

                <table class="table">
                    <tbody>
                    @foreach($chars_per_faction as $factionchars)
                    <tr>
                        <td>{{$factionchars['contest_faction']}}</td>
                        <td>{{$factionchars['char_count']}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($chars_per_faction)}}</pre>--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Faction - Successful</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_faction_success as $missionsuccess)
                        <tr>
                            <td>{{$missionsuccess['character_faction']}}</td>
                            <td>{{$missionsuccess['faction_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_faction_success)}}</pre>--}}
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Faction - Failed</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_faction_fail as $missionfail)
                        <tr>
                            <td>{{$missionfail['character_faction']}}</td>
                            <td>{{$missionfail['faction_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_faction_fail)}}</pre>--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Type - Successful</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_type_success as $missionsuccess)
                        <tr>
                            <td>{{$missionsuccess['objective_type']}}</td>
                            <td>{{$missionsuccess['type_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_type_success)}}</pre>--}}
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Type - Failed</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_type_fail as $missionfail)
                        <tr>
                            <td>{{$missionfail['objective_type']}}</td>
                            <td>{{$missionfail['type_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_type_fail)}}</pre>--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Side - Successful</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_side_success as $missionsuccess)
                        <tr>
                            <td>{{$missionsuccess['objective_side']}}</td>
                            <td>{{$missionsuccess['side_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_side_success)}}</pre>--}}
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">Missions per Side - Failed</div>

                <table class="table">
                    <tbody>
                    @foreach($missions_per_side_fail as $missionfail)
                        <tr>
                            <td>{{$missionfail['objective_side']}}</td>
                            <td>{{$missionfail['side_missions']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<pre>{{var_dump($missions_per_side_fail)}}</pre>--}}
            </div>
        </div>
    </div>
</div>
@endsection
