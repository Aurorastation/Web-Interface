{{--Copyright (c) 2016 "Werner Maisl"--}}

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
        @if(isset($game_details->blackbox_destroyed))
            <div class="alert alert-danger">
                The Blackbox has been destroyed.
            </div>
        @endif
        <div class="col-md-10 col-md-offset-1">
            @include('components.formerrors')
            <div class="panel panel-default">
                <div class="panel-heading">Round Details</div>

                <table class="table table-bordered">
                    <tbody>
                    @foreach($game_details as $detail)
                        <tr>
                            <td>{{$detail->var_name}}</td>
                            <td>{{$detail->details}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
