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
use App\Http\Controllers\Controller;
use App\Models\ServerPollQuestion;
use App\Models\ServerPollOption;
use App\Models\ServerPollTextReply;
use App\Models\ServerPollVote;
use Yajra\Datatables\Datatables;

class PollController extends Controller
{
    /**
     * Show the overview over all the polls
     */
    public function index(Request $request)
    {
        return view('server.poll.index');
    }

    public function getPollData(Request $request, Datatables $datatables)
    {
        $builder = ServerPollQuestion::query()->select('id', 'question');

        //If the user doesnt have poll permissions, show only the public polls
        if ( !$request->user() || $request->user()->cannot('server_poll_show')) {
            $builder->where('publicresult', 1);
        }

        return $datatables->eloquent($builder)
            ->addColumn('action', function ($question) {
                return '<a href="' . route("server.poll.show", ["id" => $question->id]) . '" class="btn btn-success" role="button">Show</a>';
            })
            ->editColumn('question', function ($question) {
                if(strlen($question) > 80)
                    return substr($question->question, 0, 80) . '...';
                return $question->question;
            })
            ->rawColumns(['action'])
            ->make(TRUE);
    }

    public function show(Request $request, $id)
    {
        return $this->showPrivate($request, $id, NULL);
    }

    public function showPrivate(Request $request, $id, $key)
    {
        //Get the poll with the specified id
        $question = ServerPollQuestion::findOrFail($id);

        $show_all = false;
        if($request->user())
            $show_all = $request->user()->can('server_poll_show');

        //Check if the poll is visible to the public (or if the user has view permissions)ServerPollQuestion
        if (!$question->isVisible($show_all, $key))
            abort(403,"You are not authorized to view this poll");

        if (!$question->isComplete())
            abort(403,"This poll is still ongoing.");

        //Check the poll type and prepare the data accordingly
        switch ($question->polltype) {
            case "OPTION":
            case "MULTICHOICE":
                return view('server.poll.option',["question"=>$question]);
            case "TEXT":
                return view('server.poll.text',["question"=>$question]);
            case "NUMVAL":
                return view('server.poll.numval',["question"=>$question]);
            default:
                return view('server.poll.error',["question"=>$question]);
        }
    }
}
