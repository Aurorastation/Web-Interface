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

use App\Models\ServerPlayer;
use Illuminate\Http\Request;

use DB;
use App\Http\Controllers\Controller;
use App\Services\Server\Helpers;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $byond_key = NULL;

        //Check if a linking request already exists (where the deleted_at date is not set)
        $linking_in_progress = DB::connection('server')->table('player_linking')
                ->where('forum_id', '=', $request->user()->user_id)->where('deleted_at', '=', NULL)->count() != 0;


        //Check if the linking request is marked as verified
        if ($linking_in_progress == TRUE) {
            //get the linking request
            $linking_request = DB::connection('server')->table('player_linking')
                ->where('forum_id', '=', $request->user()->user_id)->where('deleted_at', '=', NULL)->first();

            $byond_key = $linking_request->player_ckey;

            //Check if the linking request is set to something other than new
            if ($linking_request->status == "confirmed") {
                //If its confirmed write it to the forum db
                $sanitized_key = Helpers::sanitize_ckey($linking_request->player_ckey);
                $request->user()->byond_key = $sanitized_key;
                $request->user()->save();

                //Update the forum
                $base_url = config('aurora.forum_url');
                if ($base_url) {
                    $request_url = $base_url . 'api/core/members/' . $request->user()->id .
                        '?key=' . config('aurora.forum_api_key') .
                        '&customFields[' . config('aurora.forum_byond_attribute') . ']=' . $sanitized_key;
                    $client = new \GuzzleHttp\Client();
                    $client->request('POST', $request_url);
                }


                //Set the status of the linking request to linked and set the deleted_at date
                DB::connection('server')->table('player_linking')
                    ->where('forum_id', '=', $request->user()->user_id)
                    ->where('deleted_at', '=', NULL)
                    ->update(['deleted_at' => date('Y-m-d H:i:s', time()), 'status' => 'linked']);

                //Redirect back to the same page to refresh the user data of the logged in user
                return redirect()->route('user.link');

            } elseif ($linking_request->status == "rejected") {
                //If its rejected, delete it. (Just call the cancel function to avoid duplicate code)
                $this->cancel($request);
            }
        }


        return view('user.link.index', array("linking_in_progress" => $linking_in_progress, 'byond_key' => $byond_key));
    }

    /**
     * Delete all pending linking requests for the user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request)
    {
        //Set the deleted_at column where the forum_id matches the forum id of the logged in user and the deleted_at column is not set --> Should be only one
        DB::connection('server')->table('player_linking')
            ->where('forum_id', '=', $request->user()->user_id)
            ->where('deleted_at', '=', NULL)
            ->update(['deleted_at' => date('Y-m-d H:i:s', time())]);

        return redirect()->route('user.link');
    }

    /**
     * @param Request $request
     *
     * Add a new linking request to the db
     * Then redirect the user back to the linking page
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'Byond_Username' => 'required'
        ]);

        $ckey = Helpers::sanitize_ckey($request->input("Byond_Username"));

        #Check if a player with that ckey exists
        $player_count = ServerPlayer::where('ckey',$ckey)->count();

        if($player_count != 1){
            return redirect()->route('user.link')->withErrors(array("Could not find player with the ckey".$ckey.". You need to join on the server before you can link your account."));
        }

        //Only add a new linking request if there is no existing one (where the deleted_at date is not set)
        if (DB::connection('server')->table('player_linking')->where('forum_id', '=', $request->user()->user_id)->where('deleted_at', '=', NULL)->count() == 0) {

            DB::connection('server')->table('player_linking')
                ->insert([
                    ['forum_id' => $request->user()->user_id,
                        'forum_username_short' => $request->user()->username,
                        'forum_username' => $request->user()->username,
                        'player_ckey' => $ckey,
                        'status' => 'new',
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ]
                ]);

            return redirect()->route('user.link');
        } else {
            return redirect()->route('user.link')->withErrors(array("Could not create linking request, because there is already a active linking request"));
        }

    }
}
