<?php
/**
 * Copyright (c) 2018 "Arrow768"
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
use Carbon\Carbon;

class ServerPollQuestion extends Model
{
    public $timestamps = FALSE;
    protected $connection = 'server';
    protected $table = 'poll_question';
    protected $fillable = ['polltype', 'starttime', 'endtime', 'question', 'multiplechoiceoptions', 'adminonly', 'createdby_ckey', 'createdby_ip'];
    protected $primaryKey = 'id';

    public function votes()
    {
        return $this->hasMany('App\Models\ServerPollVote', 'pollid', 'id');
    }

    public function options()
    {
        return $this->hasMany('App\Models\ServerPollOption', 'pollid', 'id');
    }

    public function textreplies()
    {
        return $this->hasMany('App\Models\ServerPollTextReply', 'pollid', 'id');
    }

    /**
     * Returns if the question is active or not
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->starttime < Carbon::now() && ($this->endtime = NULL || $this->endtime > Carbon::now());
    }

    /**
     * Returns if the poll has been completed
     *
     * @return bool
     */
    public function isComplete()
    {
        return $this->endtime < Carbon::now();
    }

    /**
     * Checks if the question is visible or not
     *
     * @return bool
     */
    public function isVisible($view_all = false, $token = null)
    {
        //Dont check anything else, if we have view_all
        if($view_all)
            return true;

        //If the result is public, then display it aswell
        if($this->publicresult)
            return true;

        //If the result is not public, then check if we have a matching token
        if($token != null && $token == $this->viewtoken)
            return true;

        return false;
    }
}
