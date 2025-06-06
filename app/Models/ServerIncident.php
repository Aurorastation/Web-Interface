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

class ServerIncident extends Model
{
    use SoftDeletes;
    protected $connection = 'server';
    protected $table = 'character_incidents';
    protected $fillable = ['UID', 'datetime', 'notes', 'charges', 'evidence', 'arbiters', 'brig_sentence', 'fine', 'felondy', 'created_by', 'game_id', 'created_at'];
    protected $primaryKey = 'id';
    public $timestamps = TRUE;
}
