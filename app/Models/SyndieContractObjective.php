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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SyndieContractObjective extends Model
{
    use SoftDeletes;

    /* Status Codes:
     * open -> The objective is uncompleted
     * closed -> The objective is completed
     */

    protected $connection = 'server';
    protected $table = 'syndie_contracts_objectives';
    protected $fillable = ['title', 'description', 'reward', 'reward_other'];
    protected $primaryKey = 'objective_id';
    protected $dates = ['deleted_at'];

    public function comments()
    {
        return $this->belongsToMany('App\Models\SyndieContractComment','syndie_contract_comment_objectives');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\SyndieContract','contract_id','contract_id');
    }
}
