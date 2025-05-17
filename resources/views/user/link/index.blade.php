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
                <div class="panel-heading">Link Byond Account</div>

                <div class="panel-body">
                    {{-- Check if Byond Account is linked already, If so, then only show this message --}}
                    @if(Auth::user()->byond_linked == True)
                        <div class="alert alert-success">
                            <strong>Linking completed!</strong><br>
                            You have already linked your byond account with your forum account.
                            You should have additional menu items available in the user menu.
                        </div>
                    @else
                        @if($linking_in_progress)
                            {{-- Check if a linking is in progress, If so, then show info panel --}}
                            <div class="alert alert-warning">
                                <strong>Linking in Progress!</strong><br>
                                You have already initiated a linking request with the following byond account: <i><b>{{$byond_key}}</b></i><br>
                                The next time you join the server, you should see a linking request.
                            </div>
                            <p><a href="{{route('user.link.cancel')}}" class="btn btn-danger" role="button">Cancel Linking Request</a></p>
                        @else
                            {{-- If not show the form --}}
                            <div class="alert alert-danger">
                                <strong>Byond Account not linked!</strong><br>
                                You have not linked you byond account to your forum account.<br>
                                To use more advanced features of the WebInterface, you have to link your byond account to your forum user account.
                            </div>
                            {{ Form::open(array('route' => 'user.link.add','method' => 'post')) }}
                            {{Form::token()}}
                            {{Form::bsText('Byond Username')}}
                            {{Form::submit('Submit', array('class'=>'btn btn-default'))}}
                            {{ Form::close() }}
                        @endif
                    @endif()
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
