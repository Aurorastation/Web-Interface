<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\ServerRecordComment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Datatables;

class RecordCommentController extends Controller
{
    public function getShow($comment_id, Request $request)
    {
        if ($request->user()->can('server_players_incidents_show')) {
            $comment = ServerRecordComment::withTrashed()->findOrFail($comment_id);
        } else {
            $comment = ServerRecordComment::findOrFail($comment_id);
        }

        if (!$this->canSee($comment->char_id, $request)) {
            abort(403, 'You do not have the required permission to view this record comment.');
        }

        return view('server.record_comments.show', [
            'comment' => $comment,
            'can_edit' => $this->canEdit($comment->char_id, $request),
        ]);
    }

    public function getDelete($comment_id, Request $request)
    {
        $comment = ServerRecordComment::findOrFail($comment_id);

        if (!$this->canEdit($comment->char_id, $request)) {
            abort(403, 'You do not have the required permission to delete this record comment.');
        }

        $comment->deleted_by = $request->user()->byond_key;
        $comment->save();
        $comment->delete();

        Log::notice('perm.record_comments.delete - Record comment has been deleted', [
            'user_id' => $request->user()->user_id,
            'comment_id' => $comment->id,
        ]);

        if ($request->user()->can('server_players_incidents_show')) {
            return redirect()->route('server.record-comments.show.get', ['comment_id' => $comment->id]);
        }

        return redirect()->route('server.chars.show.get', ['char_id' => $comment->char_id]);
    }

    public function getRestore($comment_id, Request $request)
    {
        $comment = ServerRecordComment::withTrashed()->findOrFail($comment_id);

        if ($request->user()->cannot('server_players_incidents_edit')) {
            abort(403, 'You do not have the required permission to restore this record comment.');
        }

        $char_id = $comment->char_id;

        $comment->deleted_by = null;
        $comment->restore();

        Log::notice('perm.record_comments.restore - Record comment has been restored', [
            'user_id' => $request->user()->user_id,
            'comment_id' => $comment->id,
        ]);

        if ($request->user()->can('server_players_incidents_show')) {
            return redirect()->route('server.record-comments.show.get', ['comment_id' => $comment->id]);
        }

        return redirect()->route('server.chars.show.get', ['char_id' => $char_id]);
    }

    public function getRecordCommentDataChar($char_id, Request $request)
    {
        if ($request->user()->can('server_players_incidents_show')) {
            $comments = ServerRecordComment::withTrashed()
                ->where('char_id', $char_id)
                ->select(['id', 'char_id', 'record_type', 'body', 'author', 'game_id', 'created_at', 'updated_at', 'deleted_at']);
        } elseif ($request->user()->checkPlayerChar($char_id) === true) {
            $comments = ServerRecordComment::where('char_id', $char_id)
                ->select(['id', 'char_id', 'record_type', 'body', 'author', 'game_id', 'created_at', 'updated_at', 'deleted_at']);
        } else {
            $comments = new Collection;
        }

        return Datatables::of($comments)
            ->removeColumn('char_id')
            ->removeColumn('deleted_at')
            ->editColumn('record_type', function (ServerRecordComment $comment) {
                return ucfirst($comment->record_type);
            })
            ->editColumn('body', function (ServerRecordComment $comment) {
                return Str::limit($comment->body, 150);
            })
            ->editColumn('created_at', '@if(isset($deleted_at))<s>@endif()<a href="{{route(\'server.record-comments.show.get\',[\'comment_id\'=>$id])}}">{{$created_at}}</a>@if(isset($deleted_at))</s>@endif()')
            ->editColumn('updated_at', '{{$updated_at}}')
            ->addColumn('status', '@if(isset($deleted_at)) Deleted @else() Active @endif()')
            ->addColumn('action', '<p><a href="{{route(\'server.record-comments.show.get\',[\'comment_id\'=>$id])}}" class="btn btn-success" role="button">Show</a></p>')
            ->rawColumns(['created_at', 'action'])
            ->make();
    }

    private function canSee($char_id, Request $request)
    {
        return $request->user()->can('server_players_incidents_show')
            || $request->user()->checkPlayerChar($char_id) === true;
    }

    private function canEdit($char_id, Request $request)
    {
        return $request->user()->can('server_players_incidents_edit')
            || $request->user()->checkPlayerChar($char_id) === true;
    }
}
