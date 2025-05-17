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

@if($contract->status == "new")
    <span class="label label-default">New</span>
@elseif($contract->status == "open")
    <span class="label label-primary">Open</span>
@elseif($contract->status == "mod-nok")
    <span class="label label-danger">Mod Rejected</span>
@elseif($contract->status == "completd")
    <span class="label label-info">Completed</span>
@elseif($contract->status == "closed")
    <span class="label label-success">Finished</span>
@elseif($contract->status == "canceled")
    <span class="label label-warning">Canceled by Contractee</span>
@else
    <span class="label label-default">{{$contract->status}}</span>
@endif