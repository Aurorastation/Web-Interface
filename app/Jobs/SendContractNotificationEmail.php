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
namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SyndieContract;
use App\Services\Notification\Mailer;
use App\Models\SiteRole;

class SendContractNotificationEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $contract;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SyndieContract $contract, $type = 'unset')
    {
        $this->contract = $contract;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contract_id = $this->contract->contract_id;

        //Get the subscribed users for the contract
        $subscribed_users = $this->contract->get_subscribers();
        $mailer = new Mailer();
        foreach($subscribed_users as $user)
        {
            $mailer->send_contract_notification($user,$contract_id,$this->type);
        }

        //Also notifiy the contract Moderators for new contracts
        if($this->type == 'new')
        {
            $contract_mods_role = SiteRole::findOrFail(config('aurora.role_syndie_contract_mods_id'));
            $contract_mods_users = $contract_mods_role->get_users();
            foreach($contract_mods_users as $mod)
            {
                $mailer->send_contract_new_mod($mod,$contract_id);
            }
        }
    }
}
