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

@if($comment->commentor_id == $contract->contractee_id)<li> @else <li class="timeline-inverted">@endif
    <div class="timeline-badge success"><i class="glyphicon glyphicon-envelope"></i></div>
    <div class="timeline-panel">
        <div class="timeline-heading">
            <h4 class="timeline-title"><b>Message: </b>{{$comment->title}}</h4>
            @include('syndie.comment.subtitle')
        </div>
        <div class="timeline-body">
            <p>@parsedown($comment->comment)</p>
            @include('syndie.comment.image')
            @if(Auth::user()->can('syndie_contract_moderate'))<br><p><a href="{{route('syndie.comments.delete',['comment'=>$comment->comment_id])}}" class="btn btn-danger" role="button">Delete Comment</a></p>@endif
        </div>
    </div>
</li>