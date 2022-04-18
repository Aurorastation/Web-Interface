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

namespace App\Http\Controllers\Syndie;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\SyndieContract;
use App\Models\SyndieContractComment;
use App\Models\SyndieContractObjective;
use App\Models\ServerPlayer;
use Yajra\DataTables\Datatables;
use App\Jobs\SendContractNotificationEmail;
use App\Services\Server\Helpers;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        return view('syndie.contract.index');
    }

    public function show($contract)
    {
        $contract = SyndieContract::findOrFail($contract);
        $comments = SyndieContractComment::where('contract_id', '=', $contract->contract_id)->get();
        $objectives = SyndieContractObjective::where('contract_id', '=', $contract->contract_id)->get();
        $contractee = User::find($contract->contractee_id);

        return view('syndie.contract.view', ['contract' => $contract, 'objectives' => $objectives, 'comments' => $comments, 'contractee' => $contractee]);
    }

    public function getContractData(Request $request)
    {
        $contracts = SyndieContract::select(['contract_id', 'title', 'contractee_name', 'status']);
        //For contract mods: Show all contracts
        if ($request->user()->can('syndie_contract_moderate')) {

        } else //For normal users: Show all contracts that have a status of open, assigned, completed, confirmed or that they own
        {
            $contracts->whereIn('status', ['open', 'assigned', 'completed', 'confirmed'])->OrWhere('contractee_id', '=', $request->user()->user_id)->get();
        }

        return Datatables::of($contracts)
            ->editColumn('title', '<b><a href="{{route(\'syndie.contracts.show\',[\'contract\'=>$contract_id])}}">{{$title}}</a></b>')
            ->rawColumns(['title'])
            ->make();
    }

    public function getAdd()
    {
        return view('syndie.contract.add');
    }

    public function postAdd(Request $request)
    {
        $user = \Auth::user();

        $this->validate($request, [
            'contractee_name' => 'required',
            'title' => 'required',
            'description' => 'required',
            'reward' => 'required'
        ]);

        $SyndieContract = new SyndieContract();
        $SyndieContract->contractee_id = $user->user_id;
        $SyndieContract->status = "new";
        $SyndieContract->reward_other = $request->input('reward');
        $SyndieContract->contractee_name = strip_tags($request->input('contractee_name'));
        $SyndieContract->title = strip_tags($request->input('title'));
        $SyndieContract->description = strip_tags($request->input('description'));
        $SyndieContract->save();

        $SyndieContract->add_subscribers($SyndieContract->contractee_id);

        $this->dispatch(new SendContractNotificationEmail($SyndieContract, 'new'));

        Log::notice('perm.contracts.add - Contract has been added', ['user_id' => $request->user()->user_id, 'contract_id' => $SyndieContract->contract_id]);

        return redirect()->route('syndie.contracts.show',["contract"=>$SyndieContract->id]);
    }

    public function getEdit(Request $request, $contract)
    {
        $SyndieContract = SyndieContract::find($contract);

        //Check if the user is the contract owner or a moderator
        if ($request->user()->cannot('syndie_contract_moderate') && $request->user()->user_id != $SyndieContract->contractee_id) {
            abort(502, "You do not have the permission to edit the contract");
        }

        return view('syndie.contract.edit', ['contract' => $SyndieContract]);
    }

    public function postEdit(Request $request, $contract)
    {
        $SyndieContract = SyndieContract::find($contract);

        //Check if the user is the contract owner or a moderator
        if ($request->user()->cannot('syndie_contract_moderate') && $request->user()->user_id != $SyndieContract->contractee_id) {
            abort(502, "You do not have the permission to edit the contract");
        }

        $SyndieContract->title = $request->input('title');
        $SyndieContract->description = $request->input('description');
        $SyndieContract->reward_other = $request->input('reward');
        $SyndieContract->save();

        Log::notice('perm.contracts.edit - Contract has been edited', ['user_id' => $request->user()->user_id, 'contract_id' => $SyndieContract->contract_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $SyndieContract->contract_id]);

    }

    public function approve(Request $request, $contract)
    {
        //Check if player is contract mod
        if ($request->user()->cannot('syndie_contract_moderate')) {
            abort('403', 'You do not have the required permission');
        }

        $SyndieContract = SyndieContract::findOrFail($contract);
        $SyndieContract->mod_approve($request->user());

        return redirect()->route('syndie.contracts.show', ['contract' => $contract]);

    }

    public function reject(Request $request, $contract)
    {
        if ($request->user()->cannot('syndie_contract_moderate')) {
            abort('403', 'You do not have the required permission');
        }

        $SyndieContract = SyndieContract::findOrFail($contract);
        $SyndieContract->mod_reject($request->user());

        return redirect()->route('syndie.contracts.show', ['contract' => $contract]);

    }

    public function delete($contract, Request $request)
    {
        if ($request->user()->cannot('syndie_contract_moderate')) {
            abort('403', 'You do not have the required permission');
        }
        $SyndieContract = SyndieContract::findOrFail($contract);
        $SyndieContract->mod_delete($request->user());

        return redirect()->route('syndie.contracts.index');
    }

    public function getAgentList(Request $request)
    {
        $term = $request->input('term');

        $search_key = Helpers::sanitize_ckey($term);

        //Check for proper input length
        if (strlen($term) >= 3) {
            //Get corresponding ckeys from DB
            $players = ServerPlayer::where('ckey', 'like', '%' . $search_key . '%')->lists('ckey', 'id');
            return json_encode($players);
        }
    }

    public function subscribe($contract, Request $request)
    {
        $SyndieContract = SyndieContract::findOrfail($contract);
        $SyndieContract->add_subscribers($request->user()->user_id);

        Log::notice('perm.contracts.subscribe - User Subscribed to Contract', ['user_id' => $request->user()->user_id, 'contract_id' => $SyndieContract->contract_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $contract]);
    }

    public function unsubscribe($contract, Request $request)
    {
        $SyndieContract = SyndieContract::findOrfail($contract);
        $SyndieContract->remove_subscribers($request->user()->user_id);

        Log::notice('perm.contracts.unsubscribe - User Unsubscribed from Contract', ['user_id' => $request->user()->user_id, 'contract_id' => $SyndieContract->contract_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $contract]);
    }
}
