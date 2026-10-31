@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if(isset($comment->deleted_at))
            <div class="alert alert-danger">
                <b>This record comment was deleted by {{$comment->deleted_by}} and is only displayed to staff members.</b>
            </div>
        @endif

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Record Comment: </b>{{$comment->UID}}</div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Record Type:</b></td>
                        <td>{{ucfirst($comment->record_type)}}</td>
                    </tr>
                    <tr>
                        <td><b>Author:</b></td>
                        <td>{{$comment->author}}</td>
                    </tr>
                    <tr>
                        <td><b>Comment:</b></td>
                        <td>{!! nl2br(e($comment->body)) !!}</td>
                    </tr>
                    <tr>
                        <td><b>Created At:</b></td>
                        <td>{{$comment->created_at}}</td>
                    </tr>
                    @if($comment->updated_at && $comment->updated_at != $comment->created_at)
                        <tr>
                            <td><b>Updated At:</b></td>
                            <td>{{$comment->updated_at}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td><b>Game ID:</b></td>
                        <td>{{$comment->game_id}}</td>
                    </tr>
                    @if($can_edit)
                        <tr>
                            <td><b>Actions:</b></td>
                            @if(!isset($comment->deleted_at))
                                <td><a href="{{route('server.record-comments.delete.get', ['comment_id' => $comment->id])}}" class="btn btn-danger" role="button">Delete</a></td>
                            @else
                                <td><a href="{{route('server.record-comments.restore.get', ['comment_id' => $comment->id])}}" class="btn btn-success @cannot('server_players_incidents_edit') disabled @endcannot" role="button">Restore</a></td>
                            @endif
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
