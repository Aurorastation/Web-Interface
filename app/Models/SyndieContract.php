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

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyndieContract extends Model
{
    use SoftDeletes;

    protected $connection = 'server';
    protected $table = 'syndie_contracts';
    protected $fillable = ['contractee_name', 'title', 'description', 'reward_credits', 'reward_other'];
    protected $primaryKey = 'contract_id';
    protected $dates = ['deleted_at'];

    /**
     * Returns an Array of all the users subscribed to the contract
     *
     * @return A Array with the user_id´s subscribed to the contract
     */
    public function get_subscribers()
    {
        $users = DB::connection($this->connection)->table('syndie_contracts_subscribers')->where('contract_id', $this->contract_id)->pluck('user_id');
        return $users;
    }

    /**
     * Checks weather a user is subscribed to the contract or not
     *
     * @param $user_id The userid the subscribed status should be returned for
     *
     * @return boolean true if the user is subscribed false if not
     */
    public function is_subscribed($user_id)
    {
        $count = DB::connection($this->connection)->table('syndie_contracts_subscribers')->where('contract_id', $this->contract_id)->where('user_id', $user_id)->count();
        if ($count > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Adds subscribers to the contract
     *
     * @param $subscribers A array or integer with the id of the users that should be subscribed to the contract
     */
    public function add_subscribers($subscribers)
    {
        //Check if its an array or a integer
        if (is_array($subscribers)) {
            //Generate array with insert data
            $query_data = array();
            foreach ($subscribers as $subscriber) {
                $query_data[] = ['contract_id' => $this->contract_id, 'user_id' => $subscriber];
            }
            DB::connection($this->connection)->table('syndie_contracts_subscribers')->insert($query_data);
        } else {
            //Insert directly into the db
            DB::connection($this->connection)->table('syndie_contracts_subscribers')->insert(['contract_id' => $this->contract_id, 'user_id' => $subscribers]);
        }
    }

    /**
     * Removes subscribers from the contract
     *
     * @param $subscribers A array or ineger with the id of the users that should be unsubscribed from the contract
     */
    public function remove_subscribers($subscribers)
    {
        //Check if its an array or a integer
        if (is_array($subscribers)) {
            DB::connection($this->connection)->table('syndie_contracts_subscribers')->where('contract_id', $this->contract_id)->whereIn('user_id', $subscribers)->delete();
        } else {
            //Insert directly into the db
            DB::connection($this->connection)->table('syndie_contracts_subscribers')->where('contract_id', $this->contract_id)->where('user_id', $subscribers)->delete();
        }
    }

    /**
     * Tries to approve a contract if possible. Checks the required preconditions.
     *
     * @param $user User who approved the contract
     */
    public function mod_approve(User $user)
    {
        if (!in_array($this->status, ['new', 'mod-nok'])) {
            abort("420", "You can only approve new or rejected contracts");
        }

        if (in_array($this->status, ['open'])) {
            abort("420", "The contract has already been approved");
        }

        $this->status = "open";
        $this->save();
        Log::notice('perm.contracts.approve - Contract has been approved', ['user_id' => $user->id, 'contract_id' => $this->contract_id]);
    }

    /**
     * Rejects a Contract
     *
     * @param $user User who rejected the contract
     */
    public function mod_reject(User $user)
    {
        if (in_array($this->status, ['mod-nok'])) {
            abort("420", "The Contract has already been rejected");
        }

        $this->status = "mod-nok";
        $this->save();

        Log::notice('perm.contracts.reject - Contract has been rejected', ['user_id' => $user->user_id, 'contract_id' => $this->contract_id]);
    }

    /**
     * Deletes a contract
     *
     * @param $user User who deleted the contract
     */
    public function mod_delete(User $user)
    {
        //Check if the contract is marked as rejected
        if ($this->status != "mod-nok") {
            abort("420", "You can only delete rejected contracts");
        }
        $this->delete();

        Log::notice('perm.contracts.delete - Contract has been deleted', ['user_id' => $user->user_id, 'contract_id' => $this->contract_id]);
    }

    public function comments()
    {
        return $this->hasMany('App\Models\SyndieContractComment', 'contract_id');
    }

    public function objectives()
    {
        return $this->hasMany('App\Models\SyndieContractObjective', 'contract_id');
    }
}
