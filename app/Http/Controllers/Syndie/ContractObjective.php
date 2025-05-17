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

use App\Models\SyndieContractComment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SyndieContract;
use App\Models\SyndieContractObjective;
use Illuminate\Support\Facades\Log;

class ContractObjective extends Controller
{
    public function view(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = SyndieContract::findOrFail($objective->contract_id);
        return view('syndie.objective.view', ['objective' => $objective, 'contract' => $contract]);
    }

    public function getAdd(Request $request, $contract)
    {
        $contract = SyndieContract::findOrFail($contract);
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        return view('syndie.objective.add', ['contract' => $contract]);
    }

    public function postAdd(Request $request, $contract)
    {
        $contract = SyndieContract::findOrFail($contract);
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'reward' => 'required'
        ]);

        $objective = new SyndieContractObjective();
        $objective->contract_id = $contract->contract_id;
        $objective->status = "open";
        $objective->title = $request->input('title');
        $objective->description = $request->input('description');
        //$objective->reward_credits = $request->input('reward');
        $objective->reward_other = $request->input('reward');
        $objective->save();

        Log::notice('perm.syndie.objective.add - Contract Objective has been added',['user_id' => $request->user()->user_id, 'objective_id' => $objective->objective_id]);

        return redirect()->route('syndie.contracts.show',['contract'=>$objective->contract_id]);
    }

    public function getEdit(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = $objective->contract()->get();
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        return view('syndie.objective.edit', ['objective' => $objective,'contract' => $contract]);
    }

    public function postEdit(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = $objective->contract()->get();
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $objective->title = $request->input('title');
        $objective->description = $request->input('description');
        $objective->save();

        Log::notice('perm.syndie.objective.edit - Contract Objective has been edited',['user_id' => $request->user()->user_id, 'objective_id' => $objective->objective_id]);

        return redirect()->route('syndie.contracts.show',['contract'=>$objective->contract_id]);
    }

    public function close(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = $objective->contract()->get();
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $objective->status = "closed";
        $objective->save();

        Log::notice('perm.syndie.objective.close - Contract Objective has been closed',['user_id' => $request->user()->user_id, 'objective_id' => $objective->objective_id]);

        return redirect()->route('syndie.contracts.show',['contract'=>$objective->contract_id]);
    }

    public function open(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = $objective->contract()->get();
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $objective->status = "open";
        $objective->save();

        Log::notice('perm.syndie.objective.open - Contract Objective has been opened',['user_id' => $request->user()->user_id, 'objective_id' => $objective->objective_id]);

        return redirect()->route('syndie.contracts.show',['contract'=>$objective->contract_id]);
    }

    public function delete(Request $request, $objective)
    {
        $objective = SyndieContractObjective::findOrFail($objective);
        $contract = $objective->contract()->get();
        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $objective->status = "deleted";
        $objective->save();
        $objective->delete();

        Log::notice('perm.syndie.objective.delete - Contract Objective has been deleted',['user_id' => $request->user()->user_id, 'objective_id' => $objective->objective_id]);

        return redirect()->route('syndie.contracts.show',['contract'=>$objective->contract_id]);
    }
}
