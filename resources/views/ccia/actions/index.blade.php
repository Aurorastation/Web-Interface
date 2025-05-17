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
    <link href="{{ asset('/DataTables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#active">Active Actions</a></li>
                    @can('ccia_action_show')<li><a data-toggle="tab" href="#all">All Actions</a></li>@endcan()
                </ul>
                <div class="tab-content">
                    <div id="active" class="tab-pane fade in active">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @can('ccia_action_edit')<p><a href="{{route('ccia.actions.add.post')}}" class="btn btn-success" role="button">Add new Action</a></p>@endcan()
                                <table id="active-actions-table" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th width="30">ID</th>
                                        <th>Title</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    @can('ccia_action_show')
                    <div id="all" class="tab-pane fade in">
                        <div class="tab-pane fade in ">
                            <div class="panel">
                                <div class="panel-body">
                                    @can('ccia_action_edit')<p><a href="{{route('ccia.actions.add.post')}}" class="btn btn-success" role="button">Add new Action</a></p>@endcan()
                                    <table id="all-actions-table" class="table table-condensed" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="30">ID</th>
                                            <th>Title</th>
                                            <th>Expires At</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan()
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')

    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(function() {
            $('#active-actions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('ccia.actions.data.active') }}',
                columns:[
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action' }
                ]
            });
            @can('ccia_action_show')
            $('#all-actions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('ccia.actions.data.all') }}',
                columns:[
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'expires_at', name: 'expires_at' },
                    { data: 'action', name: 'action' }
                ]
            });
            @endcan()
        });
    </script>
@endsection
