<?php

/**
 * Copyright (c) 2016 'Arrow768', 'Sierra Brown'
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
use App\Models\SiteRole;
use App\Models\SitePermission;

class CreatePermissionRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('wi')->create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::connection('wi')->create('permissions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::connection('wi')->create('permission_role', function(Blueprint $table)
        {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id','role_id']);
        });

        Schema::connection('wi')->create('role_user', function(Blueprint $table)
        {
            $table->integer('role_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['role_id','user_id']);
        });

//        syndie_contract_moderate -> Allows to moderate Syndicate Contracts
        $syndie_contract_moderate = new SitePermission();
        $syndie_contract_moderate->name = "syndie_contract_moderate";
        $syndie_contract_moderate->label = "Allows to moderate Syndicate Contracts";
        $syndie_contract_moderate->save();

//        site_admin_menu_view -> Show the admin menu
        $site_admin_menu_view = new SitePermission();
        $site_admin_menu_view->name = "site_admin_menu_view";
        $site_admin_menu_view->label = "Show the admin menu";
        $site_admin_menu_view->save();

//        server_players_whitelists_show -> Show the whitelists
        $server_players_whitelists_show = new SitePermission();
        $server_players_whitelists_show->name = "server_players_whitelists_show";
        $server_players_whitelists_show->label = "Show the whitelists";
        $server_players_whitelists_show->save();

//        server_players_whitelists_edit ->  Edit the whitelists
        $server_players_whitelists_edit = new SitePermission();
        $server_players_whitelists_edit->name = "server_players_whitelists_edit";
        $server_players_whitelists_edit->label = "Edit the whitelists";
        $server_players_whitelists_edit->save();

//        server_permissions_show -> Show the permissions on the server
        $server_permissions_show = new SitePermission();
        $server_permissions_show->name = "server_permissions_show";
        $server_permissions_show->label = "Show the permissions on the server";
        $server_permissions_show->save();

//        server_permissions_edit -> Edit the permissions on the server
        $server_permissions_edit = new SitePermission();
        $server_permissions_edit->name = "server_permissions_edit";
        $server_permissions_edit->label = "Edit the permissions on the server";
        $server_permissions_edit->save();

//        site_staff_roster_show -> Show the staff roster
        $site_staff_roster_show = new SitePermission();
        $site_staff_roster_show->name = "site_staff_roster_show";
        $site_staff_roster_show->label = "Show the staff roster";
        $site_staff_roster_show->save();

//        site_staff_roster_edit -> Edit the staff roster
        $site_staff_roster_edit = new SitePermission();
        $site_staff_roster_edit->name = "site_staff_roster_edit";
        $site_staff_roster_edit->label = "Edit the staff roster";
        $site_staff_roster_edit->save();

//        admin_character_records_show -> Show the Character Records
        $admin_character_records_show = new SitePermission();
        $admin_character_records_show->name = "admin_character_records_show";
        $admin_character_records_show->label = "Show the Character Records";
        $admin_character_records_show->save();

//        admin_character_records_edit -> Edit the Character Records
        $admin_character_records_edit = new SitePermission();
        $admin_character_records_edit->name = "admin_character_records_edit";
        $admin_character_records_edit->label = "Allows to moderate Syndicate Contracts";
        $admin_character_records_edit->save();

//        admin_do_recorder_logs_show -> Show the DO recorder logs
        $admin_do_recorder_logs_show = new SitePermission();
        $admin_do_recorder_logs_show->name = "admin_do_recorder_logs_show";
        $admin_do_recorder_logs_show->label = "Show the DO recorder logs";
        $admin_do_recorder_logs_show->save();

//        server_stats_show -> Show the admin stats
        $server_stats_show = new SitePermission();
        $server_stats_show->name = "server_stats_show";
        $server_stats_show->label = "Show the admin stats";
        $server_stats_show->save();

//        site_roles_show -> Show the site roles
        $site_roles_show = new SitePermission();
        $site_roles_show->name = "site_roles_show";
        $site_roles_show->label = "Show the site roles";
        $site_roles_show->save();

//        site_roles_edit -> Edit the site roles
        $site_roles_edit = new SitePermission();
        $site_roles_edit->name = "site_roles_edit";
        $site_roles_edit->label = "Edit the site roles";
        $site_roles_edit->save();

//        site_permissions_show -> Show the site permissions
        $site_permissions_show = new SitePermission();
        $site_permissions_show->name = "site_permissions_show";
        $site_permissions_show->label = "Show the site permissions";
        $site_permissions_show->save();

//        site_permissions_edit -> Edit the site permissions
        $site_permissions_edit = new SitePermission();
        $site_permissions_edit->name = "site_permissions_edit";
        $site_permissions_edit->label = "Edit the site permissions";
        $site_permissions_edit->save();

//        site_logs_show -> Show the site logs
        $site_logs_show = new SitePermission();
        $site_logs_show->name = "site_logs_show";
        $site_logs_show->label = "Show the site logs";
        $site_logs_show->save();

//        server_logs_show -> Show the server logs
        $server_logs_show = new SitePermission();
        $server_logs_show->name = "server_logs_show";
        $server_logs_show->label = "Show the server logs";
        $server_logs_show->save();

//        server_forms_show -> Show the corporate Form Database
        $server_forms_show = new SitePermission();
        $server_forms_show->name = "server_forms_show";
        $server_forms_show->label = "Show the corporate Form Database";
        $server_forms_show->save();

//        server_forms_edit -> Edit the corporate Form Database
        $server_forms_edit = new SitePermission();
        $server_forms_edit->name = "server_forms_edit";
        $server_forms_edit->label = "Edit the corporate Form Database";
        $server_forms_edit->save();

//        server_players_show -> Show the player page (Alone Useless -> Needs some of the perms below)
        $server_players_show = new SitePermission();
        $server_players_show->name = "server_players_show";
        $server_players_show->label = "Show the player page";
        $server_players_show->save();

//        server_players_warnings_show -> Show a players warnings
        $server_players_warnings_show = new SitePermission();
        $server_players_warnings_show->name = "server_players_warnings_show";
        $server_players_warnings_show->label = "Show a players warnings";
        $server_players_warnings_show->save();

//        server_players_warnings_edit -> Edit a players warnings
        $server_players_warnings_edit = new SitePermission();
        $server_players_warnings_edit->name = "server_players_warnings_edit";
        $server_players_warnings_edit->label = "Edit a players warnings";
        $server_players_warnings_edit->save();

//        server_players_notes_show -> Show a players notes
        $server_players_notes_show = new SitePermission();
        $server_players_notes_show->name = "server_players_notes_show";
        $server_players_notes_show->label = "Show a players notes";
        $server_players_notes_show->save();

//        server_players_notes_edit -> Edit a players notes
        $server_players_notes_edit = new SitePermission();
        $server_players_notes_edit->name = "server_players_notes_edit";
        $server_players_notes_edit->label = "Edit a players notes";
        $server_players_notes_edit->save();

//        admin_do_actions_show -> Show the DO Actions
        $admin_do_actions_show = new SitePermission();
        $admin_do_actions_show->name = "admin_do_actions_show";
        $admin_do_actions_show->label = "Show the current DO Actions";
        $admin_do_actions_show->save();

//        admin_do_actions_edit -> Edit the DO Actions
        $admin_do_actions_edit = new SitePermission();
        $admin_do_actions_edit->name = "admin_do_actions_edit";
        $admin_do_actions_edit->label = "Edit the DO Actions";
        $admin_do_actions_edit->save();
        
        // ccia_general_notice_edit -> Edit the CCIA General Notice list
        $ccia_general_notice_edit = new SitePermission();
        $ccia_general_notice_edit->name = "ccia_general_notice_edit";
        $ccia_general_notice_edit->label = "Edit the CCIA General Notice list";
        $ccia_general_notice_edit->save();


        $administrators = new SiteRole();
        $administrators->name = "administrators";
        $administrators->label = "Administrators";
        $administrators->description = "Can do everything";
        $administrators->save();

        $administrators->givePermissionTo($syndie_contract_moderate);
        $administrators->givePermissionTo($site_admin_menu_view);
        $administrators->givePermissionTo($server_permissions_show);
        $administrators->givePermissionTo($server_permissions_edit);
        $administrators->givePermissionTo($site_staff_roster_show);
        $administrators->givePermissionTo($site_staff_roster_edit);
        $administrators->givePermissionTo($admin_character_records_show);
        $administrators->givePermissionTo($admin_character_records_edit);
        $administrators->givePermissionTo($admin_do_recorder_logs_show);
        $administrators->givePermissionTo($server_stats_show);
        $administrators->givePermissionTo($site_roles_show);
        $administrators->givePermissionTo($site_roles_edit);
        $administrators->givePermissionTo($site_permissions_show);
        $administrators->givePermissionTo($site_permissions_edit);
        $administrators->givePermissionTo($site_logs_show);
        $administrators->givePermissionTo($server_logs_show);
        $administrators->givePermissionTo($server_forms_show);
        $administrators->givePermissionTo($server_forms_edit);
        $administrators->givePermissionTo($server_players_show);
        $administrators->givePermissionTo($server_players_whitelists_show);
        $administrators->givePermissionTo($server_players_whitelists_edit);
        $administrators->givePermissionTo($server_players_warnings_show);
        $administrators->givePermissionTo($server_players_warnings_edit);
        $administrators->givePermissionTo($server_players_notes_show);
        $administrators->givePermissionTo($server_players_notes_edit);
        $administrators->givePermissionTo($admin_do_actions_show);
        $administrators->givePermissionTo($admin_do_actions_edit);
        $administrators->givePermissionTo($ccia_general_notice_edit);

        $moderators = new SiteRole();
        $moderators->name = "moderators";
        $moderators->label = "Moderators";
        $moderators->description = "Like a admin but not as powerful";
        $moderators->save();

        $moderators->givePermissionTo($syndie_contract_moderate);
        $moderators->givePermissionTo($site_admin_menu_view);
        $moderators->givePermissionTo($site_staff_roster_show);
        $moderators->givePermissionTo($admin_character_records_show);
        $moderators->givePermissionTo($server_stats_show);
        $moderators->givePermissionTo($site_roles_show);
        $moderators->givePermissionTo($site_permissions_show);
        $moderators->givePermissionTo($server_forms_show);
        $moderators->givePermissionTo($server_players_show);
        $moderators->givePermissionTo($server_players_whitelists_show);
        $moderators->givePermissionTo($server_players_warnings_show);
        $moderators->givePermissionTo($server_players_warnings_edit);
        $moderators->givePermissionTo($server_players_notes_show);
        $moderators->givePermissionTo($server_players_notes_edit);

        $duty_offiers = new SiteRole();
        $duty_offiers->name = "duty_officers";
        $duty_offiers->label = "Duty Officers";
        $duty_offiers->description = "Something between a moderator and a paper pusher";
        $duty_offiers->save();

        $duty_offiers->givePermissionTo($site_admin_menu_view);
        $duty_offiers->givePermissionTo($admin_character_records_show);
        $duty_offiers->givePermissionTo($admin_character_records_edit);
        $duty_offiers->givePermissionTo($admin_do_recorder_logs_show);
        $duty_offiers->givePermissionTo($server_forms_show);
        $duty_offiers->givePermissionTo($server_forms_edit);
        $duty_offiers->givePermissionTo($server_players_show);
        $duty_offiers->givePermissionTo($server_players_whitelists_show);
        $duty_offiers->givePermissionTo($admin_do_actions_show);
        $duty_offiers->givePermissionTo($admin_do_actions_edit);
        $duty_offiers->givePermissionTo($ccia_general_notice_edit);

        $whitelist_managers = new SiteRole();
        $whitelist_managers->name = "whitelist_managers";
        $whitelist_managers->label = "Whitelist Managers";
        $whitelist_managers->description = "Manages the White-Lists";
        $whitelist_managers->save();

        $whitelist_managers->givePermissionTo($site_admin_menu_view);
        $whitelist_managers->givePermissionTo($server_players_show);
        $whitelist_managers->givePermissionTo($server_players_whitelists_show);
        $whitelist_managers->givePermissionTo($server_players_whitelists_edit);

        $contract_managers = new Siterole();
        $contract_managers->name = "contract_managers";
        $contract_managers->label = "Contract Managers";
        $contract_managers->description = "Manages the Syndicate Contracts";
        $contract_managers->save();

        $contract_managers->givePermissionTo($syndie_contract_moderate);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('wi')->drop('permission_role');
        Schema::connection('wi')->drop('role_user');
        Schema::connection('wi')->drop('roles');
        Schema::connection('wi')->drop('permissions');
    }
}
