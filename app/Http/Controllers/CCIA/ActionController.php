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

namespace App\Http\Controllers\CCIA;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\CCIAAction;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Log;

class ActionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->cannot('ccia_action_show') && $request->user()->cannot('_heads-of-staff')) {
            abort('403', 'You do not have permission to view CCIA Actions.');
        }
        return view('ccia.actions.index');
    }

    public function getShow(Request $request, $action_id)
    {
        $action = CCIAAction::findOrFail($action_id);
        if ($request->user()->cannot('ccia_action_show') && $request->user()->cannot('_heads-of-staff') && !$action->has_linked_char($request->user())) {
            abort('403', 'You do not have permission to view CCIA Actions.');
        }

        $linked_chars = $action->characters()->get();
        return view('ccia.actions.show', ['action' => $action, 'linked_chars' => $linked_chars]);
    }

    public function getEdit(Request $request, $action_id)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }

        $action = CCIAAction::findOrFail($action_id);
        return view('ccia.actions.edit', ['action' => $action]);
    }

    public function postEdit(Request $request, $action_id)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required|in:injunction,suspension,reprimand,demotion,other',
            'issuedby' => 'required',
            'details' => 'required',
            'url' => 'required'
        ]);

        $action = CCIAAction::findOrFail($action_id);
        $action->title = $request->input('title');
        $action->type = $request->input('type');
        $action->issuedby = $request->input('issuedby');
        $action->details = $request->input('details');
        $action->url = $request->input('url');

        $expires_at = $request->input('expires_at');
        if ($expires_at == NULL || $expires_at == "" || $expires_at == 0) {
            $action->expires_at = NULL;
        } else {
            $action->expires_at = $expires_at;
        }

        $action->save();

        Log::notice('perm.cciaaction.edit - CCIA Action has been edited', ['user_id' => $request->user()->user_id, 'action_id' => $action->id]);

        return redirect()->route('ccia.actions.show.get', ['action_id' => $action->id]);
    }

    public function getAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }

        return view('ccia.actions.add');
    }

    public function postAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }

        $this->validate($request, [
            'title' => 'required',
            'type' => 'required|in:injunction,suspension,reprimand,demotion,other',
            'issuedby' => 'required',
            'details' => 'required',
            'url' => 'required'
        ]);

        $action = new CCIAAction();
        $action->title = $request->input('title');
        $action->type = $request->input('type');
        $action->issuedby = $request->input('issuedby');
        $action->details = $request->input('details');
        $action->url = $request->input('url');

        $expires_at = $request->input('expires_at');
        if ($expires_at == NULL || $expires_at == "" || $expires_at == 0) {
            $action->expires_at = NULL;
        } else {
            $action->expires_at = $expires_at;
        }

        $action->save();

        Log::notice('perm.cciaaction.add - CCIA Action has been added', ['user_id' => $request->user()->user_id, 'action_id' => $action->id]);

        return redirect()->route('ccia.actions.show.get', ['action_id' => $action->id]);
    }

    public function linkChar(Request $request, $action_id)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }
        $char_id = $request->input("char_id");

        $action = CCIAAction::findOrFail($action_id);
        $action->characters()->attach($char_id);

        Log::notice('perm.cciaaction.attach - CCIA Action Char has been attached', ['user_id' => $request->user()->user_id, 'action_id' => $action->id, 'char_id' => $char_id]);

        return redirect()->route('ccia.actions.show.get', ['action_id' => $action_id]);
    }

    public function unlinkChar(Request $request, $action_id)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }
        $char_id = $request->input("char_id");

        $action = CCIAAction::findOrFail($action_id);
        $action->characters()->detach($char_id);

        Log::notice('perm.cciaaction.detach - CCIA Action Char has been detached', ['user_id' => $request->user()->user_id, 'action_id' => $action->id, 'char_id' => $char_id]);

        return redirect()->route('ccia.actions.show.get', ['action_id' => $action_id]);
    }

    public function delete(Request $request, $action_id)
    {
        if ($request->user()->cannot('ccia_action_edit')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }

        $action = CCIAAction::findOrFail($action_id);
        Log::notice('perm.cciaaction.delete - CCIA Action has been deleted', ['user_id' => $request->user()->user_id, 'action_id' => $action->id]);
        $action->delete();

        return redirect()->route('ccia.actions.index');
    }

    public function getDataActive(Request $request)
    {
        if ($request->user()->cannot('ccia_action_show') && $request->user()->cannot('_heads-of-staff')) {
            abort('403', 'You do not have permission to view CCIA Actions.');
        }

        $data = CCIAAction::select(['id', 'title'])->where('expires_at', NULL)->orWhere('expires_at', '>=', date("Y-m-d"));

        return Datatables::of($data)
            ->editColumn('title', '<a href="{{ route(\'ccia.actions.show.get\', [\'id\' => $id]) }}">{{$title}}</a>')
            ->addColumn('action', '<div class="btn-group" role="group"><a href="{{ route(\'ccia.actions.show.get\', [\'id\' => $id]) }}" class="btn btn-success" role="button">Show</a>  @can(\'ccia_action_edit\')<a href="{{route(\'ccia.actions.edit.get\', [\'id\' => $id]) }}" class="btn btn-info" role="button">Edit</a><a href="{{route(\'ccia.actions.delete\', [\'id\' => $id]) }}" class="btn btn-danger" role="button">Delete</a>@endcan()</div>')
            ->rawColumns(['title', 'action'])
            ->make();
    }

    public function getDataAll(Request $request)
    {
        if ($request->user()->cannot('ccia_action_show')) {
            abort('403', 'You do not have permission to edit CCIA Actions.');
        }
        $data = CCIAAction::select(['id', 'title', 'expires_at']);

        return Datatables::of($data)
            ->editColumn('title', '<a href="{{ route(\'ccia.actions.show.get\', [\'id\' => $id]) }}">{{$title}}</a>')
            ->addColumn('action', '<div class="btn-group" role="group"><a href="{{ route(\'ccia.actions.show.get\', [\'id\' => $id]) }}" class="btn btn-success" type="button">Show</a>  @can(\'ccia_action_edit\')<a href="{{route(\'ccia.actions.edit.get\', [\'id\' => $id]) }}" class="btn btn-info" role="button">Edit</a><a href="{{route(\'ccia.actions.delete\', [\'id\' => $id]) }}" class="btn btn-danger" role="button">Delete</a>@endcan()</div>')
            ->rawColumns(['title', 'action'])
            ->make();
    }
}
