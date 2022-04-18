<?php
/**
 * Copyright (c) 2016-2017 "Werner Maisl"
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

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ServerLibrary;
use MongoDB\Driver\Server;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Log;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            //If the users byond account is not linked and he doesnt have permission to edit the library -> Abort
            if($request->user()->byond_linked == False)
            {
                abort('403','Your byond account is not linked to your forum account.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('server.library.index');
    }

    public function getShow($book_id,Request $request)
    {
        $book = ServerLibrary::findOrFail($book_id);

        $canedit = $this->can_edit($book,$request->user());
        return view('server.library.show', ['book' => $book, 'canedit' => $canedit]);
    }

    public function getEdit($book_id, Request $request)
    {
        $book = ServerLibrary::findOrFail($book_id);

        if(!$this->can_edit($book,$request->user()))
        {
            abort('403','You do not have the required permission');
        }

        return view('server.library.edit', ['book' => $book]);
    }

    public function postEdit($book_id, Request $request)
    {
        $book = ServerLibrary::findOrFail($book_id);
        if(!$this->can_edit($book,$request->user()))
        {
            abort('403','You do not have the required permission');
        }
        $this->validate($request,[
            'author' => 'required|max:50',
            'title' => 'required|max:50',
            'content' => 'required',
            'category' => 'required|in:Reference,Non-Fiction,Fiction,Religion,Adult'
        ]);

        $book->author = $request->input('author');
        $book->title = $request->input('title');
        $book->content = $request->input('content');
        $book->category = $request->input('category');
        $book->save();

        Log::notice('perm.library.edit - Book has been edited',['user_id' => $request->user()->user_id, 'book_id' => $book->id]);

        return redirect()->route('server.library.show.get',['book_id'=>$book_id]);
    }

    public function delete($book_id, Request $request)
    {
        if($request->user()->cannot('server_library_edit'))
        {
            abort('403','You do not have the required permission');
        }

        $book = ServerLibrary::findOrFail($book_id);
        Log::notice('perm.library.delete - Book has been deleted',['user_id' => $request->user()->user_id, 'book_id' => $book->id, 'book_title' => $book->name, 'book_author' => $book->author]);
        $book->delete();

        return redirect()->route('server.library.index');
    }

    public function getAdd(Request $request)
    {
        return view('server.library.add');
    }

    public function postAdd(Request $request)
    {
        $this->validate($request,[
            'author' => 'required|max:50',
            'title' => 'required|max:50',
            'content' => 'required',
            'category' => 'required|in:Reference,Non-Fiction,Fiction,Religion,Adult'
        ]);
        $book = new ServerLibrary();
        $book->author = $request->input('author');
        $book->title = $request->input('title');
        $book->content =  $request->input('content');
        $book->category = $request->input('category');
        $book->uploader = $request->user()->byond_key;
        $book->uploadtime = Carbon::now();
        $book->save();

        Log::notice('perm.library.add - Book has been added',['user_id' => $request->user()->user_id, 'book_id' => $book->id]);

        return redirect()->route('server.library.show.get',['book_id'=>$book->id]);
    }

    public function getBookData()
    {
        $books = ServerLibrary::select(['id','title','author','category']);

        return Datatables::of($books)
            ->removeColumn('id')
            ->editColumn('title', '<a href="{{route(\'server.library.show.get\',[\'book\'=>$id])}}">{{$title}}</a>')
            ->addColumn('action','<div class="btn-group"><a href="{{route(\'server.library.show.get\',[\'book\'=>$id])}}" class="btn btn-success" role="button">Show</a>  @can(\'server_library_edit\')<a href="{{route(\'server.library.edit.get\',[\'book\'=>$id])}}" class="btn btn-info" role="button">Edit</a><a href="{{route(\'server.library.delete\',[\'book\'=>$id])}}" class="btn btn-danger" role="button">Delete</a>@endcan()</div>')
            ->rawColumns(['title', 'action'])
            ->make();
    }

    private function can_edit($book,$user)
    {
        //Check if user has library edit persm
        if ($user->can('server_library_edit'))
            return true;
        if($user->byond_key == $book->uploader)
            return true;

        return false;
    }
}
