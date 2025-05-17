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

<li class="timeline-inverted">
    <div class="timeline-badge success"><i class="glyphicon glyphicon-ok"></i></div>
    <div class="timeline-panel">
        <div class="timeline-heading">
            <h4 class="timeline-title"><b>Completion Report: </b>{{$comment->title}}</h4>
            @include('syndie.comment.subtitle')
        </div>
        <div class="timeline-body">
            @if($comment->report_status == "waiting-approval")
                <div class="alert alert-info">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    This report is waiting to be approved by the contract author
                </div>
            @endif
            @if($comment->report_status == "accepted")
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    This report has been accepted by the contract author
                </div>
            @endif
            @if($comment->report_status == "rejected")
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    This report has been rejected by the contract author
                </div>
            @endif
            <p>@parsedown($comment->comment)</p>
            @if($comment->objectives()->count() > 0)
                <br><p><u>Completed objectives:</u></p>
                <ul>
                    @foreach($comment->objectives()->get() as $objective)
                        <li>{{$objective->title}}</li>
                    @endforeach
                </ul>
            @endif
            @if($comment->completers()->count() > 0)
                <br><p><u>Completing Agents:</u></p>
                <ul>
                    @foreach($comment->completers()->get() as $completer)
                        <li>{{$completer->ckey}}</li>
                    @endforeach
                </ul>
            @endif
            @include('syndie.comment.image')
            @if($contract->contractee_id == Auth::user()->id || Auth::user()->can('syndie_contract_moderate'))
                @if($comment->report_status == "waiting-approval")
                    <br><p>
                        <a href="{{route('syndie.comments.confirmopen',['comment'=>$comment->comment_id])}}" class="btn btn-success" role="button">Confirm Open</a>
                        <a href="{{route('syndie.comments.confirmclose',['comment'=>$comment->comment_id])}}" class="btn btn-warning" role="button">Confirm Close</a>
                        <a href="{{route('syndie.comments.reject',['comment'=>$comment->comment_id])}}" class="btn btn-danger" role="button">Reject</a>
                    </p>
                @endif
                @if(Auth::user()->can('syndie_contract_moderate'))<br><p><a href="{{route('syndie.comments.delete',['comment'=>$comment->comment_id])}}" class="btn btn-danger" role="button">Delete Comment</a></p>@endif
            @endif
        </div>
    </div>
</li>