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

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @include('components.formerrors')
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><b>{{$item->name}}</b></h4></div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Supplier:</b></td>
                        <td>{{$item->supplier}}</td>
                    </tr>
                    <tr>
                        <td><b>Categories:</b></td>
                        <td>{{$item->categories}}</td>
                    </tr>
                    <tr>
                        <td><b>Order By:</b></td>
                        <td>{{$item->order_by}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Packaging Data</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Container Type:</b></td>
                        <td>{{$item->container_type}}</td>
                    </tr>
                    <tr>
                        <td><b>Access:</b></td>
                        <td>{{$item->access}}</td>
                    </tr>
                    <tr>
                        <td><b>Groupable:</b></td>
                        <td>@if($item->groupable == 1) Yes @else No @endif()</td>
                    </tr>
                    <tr>
                        <td><b>Item Mul:</b></td>
                        <td>{{$item->item_mul}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Description</div>
                <div class="panel-body">{{$item->description}}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Item Data</div>
                <div class="panel-body">{{json_encode(json_decode($item->items),JSON_PRETTY_PRINT)}}</div>
            </div>
        </div>
    </div>

</div>
@endsection
