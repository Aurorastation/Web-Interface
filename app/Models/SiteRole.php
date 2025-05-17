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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;


class SiteRole extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'label', 'description'];
    protected $connection = 'wi';

    public function permissions()
    {
        return $this->belongsToMany(SitePermission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function get_users($user_models = FALSE)
    {
        $users = DB::connection($this->connection)->table('role_user')->where('role_id', $this->id)->pluck('user_id');

        if (!$user_models) {
            return $users;
        } else {
            $user_data = array();
            foreach ($users as $user) {
                $user_data[] = User::findOrFail($user);
            }
            return Collection::make($user_data);
        }
    }

    public function givePermissionTo(SitePermission $permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->save(
                SitePermission::whereName($permission)->firstOrfail
            );
        }

        return $this->permissions()->save($permission);
    }
}
