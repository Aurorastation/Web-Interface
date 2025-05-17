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
                    <div class="panel-heading">New Comment for Contract: {{$contract->title}}</div>


                    <div class="panel-body">
                        {{ Form::open(array('route' => array('syndie.comments.add.post',$contract->contract_id),'method' => 'post', 'files' => true)) }}

                        {{Form::token()}}

                        @if(Auth::user()->can('syndie_contract_moderate')){{-- Check if user is contract mod --}}
                        {{Form::bsSelectList('type',array('approve'=>'Approve the Contract','reject'=>'Reject the Contract','ic'=>'IC Comment','ic-failrep'=> 'IC Failure Report','ic-comprep'=>'IC Completion Report','ic-cancel'=>'IC Cancel Contract','ooc' => 'OOC Comment','mod-author'=>'MOD-Author PM','mod-ooc'=>'MOD-OOC Message'))}}
                        @elseif(Auth::user()->user_id == $contract->contractee_id ){{-- Check if user is contract owner --}}
                        {{Form::bsSelectList('type',array('ic'=>'IC Comment','ic-cancel'=>'IC Cancel Contract','ooc' => 'OOC Comment','mod-author'=>'MOD-Author PM'))}}
                        @else(){{-- Otherwise --}}
                        {{Form::bsSelectList('type',array('ic'=>'IC Comment','ic-failrep'=> 'IC Failure Report','ic-comprep'=>'IC Completion Report','ooc' => 'OOC Comment'))}}
                        @endif()

                        <div id="user">
                            {{--Only show the commentor name field of the user is not the owner of the contract or a mod--}}
                            {{--<div class="alert alert-success">--}}
                                {{--<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>--}}
                                {{--The username is forced to your forum username for the following message types: 'ooc','mod-author','mod-ooc'--}}
                            {{--</div>--}}
                            {{Form::bsText('commentor_name')}}
                        </div>

                        {{Form::bsText('title')}}

                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            You can use Markdown in the comment field
                        </div>
                        {{Form::bsTextArea('comment')}}

                        {{--<div id="image">--}}
                            {{--<div class="alert alert-success">--}}
                                {{--<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>--}}
                                {{--Uploading a image is optional. You can only upload one image at a time.<br>--}}
                                {{--It is recommended to provide a description for each image you upload and then post a contract report.--}}
                            {{--</div>--}}
                            {{--{{Form::bsFile('image')}}--}}
                        {{--</div>--}}

                        <div id="objectives-select" class="form-group" hidden>
                            <label for="objectives">Select completed objectives:</label><br>
                            <select class="form-control objectives" id="objectives" multiple="multiple" name="objectives[]" style="width: 100%">
                                @foreach($objectives as $objective)
                                    <option value="{{$objective->objective_id}}" @if(@in_array($objective->objective_id, old('objectives'))) selected="selected" @endif>{{$objective->title}}</option>
                                @endforeach()
                            </select>
                        </div>
                        <div id="agents-select" class="form-group" hidden>
                            <label for="agents">Select participating agent ckeys:</label><br>
                            <select class="form-control agents" id="agents" multiple="multiple" name="agents[]" style="width: 100%">
                                @if(Auth::user()->byond_linked == True)
                                    <option value="{{Auth::user()->getServerPlayerID()}}" selected="selected">{{Auth::user()->byond_key}}</option>
                                @endif
                            </select>
                        </div>

                        {{Form::submit('Submit', array('class'=>'btn btn-default'))}}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script type="text/javascript" src="{{asset('assets/js/select2.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            updateForm();
            $(".objectives").select2({
                placeholder: "Select objectives to link to completion report",
                allowClear: true,
                width: '100%'
            });
            $(".agents").select2({
                placeholder: "Select agent ckeys to link to completion report",
                allowClear: true,
                width: '100%',
                maximumSelectionLength: 5,
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route("syndie.api.get.agentlist") }}",
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function (data) {
//                        alert(JSON.stringify(data));
                        return {
                            results: $.map(data, function (item, value) {
//                                alert(JSON.stringify(item));
                                return {
                                    text: item,
                                    id: value
                                }
                            })
                        };
                    }
                }
            });
        });

        $("#type").change(function () {
            updateForm();
        });

        function updateForm(){
            $("#title").hide();
            $('label[for="title"]').hide();
            $("#comment").hide();
            $('label[for="comment"]').hide();
            $("#user").hide();
            $('label[for="user"]').hide();
            $("#image").hide();
            $('label[for="image"]').hide();
            $("#objectives-select").hide();
            $('label[for="objectives-select"]').hide();
            $("#agents-select").hide();
            $('label[for="agents-select"]').hide();

            var type = $( "#type option:selected" ).val();
            if(type == "ic")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();

                $("#user").show();
                $('label[for="user"]').show();

                $("#image").show();
                $('label[for="image"]').show();
            }
            else if(type == "ic-failrep")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();

                $("#user").show();
                $('label[for="user"]').show();

                $("#image").show();
                $('label[for="image"]').show();
            }
            else if(type == "ic-comprep")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();

                $("#user").show();
                $('label[for="user"]').show();

                $("#image").show();
                $('label[for="image"]').show();

                $("#objectives-select").show();
                $('label[for="objectives-select"]').show();

                $("#agents-select").show();
                $('label[for="agents-select"]').show();

            }
            else if(type == "ic-cancel")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();

                $("#user").show();
                $('label[for="user"]').show();

                $("#image").show();
                $('label[for="image"]').show();
            }
            else if(type == "ooc")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();
            }
            else if(type == "mod-author")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();
            }
            else if(type == "mod-ooc")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();
            }
            else if(type == "approve")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();
            }
            else if(type == "reject")
            {
                $("#title").show();
                $('label[for="title"]').show();

                $("#comment").show();
                $('label[for="comment"]').show();
            }
        }
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@endsection