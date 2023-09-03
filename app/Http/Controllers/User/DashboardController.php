<?php
/**
 * Copyright (c) 2016 "Werner Maisl"
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

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Services\Server\PlayerWarning;
use App\Models\ServerPlayer;
use App\Models\CCIAAction;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use MongoDB\Driver\Server;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->cannot('byond_linked')) {
            return redirect()->route('user.link');
        }

        $player = ServerPlayer::where('ckey', $request->user()->byond_key)->first();

        //Get player warning data
        $playerwarning = new PlayerWarning($request->user()->byond_key);

        return view('user.dashboard.index', array(
            "whitelists" => $player->get_player_whitelists(),
            "warnings" => $playerwarning,
            "chars" => $player->get_chars()
        ));
    }
}
