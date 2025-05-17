<?php
/**
 * Copyright (c) 2018 "Arrow768"
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

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ServerNewsChannel;
use App\Models\ServerNewsStory;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //If the users byond account is not linked and he doesnt have permission to access the server news -> Abort
            if ($request->user()->byond_linked == False)
                abort('403', 'Your byond account is not linked to your forum account.');
            if ($request->user()->cannot('server_news_show'))
                abort('403', 'You do not have the required permission.');
            return $next($request);
        });
    }

    public function index()
    {
        return view('server.news.index');
    }

    public function getShow($news_id, Request $request)
    {
        $news = ServerNewsStory::findOrFail($news_id);

        return view('server.news.show', ['news' => $news]);
    }

    public function getEdit($news_id, Request $request)
    {
        $news = ServerNewsStory::findOrFail($news_id);
        //Check if its unapproved and the user can only edit or if the user can approve orders
        if($request->user()->cannot("server_news_edit"))
            abort("403","You do not have the required permission");
        if($news->approved_at && $request->user()->cannot('server_news_approve'))
            abort("403","You do not have the required permission for approved news");

        $channels = ServerNewsChannel::select('id', 'name')->get();
        $channel_array = [];
        foreach ($channels as $channel)
            $channel_array[$channel->id] = $channel->name;

        return view('server.news.edit', ['news' => $news, 'channels' => $channel_array]);
    }

    public function postEdit($news_id, Request $request)
    {
        $news = ServerNewsStory::findOrFail($news_id);
        //TODO: Enable player submitted news
        if($request->user()->cannot("server_news_edit"))
            abort("403","You do not have the required permission");
        if($news->approved_at && $request->user()->cannot('server_news_approve'))
            abort("403","You do not have the required permission to edit approved news");

        $this->validate($request, [
            'author' => 'required|max:50',
            'body' => 'required',
            'message_type' => 'required',
            'channel_id' => 'required|exists:server.news_channels,id',
            'ic_timestamp' => 'required|date|after:+440years',
            'publish_at' => 'nullable|date',
            'publish_until' => 'nullable|date|after:publish_at'
        ]);

        $news->author = $request->input('author');
        $news->body = $request->input('body');
        $news->message_type = $request->input('message_type');
        $news->channel_id = $request->input('channel_id');
        $news->ic_timestamp = $request->input('ic_timestamp');

        if ($request->exists('publish_at')) {
            $news->publish_at = $request->input('publish_at');
        } else {
            $news->publish_at = Carbon::now();
        }
        $news->publish_until = $request->input('publish_until');

        $news->save();

        Log::notice('perm.news.edit - News has been edited', ['user_id' => $request->user()->user_id, 'news_id' => $news->id]);

        return redirect()->route('server.news.show.get', ['news_id' => $news_id]);
    }

    public function delete($news_id, Request $request)
    {
        $news = ServerNewsStory::findOrFail($news_id);
        if($request->user()->cannot("server_news_edit"))
            abort("403","You do not have the required permission");
        if($news->approved_at && $request->user()->cannot('server_news_approve'))
            abort("403","You do not have the required permission for approved news");

        Log::notice('perm.news.delete - News has been deleted', ['user_id' => $request->user()->user_id, 'news_id' => $news->id]);
        $news->delete();

        return redirect()->route('server.news.index');
    }

    public function approve($news_id, Request $request)
    {
        if ($request->user()->cannot('server_news_approve'))
            abort('403', 'You do not have the required permission');

        $news = ServerNewsStory::findOrFail($news_id);

        $news->approved_at = Carbon::now();
        $news->approved_by = $request->user()->username;
        $news->save();

        Log::notice('perm.news.approve - News has been approved', ['user_id' => $request->user()->user_id, 'news_id' => $news->id]);

        return redirect()->route('server.news.show.get', ['news_id' => $news_id]);
    }

    public function getAdd(Request $request)
    {
        if ($request->user()->cannot('server_news_edit'))
            abort('403', 'You do not have the required permission');

        $channels = ServerNewsChannel::select('id', 'name')->get();
        $channel_array = [];
        foreach ($channels as $channel)
            $channel_array[$channel->id] = $channel->name;

        return view('server.news.add', ['channels' => $channel_array]);
    }

    public function postAdd(Request $request)
    {
        if ($request->user()->cannot('server_news_edit'))
            abort('403', 'You do not have the required permission');

        $this->validate($request, [
            'author' => 'required|max:50',
            'body' => 'required',
            'message_type' => 'required',
            'channel_id' => 'required|exists:server.news_channels,id',
            'ic_timestamp' => 'required|date|after:+440years',
            'publish_at' => 'nullable|date',
            'publish_until' => 'nullable|date|after:publish_at'
        ]);

        $news = new ServerNewsStory();
        $news->author = $request->input('author');
        $news->body = $request->input('body');
        $news->message_type = $request->input('message_type');
        $news->channel_id = $request->input('channel_id');
        $news->ic_timestamp = $request->input('ic_timestamp');
        $news->created_by = $request->user()->byond_key;

        if ($request->exists('publish_at')) {
            $news->publish_at = $request->input('publish_at');
        } else {
            $news->publish_at = Carbon::now();
        }

        $news->publish_until = $request->input('publish_until');

        if($request->user()->can('server_news_approve')){
            $news->approved_at = Carbon::now();
            $news->approved_by = "automatic";
        }

        $news->save();

        Log::notice('perm.news.add - News has been added', ['user_id' => $request->user()->user_id, 'news_id' => $news->id]);

        return redirect()->route('server.news.show.get', ['news_id' => $news->id]);
    }

    public function getNewsData()
    {
        $news = ServerNewsStory::with('channel')->select(['id', 'body', 'channel_id', 'author','approved_by'])->get();
        return Datatables::of($news)
            ->removeColumn('channel_id')
            ->addColumn('channel', function (ServerNewsStory $story) {
                return $story->channel->name;
            })
            ->editColumn('body', '{{str_limit($body,50)}}')
            ->addColumn('action', '<div class="btn-group" role="group"><a href="{{route(\'server.news.show.get\',[\'news_id\'=>$id])}}" class="btn btn-success" role="button">Show</a>  @can(\'server_news_edit\')<a href="{{route(\'server.news.edit.get\',[\'news_id\'=>$id])}}" class="btn btn-info" role="button">Edit</a><a href="{{route(\'server.news.delete\',[\'news_id\'=>$id])}}" class="btn btn-danger" role="button">Delete</a>@endcan()</div>')
            ->rawColumns(['body', 'action'])
            ->make();
    }
}
