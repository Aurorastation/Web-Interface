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
use App\Models\CCIAAction;
use Illuminate\Support\Facades\DB;

class ServerCharacter extends Model
{
    use SoftDeletes;
    protected $connection = 'server';
    protected $table = 'characters';
    protected $primaryKey = 'id';
    public $timestamps = FALSE;

    public function cciaactions()
    {
        return $this->belongsToMany(CCIAAction::class,'ccia_action_char','char_id','action_id');
    }

    //Get the number of CCIA Actions that are active for this character
    public function active_ccia_action_count()
    {
        return $this->cciaactions()->where(function($query) {
            $query->where("expires_at", ">=", DB::raw("NOW()"))->orWhereNull('expires_at');
        })->count();
    }
}
