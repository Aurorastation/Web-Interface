<?php

/**
 * Copyright (c) 2016 'Werner Maisl'
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

class CreateCciaactionlist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('server')->create('ccia_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->enum('type',array('injunction','suspension','warning','other'));
            $table->string('issuedby');
            $table->text('details');
            $table->string('url');
            $table->date('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::connection('server')->create('ccia_action_char', function (Blueprint $table) {
            $table->integer('action_id')->unsigned();
            $table->integer('char_id');

            $table->foreign('action_id')
                ->references('id')
                ->on('ccia_actions')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('char_id')
                ->references('id')
                ->on('characters')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['action_id','char_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('server')->drop('ccia_action_char');
        Schema::connection('server')->drop('ccia_actions');
    }
}
