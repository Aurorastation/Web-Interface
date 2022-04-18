<?php
/**
 * Copyright (c) 2019 "Werner Maisl"
 *
 * This file is part of Aurorastation-Wi
 * Aurorastation-Wi is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\ServerDocument;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Log;

class DocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //If the users byond account is not linked and he doesnt have permission to edit the library -> Abort
            if ($request->user()->cannot('server_documents_show'))
                abort('403', 'You do not have the required permission.');
            return $next($request);
        });
    }

    public function index()
    {
        return view('server.documents.index');
    }

    public function getShow($document_id, Request $request)
    {
        $document = ServerDocument::findOrFail($document_id);
        $canedit = $request->user()->can('server_documents_edit');

        return view('server.documents.show', ['document' => $document, 'canedit' => $canedit]);
    }

    public function getEdit($document_id, Request $request)
    {
        $document = ServerDocument::findOrFail($document_id);

        if ($request->user()->cannot('server_documents_edit')) {
            abort('403', 'You do not have the required permission');
        }

        return view('server.documents.edit', ['document' => $document]);
    }

    public function postEdit($document_id, Request $request)
    {
        $document = ServerDocument::findOrFail($document_id);
        if ($request->user()->cannot('server_documents_edit')) {
            abort('403', 'You do not have the required permission');
        }
        $this->validate($request, [
            'name' => 'required|max:100',
            'title' => 'required|max:26',
            'chance' => 'required|numeric',
            'content' => 'required',
            'tags' => 'required|json'
        ]);

        $document->name = $request->input('name');
        $document->title = $request->input('title');
        $document->chance = $request->input('chance');
        $document->content = $request->input('content');
        $document->tags = $request->input('tags');
        $document->save();

        Log::notice('perm.documents.edit - Document has been edited', ['user_id' => $request->user()->user_id, 'document_id' => $document->id]);

        return redirect()->route('server.documents.show.get', ['document_id' => $document_id]);
    }

    public function delete($document_id, Request $request)
    {
        if ($request->user()->cannot('server_documents_edit')) {
            abort('403', 'You do not have the required permission');
        }

        $document = ServerDocument::findOrFail($document_id);
        Log::notice('perm.library.delete - Book has been deleted', ['user_id' => $request->user()->user_id, 'document_id' => $document->id]);
        $document->delete();

        return redirect()->route('server.documents.index');
    }

    public function getAdd(Request $request)
    {
        return view('server.documents.add');
    }

    public function postAdd(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'title' => 'required|max:26',
            'chance' => 'required|numeric',
            'content' => 'required',
            'tags' => 'required|json'
        ]);
        $document = new ServerDocument();
        $document->name = $request->input('name');
        $document->title = $request->input('title');
        $document->chance = $request->input('chance');
        $document->content = $request->input('content');
        $document->tags = $request->input('tags');
        $document->save();

        Log::notice('perm.library.add - Book has been added', ['user_id' => $request->user()->user_id, 'book_id' => $document->id]);

        return redirect()->route('server.documents.show.get', ['book_id' => $document->id]);
    }

    public function getDocumentData()
    {
        $documents = ServerDocument::select(['id', 'name', 'tags']);

        return Datatables::of($documents)
            ->removeColumn('id')
            ->editColumn('name', '<a href="{{route(\'server.documents.show.get\',[\'document\'=>$id])}}">{{$name}}</a>')
            ->addColumn('action', '<div class="btn-group"><a href="{{route(\'server.documents.show.get\',[\'document\'=>$id])}}" class="btn btn-success" role="button">Show</a>  @can(\'server_documents_edit\')<a href="{{route(\'server.documents.edit.get\',[\'book\'=>$id])}}" class="btn btn-info" role="button">Edit</a><a href="{{route(\'server.documents.delete\',[\'book\'=>$id])}}" class="btn btn-danger" role="button">Delete</a>@endcan()</div>')
            ->rawColumns(['name', 'action'])
            ->make();
    }
}
