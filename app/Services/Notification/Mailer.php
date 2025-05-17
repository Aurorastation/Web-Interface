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

namespace App\Services\Notification;

use Illuminate\Support\Facades\Mail;
use App\Models\SyndieContract;
use App\Models\User;

class Mailer
{

    public function send_contract_notification($forum_user_id, $contract_id, $type)
    {
        $contract = SyndieContract::find($contract_id);
        $forum_user = User::find($forum_user_id);

        $from_name = config('aurora.syndie_contract_from_name');
        $from_address = config('aurora.syndie_contract_from_address');

        //Get user E-Mail
        Mail::send('emails.contract_notification', ['forum_user' => $forum_user, 'contract' => $contract, 'type' => $type], function ($m) use ($forum_user, $contract, $from_name, $from_address) {
            $m->from($from_address, $from_name);
            $m->to($forum_user->email, $forum_user->username);
            $m->subject('Contract Update - ' . $contract->title);
        });
    }

    public function send_contract_new_mod($forum_user_id, $contract_id)
    {
        $contract = SyndieContract::find($contract_id);
        $forum_user = User::find($forum_user_id);

        $from_name = config('aurora.syndie_contract_from_name');
        $from_address = config('aurora.syndie_contract_from_address');

        //Get user E-Mail
        Mail::send('emails.contract_new_mod', ['forum_user' => $forum_user, 'contract' => $contract], function ($m) use ($forum_user, $contract, $from_name, $from_address) {
            $m->from($from_address, $from_name);
            $m->to($forum_user->email, $forum_user->username);
            $m->subject('Contract Update - ' . $contract->title);
        });
    }

}