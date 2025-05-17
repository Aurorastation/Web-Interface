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

namespace App\Models;
use Illuminate\Support\Facades\DB;
use PDO;

class AntagContest
{
    public function get_chars_per_faction()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_participants')
            ->select(DB::raw('contest_faction, count(1) as char_count'))
            ->groupBy('contest_faction')
            ->get();
    }

    public function get_missions_per_faction_success()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('character_faction, count(1) as faction_missions'))
            ->groupBy('character_faction')
            ->where('objective_outcome',1)
            ->where('duplicate',0)
            ->get();
    }

    public function get_missions_per_faction_fail()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('character_faction, count(1) as faction_missions'))
            ->groupBy('character_faction')
            ->where('objective_outcome',0)
            ->where('duplicate',0)
            ->get();
    }


    public function get_missions_per_type_success()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('objective_type, count(1) as type_missions'))
            ->groupBy('objective_type')
            ->where('objective_outcome',1)
            ->where('duplicate',0)
            ->get();
    }

    public function get_missions_per_type_fail()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('objective_type, count(1) as type_missions'))
            ->groupBy('objective_type')
            ->where('objective_outcome',0)
            ->where('duplicate',0)
            ->get();
    }


    public function get_missions_per_side_success()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('objective_side, count(1) as side_missions'))
            ->groupBy('objective_side')
            ->where('objective_outcome',1)
            ->where('duplicate',0)
            ->get();
    }

    public function get_missions_per_side_fail()
    {
        DB::connection('server')->setFetchMode(PDO::FETCH_ASSOC);
        return DB::connection('server')->table('contest_reports')
            ->select(DB::raw('objective_side, count(1) as side_missions'))
            ->groupBy('objective_side')
            ->where('objective_outcome',0)
            ->where('duplicate',0)
            ->get();
    }
}