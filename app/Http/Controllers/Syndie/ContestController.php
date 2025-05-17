<?php
/**
 * Copyright (c) 2016 "Arrow768"
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
namespace App\Http\Controllers\Syndie;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AntagContest;

class ContestController extends Controller
{
    public function index(Request $request)
    {
        if($request->user()->cannot('syndie_contest_show'))
        {
            abort('403','You do not have the required permission');
        }

        $contest = new AntagContest();

        $chars_per_faction = $contest->get_chars_per_faction();

        $missions_per_faction_success = $contest->get_missions_per_faction_success();
        $missions_per_faction_fail = $contest->get_missions_per_faction_fail();

        $missions_per_type_success = $contest->get_missions_per_type_success();
        $missions_per_type_fail = $contest->get_missions_per_type_fail();

        $missions_per_side_success = $contest->get_missions_per_side_success();
        $missions_per_side_fail = $contest->get_missions_per_side_fail();

        return view('syndie.contest.index', [
            'chars_per_faction' => $chars_per_faction,
            'missions_per_faction_success' => $missions_per_faction_success,
            'missions_per_faction_fail' => $missions_per_faction_fail,
            'missions_per_type_success' => $missions_per_type_success,
            'missions_per_type_fail' => $missions_per_type_fail ,
            'missions_per_side_success' => $missions_per_side_success,
            'missions_per_side_fail' => $missions_per_side_fail
        ]);
    }

    public function getFactions(Request $request)
    {

    }

    public function getReports(Request $request)
    {

    }
}
