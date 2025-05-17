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

@section('styles')
    <link href="{{asset('assets/css/timeline.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/ekko-lightbox.min.css')}}" rel="stylesheet">
@endsection

@section('javascripts')
    <script src="{{asset('assets/js/ekko-lightbox.min.js')}}"></script>
    <script>
        $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
    </script>
@endsection()


@section('content')
    <div class="container">
        @include('components.formerrors')

        {{--Errors and Warnings--}}
        @if($contract->status == 'new' )
            <div class="alert alert-warning">
                <strong>New Contract: </strong> This contract has not been approved by a contract mod. It is only visible to you and moderators
            </div>
        @endif
        @if($contract->status == 'mod-nok' )
            <div class="alert alert-danger">
                <strong>Rejected by Moderator: </strong> This contract has been rejected by a moderator. Check the comment why this happend and then improve the contract.
            </div>
        @endif
        @if($contract->status == 'completed')
            <div class="alert alert-success">
                <strong>Contract Completed: </strong> This contract has been marked as completed by a contractor. As Author, please confirm the completion or reopen the contract
            </div>
        @endif

        {{-- Contract Overview --}}
        <div class="row">
             {{--Details about the contract--}}
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4><b>{{$contract->title}}</b></h4></div>

                    <table class="table">
                        <tbody>
                        <tr>
                            <td><b>Contractee:</b></td>
                            <td>{{$contract->contractee_name}}</td>
                        </tr>
                        <tr>
                            <td><b>Status:</b></td>
                            <td>@include("syndie.components.contractstatus")</td>
                        </tr>
                        <tr>
                            <td><b>Base Reward:</b></td>
                            <td>{{$contract->reward_other}}</td>
                        </tr>
                        @if((Auth::user()->user_id == $contract->contractee_id && $contract->status == "new") || Auth::user()->can('syndie_contract_moderate') )
                            <tr>
                                <td><a href="{{route('syndie.contracts.edit.get',['contract'=>$contract->contract_id])}}" class="btn btn-warning" role="button">Edit the Contract</a></td>
                                <td></td>
                            </tr>
                        @endif
                        <tr>
                            @if(!$contract->is_subscribed(Auth::user()->user_id))
                                <td><a href="{{route('syndie.contracts.subscribe',['contract'=>$contract->contract_id])}}" class="btn btn-success" role="button">Subscribe</a></td>
                            @else()
                                <td><a href="{{route('syndie.contracts.unsubscribe',['contract'=>$contract->contract_id])}}" class="btn btn-info" role="button">Unsubscribe</a></td>
                            @endif()
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(Auth::user()->cannot('syndie_contract_moderate'))<div class="col-md-8">@else() <div class="col-md-6"> @endif()
                    <div class="panel panel-default">
                        <div class="panel-heading">Contract Description:</div>

                        <div class="panel-body">
                            <p>@parsedown($contract->description)</p>
                        </div>
                    </div>
                </div>
                 {{--Management Panel--}}
                @can('syndie_contract_moderate')
                    <div class="col-md-2">
                        <div class="panel panel-default">
                            <div class="panel-heading">Actions</div>
                            <div class="panel-body">
                                <p><b>Contract Mod</b></p>
                                {{--<p><a href="{{route('syndie.contracts.approve',['contract'=>$contract->contract_id])}}" class="btn btn-success @if(!in_array($contract->status,['new','mod-nok'])) disabled @endif" role="button">Approve</a></p>--}}
                                {{--<p><a href="{{route('syndie.contracts.reject',['contract'=>$contract->contract_id])}}" class="btn btn-warning" role="button">Reject </a></p>--}}
                                <p><a href="{{route('syndie.contracts.deletecontract',['contract'=>$contract->contract_id])}}" class="btn btn-danger @if($contract->status != 'mod-nok') disabled @endif" role="button">Delete</a></p>
                                <p><b>Created by:</b></p>
                                <p>@if(isset($contractee) && $contractee->byond_key != ""){{$contractee->byond_key}} @else {{$contract->contractee_id}}@endif()</p>
                            </div>
                        </div>
                    </div>
                @endcan()
            </div>
        </div>

        {{-- Contract Objectives / Comments --}}

        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="panel panel-default">
                    <div class="btn-group pull-right">
                        <a href="{{route('syndie.objectives.add.get',['contract'=>$contract->contract_id])}}" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add Objective</a>
                    </div>
                    <div class="panel-heading">Contract Objectives</div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Reward</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($objectives as $objective)
                                    @if($objective->status == "open")
                                        <tr class="success">
                                    @elseif($objective->status == "closed")
                                        <tr class="warning">
                                    @else()
                                        <tr>
                                            @endif()
                                            <td>{{$objective->title}}</td>
                                            <td>{{$objective->status}}</td>
                                            <td>{{$objective->reward_other}}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a class="btn btn-info " href="{{route('syndie.objectives.show',['objective'=>$objective->objective_id])}}">
                                                        View
                                                        {{--<span class="glyphicon glyphicon-question-sign"></span>--}}
                                                    </a>

                                                    @if($contract->contractee_id == Auth::user()->id || Auth::user()->can('syndie_contract_moderate'))
                                                        <a class="btn btn-warning" href="{{route('syndie.objectives.edit.get',['objective'=>$objective->objective_id])}}">
                                                            Edit
                                                            {{--<span class="glyphicon glyphicon-pencil"></span>--}}
                                                        </a>
                                                        @if($objective->status == "closed")
                                                            <a class="btn btn-success" href="{{route('syndie.objectives.open',['objective'=>$objective->objective_id])}}">
                                                                Open
                                                                {{--<span class="glyphicon glyphicon-ok"></span>--}}
                                                            </a>
                                                        @endif()
                                                        @if($objective->status == "open")
                                                            <a class="btn btn-warning" href="{{route('syndie.objectives.close',['objective'=>$objective->objective_id])}}">
                                                                Close
                                                                {{--<span class="glyphicon glyphicon-remove"></span>--}}
                                                            </a>
                                                        @endif()
                                                        <a class="btn btn-danger" href="{{route('syndie.objectives.delete',['objective'=>$objective->objective_id])}}">
                                                            Delete
                                                            {{--<span class="glyphicon glyphicon-trash"></span>--}}
                                                        </a>
                                                    @endif()
                                                </div>
                                            </td>
                                        </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message Timeline--}}
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="page-header">
                    <h1 id="timeline">Comments</h1>
                </div>
                <div class="btn-group pull-right">
                    <a href="{{route('syndie.comments.add.get',['contract'=>$contract->contract_id])}}" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add Message</a>
                </div>
                <br>
                <br>
                <ul class="timeline">
                    @foreach($comments as $comment)
                        @if($comment->type === 'mod-ooc')
                            @include('syndie.comment.mod-ooc')
                        @elseif($comment->type === 'mod-author')
                            @include('syndie.comment.mod-author')
                        @elseif($comment->type === 'ic')
                            @include('syndie.comment.ic')
                        @elseif($comment->type === 'ic-comprep')
                            @include('syndie.comment.ic-comprep')
                        @elseif($comment->type === 'ic-failrep')
                            @include('syndie.comment.ic-failrep')
                        @elseif($comment->type === 'ic-cancel')
                            @include('syndie.comment.ic-cancel')
                        @elseif($comment->type === 'ooc')
                            @include('syndie.comment.ooc')
                        @endif()
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection