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
<div class="container" id="app">
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Ghost Options</div>
                <div class="panel-body">
                    <div><button v-on:click="getghosts" class="btn btn-default">Get Ghosts from Server</button></div>
                    <div class="form-group">
                        <label for="ghostselect">Select ghost:</label><br>
                        <select class="ghostselect" v-model="ghostselected">
                            <option v-for="ghost in ghosts">@{{ghost}}</option>
                        </select>
                    </div>
                    <div><button v-on:click="grantrespawn" class="btn btn-default">Grant Respawn to Ghost</button></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">Ghost Status</div>
                <div class="panel-body">
                    <p><strong>Ghost Status:</strong> @{{ghoststatus}}</p>
                    <p><strong>Selected Ghost:</strong> @{{ghostselected}}</p>
                    <p><strong>Ghost List:</strong></p>
                    <ul>
                        <li v-for="ghost in ghosts">@{{ ghost }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{--<pre>--}}
{{--@{{ $data | json }}--}}
    {{--</pre>--}}
</div>
@endsection

@section('javascripts')
    <script src="{{asset('assets/js/vue_1_0_26.js')}}"></script>
    <script src="{{asset('assets/js/vue-resource_0_9_3.js')}}"></script>

    <script src="{{asset('assets/pages/server-live-ghosts.js')}}"></script>
@endsection
