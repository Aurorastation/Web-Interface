<?php

/**
 * Copyright (c) 2016 'Arrow768'
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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyndieContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('server')->create('syndie_contracts', function (Blueprint $table) {
            $table->increments('contract_id');
            $table->integer('contractee_id'); // ID of the person offering the contract
            $table->string('contractee_name'); // Name of the Entity offering the contract
            $table->enum('status',['new','open','mod-nok','completed','closed','reopened','canceled']);
            /* Status Codes:
             * new -> Newly created
             * open -> Contracted confirmed by contract mod
             * mod-nok -> Contract denied by contract mod
             * completed -> Completion report submitted by player
             *  -> closed -> Rewards assigned -> Contract closed
             * canceled -> The contract has been canceled by the contractee
             */
            $table->string('title');
            $table->text('description');
            $table->integer('reward_credits')->nullable();
            $table->text('reward_other')->nullable();
            $table->integer('completer_id')->nullable();
            $table->text('completer_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('server')->create('syndie_contracts_comments', function (Blueprint $table) {
            $table->increments('comment_id');
            $table->integer('contract_id')->unsigned();
            $table->integer('commentor_id')->unsigned();
            $table->string('commentor_name');
            $table->string('title');
            $table->text('comment');
            $table->string('image_name');
            $table->enum('type',array('mod-author','mod-ooc','ic','ic-comprep','ic-failrep','ic-cancel','ooc'));
            /* Message Types:
             * mod-author: Only visible to mods and authors
             * mod-ooc: OOC comment of a contract mod
             * ic: IC Comment
             * ic-comprep: Report of contract completion
             * ic-failrep: Report of failed operation
             * ic-cancel: Contract has been canceled
             * ooc: OOC Comment
             */
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('server')->drop('syndie_contracts_comments');
        Schema::connection('server')->drop('syndie_contracts');
    }
}
