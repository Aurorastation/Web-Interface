<?php

/**
 * Copyright (c) 2016 "Sierra Brown"
 * Copyright (c) 2017 "Werner Maisl"
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

use App\Models\CCIAReport;
use App\Models\CCIAReportTranscript;
use App\Models\ServerAntagLog;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->cannot('ccia_report_show')) {
                abort('403', 'You do not have the required permission');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('ccia.report.index');
    }

    public function getShow(Request $request, $report_id)
    {
        $report = CCIAReport::findOrFail($report_id);
        $transcripts = $report->transcripts()
            ->select(['id', 'character_id', 'interviewer', 'antag_involvement'])
            ->with(
                array(
                    'character' => function ($query) {
                        $query->select('id', 'name');
                    })
            )->get();
        return view('ccia.report.show', ['report' => $report, 'transcripts' => $transcripts]);
    }

    public function getEdit(Request $request, $report_id)
    {
        if ($request->user()->cannot('ccia_report_edit')) {
            abort('403', 'You do not have permission to edit CCIA Reports.');
        }

        $report = CCIAReport::findOrFail($report_id);
        return view('ccia.report.edit', ['report' => $report]);
    }

    public function postEdit(Request $request, $report_id)
    {
        if ($request->user()->cannot('ccia_report_edit')) {
            abort('403', 'You do not have permission to edit CCIA Reports.');
        }
        $this->validate($request, [
            'title' => 'required',
            'report_date' => 'required|date',
            'status' => 'required|in:new,approved,rejected,completed'
        ]);

        $report = CCIAReport::findOrFail($report_id);
        $report->title = $request->input('title');
        $report->report_date = $request->input('report_date');
        $report->public_topic = $request->input('public_topic');
        $report->internal_topic = $request->input('internal_topic');
        $report->game_id = $request->input('game_id');
        $report->status = $request->input('status');
        $report->save();

        Log::notice('perm.cciareport.edit - CCIA Report has been edited', ['user_id' => $request->user()->user_id, 'report_id' => $report->id]);

        return redirect()->route('ccia.report.show.get', ['report_id' => $report->id]);
    }

    public function getAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_report_edit')) {
            abort('403', 'You do not have permission to edit CCIA Reports.');
        }

        return view('ccia.report.add');
    }

    public function postAdd(Request $request)
    {
        if ($request->user()->cannot('ccia_report_edit')) {
            abort('403', 'You do not have permission to edit CCIA Reports.');
        }

        $this->validate($request, [
            'title' => 'required',
            'report_date' => 'required|date',
            'status' => 'required|in:new,approved,rejected,completed'
        ]);

        $report = new CCIAReport();
        $report->title = $request->input('title');
        $report->report_date = $request->input('report_date');
        $report->public_topic = $request->input('public_topic');
        $report->internal_topic = $request->input('internal_topic');
        $report->game_id = $request->input('game_id');
        $report->status = $request->input('status');
        $report->save();

        Log::notice('perm.cciareport.add - CCIA Report has been added', ['user_id' => $request->user()->user_id, 'report_id' => $report->id]);

        return redirect()->route('ccia.report.show.get', ['report_id' => $report->id]);
    }

    public function delete(Request $request, $report_id)
    {
        if ($request->user()->cannot('ccia_report_edit')) {
            abort('403', 'You do not have permission to edit CCIA Reports.');
        }

        $report = CCIAReport::findOrFail($report_id);
        Log::notice('perm.cciareport.delete - CCIA Report has been deleted', ['user_id' => $request->user()->user_id, 'report_id' => $report->id]);
        $report->delete();

        return redirect()->route('ccia.report.index');
    }

    public function getData(Request $request)
    {
        $data = CCIAReport::select(['id', 'report_date', 'title', 'status']);

        return Datatables::of($data)
            ->editColumn('title', function (CCIAReport $report) {
                return '<a href="' . route('ccia.report.show.get', ['report_id' => $report->id]) . '">' . $report->title . '</a></p>';
            })
            ->addColumn('action', function (CCIAReport $report) use ($request) {
                $actionstring = '<div class="btn-group"><a href="' . route('ccia.report.show.get', ['report_id' => $report->id]) . '" class="btn btn-success" role="button">Show</a>';
                if ($request->user()->can('ccia_report_edit')) {
                    $actionstring .= '<a href="' . route('ccia.report.edit.get', ['report_id' => $report->id]) . '" class="btn btn-info" role="button">Edit</a>';
                    $actionstring .= '<a href="' . route('ccia.report.delete', ['report_id' => $report->id]) . '" class="btn btn-danger" role="button">Delete</a>';
                }
                $actionstring .= '</div>';
                return $actionstring;
            })
            ->rawColumns(['title', 'action'])
            ->make();
    }

    public function getTranscript(Request $request, $transcript_id)
    {
        $transcript = CCIAReportTranscript::findOrFail($transcript_id);
        return $transcript->text;
    }

    public function getAntagClaim(Request $request, $transcript_id)
    {
        $transcript = CCIAReportTranscript::findOrFail($transcript_id);
        return $transcript->antag_involvement_text;
    }
}
