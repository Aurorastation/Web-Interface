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
namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ServerAdmin;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /*
    Route::get('', ['as' => 'server.permissions.index', 'uses'=>'Server\PermissionController@index']);
    Route::get('/{permission_id}/', ['as' => 'server.permissions.show', 'uses'=>'Server\PermissionController@show']);
    Route::get('/add', ['as' => 'server.permissions.add.get', 'uses'=>'Server\PermissionController@getAdd']);
    Route::post('/add', ['as' => 'server.permissions.add.get', 'uses'=>'Server\PermissionController@postAdd']);
    Route::get('/{permission_id}/remove', ['as' => 'server.permissions.remove', 'uses'=>'Server\PermissionController@remove']);
    Route::get('/{permission_id}/add_flag/{flag}', ['as' => 'servers.permissions.flag.add', 'uses'=>'Server\PermissionController@addFlag']);
    Route::get('/{permission_id}/remove_flag/{flag}', ['as' => 'servers.permissions.flag.remove', 'uses'=>'Server\PermissionController@removeFlag']);
    */



    public function index(Request $request)
    {
        if($request->user()->cannot('server_permissions_show'))
            abort(403);

        //Get admins from the admins table
        if($request->has('s'))
        {
            DB::connection('server')->enableQueryLog();
            $ckey = $request->input('ckey');
            $rank = $request->input('rank');
            $admins = ServerAdmin::where('rank', '!=', 'Removed')
                ->Where('ckey','LIKE','%'.$ckey.'%')
                ->Where('rank','LIKE','%'.$rank.'%')
                ->get();
        }
        else
        {
            $admins = ServerAdmin::where('rank', '!=', 'Removed')->get();
        }

        //Pass them to the view
        return view('server.permissions.index', ['flags' => ServerAdmin::$server_flags,'admins' => $admins,'input'=>$request->all()]);
    }

    public function show(Request $request, $permission_id)
    {
        //Not needed right now
    }

    public function getAdd(Request $request)
    {

    }

    public function postAdd(Request $request)
    {

    }

    public function remove(Request $request, $permission_id)
    {

    }

    public function addFlag(Request $request, $permission_id, $flag)
    {
        //Get the name of the admin
        //Write it to the log table
        //Update the flag
        //redirect back
    }

    public function removeFlag(Request $request, $permission_id, $flag)
    {
        //Get the name of the admin
        //Write it to the log table
        //Update the flag
        //redirect back
    }
}
