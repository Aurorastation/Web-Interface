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

<table class="table table-bordered">
    <tbody>
    @foreach($whitelists as $whitelist)
        <tr>
            @if($whitelist->subspecies == 0)
                <td>{{$whitelist->status_name}}</td>
                @if($whitelist->active == 1)
                    <td><span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span></td>
                @else
                    <td><span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span></td>
                @endif
            @endif()
        </tr>
    @endforeach
    </tbody>
</table>