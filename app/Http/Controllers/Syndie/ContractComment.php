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
use App\Models\SyndieContract;
use App\Models\SyndieContractComment;
use App\Models\SyndieContractObjective;
use App\Jobs\SendContractNotificationEmail;
use Illuminate\Support\Facades\Log;

class ContractComment extends Controller
{
    public function getAdd(Request $request, $contract)
    {
        $contract = SyndieContract::findOrFail($contract);
        $objectives = SyndieContractObjective::where('contract_id', '=', $contract->contract_id)->get();

        return view('syndie.comment.add', ["contract" => $contract, "objectives" => $objectives]);
    }

    public function postAdd(Request $request, $contract)
    {
        //dd($request->all());
        //Get the Contract
        $contract = SyndieContract::findOrFail($contract);

        //Validate that the type is present and that user has the permission to use it
        $type = $request->input("type");
        if ($request->user()->can('syndie_contract_moderate')) {
            $validate_array = [
                'type' => 'required|in:ic,ic-failrep,ic-comprep,ic-cancel,ooc,mod-ooc,mod-author,approve,reject'
            ];
        } else if ($request->user()->user_id == $contract->contractee_id) {
            $validate_array = [
                'type' => 'required|in:ic,ic-cancel,ooc,mod-author'
            ];
        } else {
            $validate_array = [
                'type' => 'required|in:ic,ic-failrep,ic-comprep'
            ];
        }
        $this->validate($request, $validate_array);

        //Create new Comment but dont save it yet

        $comment = new SyndieContractComment();
        $comment->contract_id = $contract->contract_id;
        $comment->type = $type;
        $comment->commentor_id = $request->user()->user_id;

        //Get the type and validate the required options for the type
        $type = $request->input("type");

        switch ($type) {
            case "ic":
            case "ic-failrep":
                $this->validate($request, [
                    'title' => 'required|max:50',
                    'comment' => 'required',
                    'commentor_name' => 'required|max:50'
                ]);

                $comment->title = $request->input('title');
                $comment->comment = $request->input('comment');
                $comment->commentor_name = $request->input('commentor_name');
                $comment->save();
                break;


            case "ic-cancel":
                $this->validate($request, [
                    'title' => 'required|max:50',
                    'comment' => 'required',
                    'commentor_name' => 'required|max:50'
                ]);

                $comment->title = $request->input('title');
                $comment->comment = $request->input('comment');
                $comment->commentor_name = $request->input('commentor_name');
                $comment->save();

                $contract->status = "closed";
                $contract->save();
                break;

            case "approve":
            case "reject":
                $this->validate($request, [
                    'title' => 'required|max:50',
                    'comment' => 'required'
                ]);
                if($type == "approve")
                    $contract->mod_approve($request->user());
                if($type == "reject")
                    $contract->mod_reject($request->user());
                $comment->type = "mod-author";
                $comment->title = $request->input('title');
                $comment->comment = $request->input('comment');
                $comment->commentor_name = $request->user()->username;
                $comment->save();
                break;

            case "ooc":
            case "mod-ooc":
            case "mod-author":
                $this->validate($request, [
                    'title' => 'required|max:50',
                    'comment' => 'required'
                ]);

                $comment->title = $request->input('title');
                $comment->comment = $request->input('comment');
                $comment->commentor_name = $request->user()->username;
                $comment->save();
                break;

            case "ic-comprep":
                if($contract->objectives()->count() > 0){
                    $this->validate($request, [
                        'title' => 'required|max:50',
                        'comment' => 'required',
                        'commentor_name' => 'required|max:50',
                        'objectives' => 'required',
                        'agents' => 'required'
                    ]);

                    $comment->title = $request->input('title');
                    $comment->comment = $request->input('comment');
                    $comment->commentor_name = $request->input('commentor_name');
                    $comment->report_status = "waiting-approval";
                    $comment->save();

                    //Now sync the objectives and agents
                    $comment->objectives()->sync($request->input('objectives'));
                    $comment->completers()->sync($request->input('agents'));
                }
                else
                {
                    $this->validate($request, [
                        'title' => 'required|max:50',
                        'comment' => 'required',
                        'commentor_name' => 'required|max:50',
                        'agents' => 'required'
                    ]);

                    $comment->title = $request->input('title');
                    $comment->comment = $request->input('comment');
                    $comment->commentor_name = $request->input('commentor_name');
                    $comment->report_status = "waiting-approval";
                    $comment->save();

                    //Now sync the objectives and agents
                    $comment->completers()->sync($request->input('agents'));
                }

                break;
        }
        $this->dispatch(new SendContractNotificationEmail($contract, $type));

        Log::notice('perm.syndie.contractcomment.add - Contract Comment has been added',['user_id' => $request->user()->user_id, 'comment_id' => $comment->comment_id]);
        return redirect()->route('syndie.contracts.show', ['contract' => $comment->contract_id]);
    }

    public function confirmopen(Request $request, $comment)
    {
        $comment = SyndieContractComment::findOrFail($comment);
        $contract = SyndieContract::findOrFail($comment->contract_id);
        $objectives = $comment->objectives()->get();

        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $comment->report_status = 'accepted';
        $comment->save();

        //Close Objectives
        $this->closeContractObjectives($objectives);
        $this->createCompletionComment($comment,$contract,$objectives,"confirmopen");

        Log::notice('perm.syndie.contractcomment.confirmopen - Contract Comment has been confirmed open',['user_id' => $request->user()->user_id, 'comment_id' => $comment->comment_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $comment->contract_id]);
    }

    public function confirmclose(Request $request, $comment)
    {
        $comment = SyndieContractComment::findOrFail($comment);
        $contract = SyndieContract::findOrFail($comment->contract_id);
        $objectives = $comment->objectives()->get();

        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $comment->report_status = 'accepted';
        $comment->save();

        $contract->status = "closed";
        $contract->save();

        //Close Objectives
        $this->closeContractObjectives($objectives);
        $this->createCompletionComment($comment,$contract,$objectives,"confirmclose");

        Log::notice('perm.syndie.contractcomment.confirmclose - Contract Comment has been confirmed closed',['user_id' => $request->user()->user_id, 'comment_id' => $comment->comment_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $comment->contract_id]);
    }

    public function reject(Request $request, $comment)
    {
        $comment = SyndieContractComment::findOrFail($comment);
        $contract = SyndieContract::findOrFail($comment->contract_id);
        $objectives = $comment->objectives()->get();

        if($request->user()->cannot('syndie_contract_moderate') && $contract->contractee_id != $request->user()->id)
        {
            abort('403','You do not have the required permission');
        }

        $comment->report_status = 'rejected';
        $comment->save();

        $this->createCompletionComment($comment,$contract,$objectives,"reject");

        Log::notice('perm.syndie.contractcomment.reject - Contract Comment has been rejected',['user_id' => $request->user()->user_id, 'comment_id' => $comment->comment_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $comment->contract_id]);
    }

    public function delete(Request $request, $comment)
    {
        $comment = SyndieContractComment::findOrFail($comment);

        if($request->user()->cannot('syndie_contract_moderate'))
        {
            abort('403','You do not have the required permission');
        }

        $comment->delete();

        Log::notice('perm.syndie.contractcomment.delete - Contract Comment has been deleted',['user_id' => $request->user()->user_id, 'comment_id' => $comment->comment_id]);

        return redirect()->route('syndie.contracts.show', ['contract' => $comment->contract_id]);
    }

    private function closeContractObjectives($objectives)
    {
        foreach($objectives as $objective)
        {
            $objective->status = "closed";
            $objective->save();
        }
    }

    private function createCompletionComment(SyndieContractComment $comment, SyndieContract $contract, $objectives,$type = "confirmopen")
    {
        $system_comment = new SyndieContractComment();
        $system_comment->contract_id = $comment->contract_id;
        $system_comment->commentor_id = 0;
        $system_comment->commentor_name = "System";
        $system_comment->title = "Contract Completion";
        $system_comment->type = "ic";

        switch($type)
        {
            case "confirmopen":
                $system_comment->comment =
"#### Completion Report Approved
The Completion-Report with the title $comment->title by $comment->commentor_name has been **approved** by the contract author.

The contract has been left open.
";
                break;
            case "confirmclose":
                $system_comment->comment =
"#### Completion Report Approved
The Completion-Report with the title $comment->title by $comment->commentor_name has been **approved** by the contract author.

The contract has been closed.
";

                break;
            case "reject":
                $system_comment->comment =
"#### Completion Report Approved
The Completion-Report with the title $comment->title by $comment->commentor_name has been **rejected** the contract author.
";
                break;
        }

        if($type == "confirmopen" || $type == "confirmclose")
        {
            $system_comment->comment .= "The following payment has been transferred to the contractor:";
            $system_comment->comment .= "
* Base Reward: ". $contract->reward_other;
            foreach($objectives as $objective)
            {
                $system_comment->comment .= "
* Objective Reward - \"".$objective->title."\": ". $objective->reward_other;
            }
        }

        $system_comment->save();
    }
}
