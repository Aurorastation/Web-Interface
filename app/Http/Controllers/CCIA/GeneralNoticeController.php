<?php

/**
 * Copyright (c) 2016 "Sierra Brown"
 * Copyright (c) 2017 "Arrow768"
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
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Log;

use App\Models\CCIAGeneralNotice;

class GeneralNoticeController extends Controller
{

    public function index()
    {
        return view('ccia.generalnotice.index');
    }

    public function getShow(Request $request, $generalnotice_id)
    {
        $notice = CCIAGeneralNotice::findOrFail($generalnotice_id);
        return view('ccia.generalnotice.show', ['notice' => $notice]);
    }

    public function getEdit(Request $request, $generalnotice_id)
    {
        if ($request->user()->cannot('ccia_general_notice_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }

        $notice = CCIAGeneralNotice::findOrFail($generalnotice_id);
        return view('ccia.generalnotice.edit', ['notice' => $notice]);
    }

    public function postEdit(Request $request, $generalnotice_id)
    {
        if ($request->user()->cannot('ccia_general_notice_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }
        $this->validate($request, [
            'title' => 'required',
            'message' => 'required'
        ]);

        $notice = CCIAGeneralNotice::findOrFail($generalnotice_id);
        $notice->title = $request->input('title');
        $notice->message = $request->input('message');
        if ($request->input('automatic'))
            $notice->automatic = 1;
        else
            $notice->automatic = 0;
        $notice->save();

        Log::notice('perm.cciageneralnotice.edit - CCIA General Notice has been edited', ['user_id' => $request->user()->user_id, 'notice_id' => $notice->id]);

        return redirect()->route('ccia.generalnotice.show.get', ['generalnotice_id' => $notice->id]);
    }

    public function getAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_general_notice_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }

        return view('ccia.generalnotice.add');
    }

    public function postAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_general_notice_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }

        $this->validate($request, [
            'title' => 'required',
            'message' => 'required'
        ]);

        $notice = new CCIAGeneralNotice();
        $notice->title = $request->input('title');
        $notice->message = $request->input('message');
        if ($request->input('automatic'))
            $notice->automatic = 1;
        else
            $notice->automatic = 0;
        $notice->save();

        Log::notice('perm.cciageneralnotice.add - CCIA General Notice has been added', ['user_id' => $request->user()->user_id, 'notice_id' => $notice->id]);

        return redirect()->route('ccia.generalnotice.show.get', ['generalnotice_id' => $notice->id]);
    }

    public function delete(Request $request, $generalnotice_id)
    {
        if ($request->user()->cannot('ccia_general_notice_edit')) {
            abort('403', 'You do not have permission to edit CCIA General Notices.');
        }

        $notice = CCIAGeneralNotice::findOrFail($generalnotice_id);
        Log::notice('perm.cciageneralnotice.delete - CCIA General Notice has been deleted', ['user_id' => $request->user()->user_id, 'notice_id' => $notice->id]);
        $notice->delete();

        return redirect()->route('ccia.generalnotice.index');
    }

    public function getData(Request $request)
    {
        if ($request->user()->can('ccia_general_notice_edit')) {
            $data = CCIAGeneralNotice::select(['id', 'title']);
        } else {
            $data = CCIAGeneralNotice::select(['id', 'title'])->where('automatic', '1');
        }

        return Datatables::of($data)
            ->editColumn('title', function (CCIAGeneralNotice $CCIAGeneralNotice) {
                return '<a href="' . route('ccia.generalnotice.show.get', ['generalnotice_id' => $CCIAGeneralNotice->id]) . '">' . $CCIAGeneralNotice->title . '</a></p>';
            })
            ->addColumn('action', function (CCIAGeneralNotice $CCIAGeneralNotice) use ($request) {
                $actionstring = '<div class="btn-group"><a href="' . route('ccia.generalnotice.show.get', ['generalnotice_id' => $CCIAGeneralNotice->id]) . '" class="btn btn-success" role="button">Show</a>';
                if ($request->user()->can('ccia_general_notice_edit')) {
                    $actionstring .= '<a href="' . route('ccia.generalnotice.edit.get', ['generalnotice_id' => $CCIAGeneralNotice->id]) . '" class="btn btn-info" role="button">Edit</a>';
                    $actionstring .= '<a href="' . route('ccia.generalnotice.delete', ['generalnotice_id' => $CCIAGeneralNotice->id]) . '" class="btn btn-danger" role="button">Delete</a>';
                }
                $actionstring .= '</div>';
                return $actionstring;
            })
            ->rawColumns(['title', 'action'])
            ->make();

    }
}
