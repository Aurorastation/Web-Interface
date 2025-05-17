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
        @include('components.formerrors')
        @if($char->active_ccia_action_count() > 0)
        <div class="alert alert-warning">
            <b>The following CCIA Actions have been linked to this character:</b><hr>
            <ul>
                @foreach ($char->cciaactions()->where(function($query) {$query->where("expires_at", ">=", DB::raw("NOW()"))->orWhereNull('expires_at');})->get() as $cciaaction)
                    <li><a href="{{route('ccia.actions.show.get',$cciaaction->id)}}">{{$cciaaction->title}}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><b>{{$char->name}}</b></h4></div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Species:</b></td>
                        <td>{{$char->species}}</td>
                    </tr>
                    <tr>
                        <td><b>Gender:</b></td>
                        <td>{{$char->gender}}</td>
                    </tr>
                    <tr>
                        <td><b>Age:</b></td>
                        <td>{{$char->age}}</td>
                    </tr>
                    <tr>
                        <td><b>Blood Type:</b></td>
                        <td>{{$char->b_type}}</td>
                    </tr>
                    @can('server_chars_show')
                        <tr>
                            <td><b>Owner ckey:</b></td>

                            <td>
                                @can('server_players_show')<a href="{{route('server.players.ckey',["player_ckey"=>$char->ckey])}}">@endcan()
                                    {{$char->ckey}}
                                @can('server_players_show')</a>@endcan()
                            </td>
                        </tr>
                    @endcan()
                    </tbody>
                </table>
            </div>
        </div>
        @can('server_chars_edit')
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Change Char Name</div>

                    <table class="table">
                        {{Form::open(array('route' => array('server.chars.edit.name.post', $char_flavour->char_id),'method' => 'post')) }}
                        {{Form::token()}}

                        <tbody>
                        <tr>
                            <td><b>Current Name:</b></td>
                            <td>{{$char->name}}</td>
                        </tr>
                        <tr>
                            <td><b>New Name:</b></td>
                            <td>{{ Form::text("name", $char->name, ['class' => 'form-control']) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{Form::submit('Submit', array('class'=>'btn btn-default'))}}</td>
                        </tr>
                        </tbody>
                        {{Form::close()}}
                    </table>
                </div>
            </div>
        @endcan()
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Underwear / Undershirt / Backpack</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Underwear:</b></td>
                        <td>{{$char->underwear}}</td>
                    </tr>
                    <tr>
                        <td><b>Undershirt:</b></td>
                        <td>{{$char->undershirt}}</td>
                    </tr>
                    <tr>
                        <td><b>Backbag:</b></td>
                        <td>{{$char->backbag}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Personal Data</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Home System:</b></td>
                        <td>{{$char->home_system}}</td>
                    </tr>
                    <tr>
                        <td><b>Citizenship:</b></td>
                        <td>{{$char->citizenship}}</td>
                    </tr>
                    <tr>
                        <td><b>Faction:</b></td>
                        <td>{{$char->faction}}</td>
                    </tr>
                    <tr>
                        <td><b>Religion:</b></td>
                        <td>{{$char->religion}}</td>
                    </tr>
                    <tr>
                        <td><b>Economic Status:</b></td>
                        <td>{{$char->economic_status}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Hair Colour/Style</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Hair Colour:</b></td>
                        <td bgcolor="{{$char->hair_colour}}"></td>
                    </tr>
                    <tr>
                        <td><b>Hair Style:</b></td>
                        <td>{{$char->hair_style}}</td>
                    </tr>
                    <tr>
                        <td><b>Facial Hair Colour:</b></td>
                        <td bgcolor="{{$char->facial_colour}}"></td>
                    </tr>
                    <tr>
                        <td><b>Facial Hair Style:</b></td>
                        <td>{{$char->facial_style}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Skin/Eye Color</div>

                <table class="table">
                    <tbody>
                    @if($char->species == "Human")
                        <tr>
                            <td><b>Skin Tone:</b></td>
                            <td>{{$char->skin_tone}}</td>
                        </tr>
                    @else()
                        <tr>
                            <td><b>Skin Colour:</b></td>
                            <td bgcolor="{{$char->skin_colour}}"></td>
                        </tr>
                    @endif()
                    <tr>
                        <td><b>Eye Colour:</b></td>
                        <td bgcolor="{{$char->eyes_colour}}"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="cold-md-12">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#recemploy">Employment</a></li>
                <li><a data-toggle="tab" href="#recmed">Medical</a></li>
                <li><a data-toggle="tab" href="#recsec">Security</a></li>
                <li><a data-toggle="tab" href="#recccia">CCIA</a></li>
                <li><a data-toggle="tab" href="#recexploit">Exploit</a></li>
            </ul>

            <div class="tab-content">
                <div id="recemploy" class="tab-pane fade in active">
                    <div class="panel panel-default">
                        <div class="panel-heading">Employment Records</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('records_employment',$char_flavour->records_employment)}}
                                {{Form::hidden('type','records_employment')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('records_employment',$char_flavour->records_employment,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="recmed" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Medical Records</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('records_medical',$char_flavour->records_medical)}}
                                {{Form::hidden('type','records_medical')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('records_medical',$char_flavour->records_medical,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="recsec" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Security Records</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('records_security',$char_flavour->records_security)}}
                                {{Form::hidden('type','records_security')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('records_security',$char_flavour->records_security,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="recccia" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">CCIA Records</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @can('server_chars_edit')
                                {{Form::bsTextArea('records_ccia',$char_flavour->records_ccia)}}
                                {{Form::hidden('type','records_ccia')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('records_ccia',$char_flavour->records_ccia,["disabled"])}}
                            @endcan
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="recexploit" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Exploitable Information</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('records_exploit',$char_flavour->records_exploit)}}
                                {{Form::hidden('type','records_exploit')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('records_exploit',$char_flavour->records_exploit,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="cold-md-12">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#flavgen">General</a></li>
                <li><a data-toggle="tab" href="#flavhead">Head</a></li>
                <li><a data-toggle="tab" href="#flavface">Face</a></li>
                <li><a data-toggle="tab" href="#flaveyes">Eyes</a></li>
                <li><a data-toggle="tab" href="#flavtorso">Torso</a></li>
                <li><a data-toggle="tab" href="#flavarms">Arms</a></li>
                <li><a data-toggle="tab" href="#flavhands">Hands</a></li>
                <li><a data-toggle="tab" href="#flavlegs">Legs</a></li>
                <li><a data-toggle="tab" href="#flavfeet">Feet</a></li>
            </ul>
            <div class="tab-content">
                <div id="flavgen" class="tab-pane fade in active">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - General</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_general',$char_flavour->flavour_general)}}
                                {{Form::hidden('type','flavour_general')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_general',$char_flavour->flavour_general,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavhead" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Head</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_head',$char_flavour->flavour_head)}}
                                {{Form::hidden('type','flavour_head')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_head',$char_flavour->flavour_head,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavface" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Face</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_face',$char_flavour->flavour_face)}}
                                {{Form::hidden('type','flavour_face')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_face',$char_flavour->flavour_face,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flaveyes" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Eyes</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_eyes',$char_flavour->flavour_eyes)}}
                                {{Form::hidden('type','flavour_eyes')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_eyes',$char_flavour->flavour_eyes,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavtorso" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Torso</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_torso',$char_flavour->flavour_torso)}}
                                {{Form::hidden('type','flavour_torso')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_torso',$char_flavour->flavour_torso,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavarms" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Arms</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_arms',$char_flavour->flavour_arms)}}
                                {{Form::hidden('type','flavour_arms')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_arms',$char_flavour->flavour_arms,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavhands" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Hands</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_hands',$char_flavour->flavour_hands)}}
                                {{Form::hidden('type','flavour_hands')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_hands',$char_flavour->flavour_hands,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavlegs" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Legs</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_legs',$char_flavour->flavour_legs)}}
                                {{Form::hidden('type','flavour_legs')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_legs',$char_flavour->flavour_legs,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                <div id="flavfeet" class="tab-pane fade in">
                    <div class="panel panel-default">
                        <div class="panel-heading">Flavourtext - Feet</div>
                        <div class="panel-body">
                            {{Form::open(array('route' => array('server.chars.edit.text.post', $char_flavour->char_id),'method' => 'post')) }}
                            {{Form::token()}}
                            @if( $can_edit )
                                {{Form::bsTextArea('flavour_feet',$char_flavour->flavour_feet)}}
                                {{Form::hidden('type','flavour_feet')}}
                                {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            @else()
                                {{Form::bsTextArea('flavour_feet',$char_flavour->flavour_feet,["disabled"])}}
                            @endif()
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Incident Overview</div>

                <div class="panel-body">
                    <table id="incidents-table" class="table table-condensed">
                        <thead>
                        <tr>
                            <th width="20px">ID</th>
                            <th width="20px">DateTime</th>
                            <th>Notes</th>
                            <th width="50px">Brig Sentence</th>
                            <th width="50px">Fine</th>
                            <th width="20px">Status</th>
                            <th width="20px">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @can('server_chars_edit')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Character Log</div>

                    <div class="panel-body">
                        <table id="character-log-table" class="table table-condensed">
                            <thead>
                            <tr>
                                <th width="20px">Game ID</th>
                                <th width="20px">DateTime</th>
                                <th width="50px">Job Name</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan()
</div>
@endsection

@section('javascripts')
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(function() {
            $('#incidents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('server.incidents.data.char',["char_id" => $char->id]) }}',
                columns:[
                    { data: 'id', name: 'id' },
                    { data: 'datetime', name: 'datetime' },
                    { data: 'notes', name: 'notes' },
                    { data: 'brig_sentence', name: 'brig_sentence' },
                    { data: 'fine', name: 'fine' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' }
                ]
            });
            @can('server_chars_show')
                $('#character-log-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('server.chars.data.log',["char_id" => $char->id]) }}',
                columns:[
                    { data: 'game_id', name: 'game_id' },
                    { data: 'datetime', name: 'datetime' },
                    { data: 'job_name', name: 'job_name' }
                ]
            });
            @endcan()
        });
    </script>
@endsection
