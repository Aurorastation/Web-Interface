<?php
/**
 * Copyright (c) 2016 "Werner Maisl"
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
use HTMLPurifier;

class ServerLibrary extends Model
{
    protected $connection = 'server';
    protected $table = 'library';
    protected $fillable = ['author', 'title', 'content', 'category', 'uploadtime', 'uploader'];
    protected $primaryKey = 'id';
    public $timestamps = FALSE;

    private $purifier;

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed','p, ul, ol, li, h1, h2, h3, h4, h5, h6, br, strong, em, b, i ,u');  //No a[href]
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('Cache.SerializerPath',sys_get_temp_dir());
        $this->purifier = new HTMLPurifier($config);
    }

    public function setAuthorAttribute($value){
        $this->attributes['author'] = $this->purifier->purify($value);
    }

    public function setTitleAttribute($value){
        $this->attributes['title'] = $this->purifier->purify($value);
    }

    public function setContentAttribute($value){
        $this->attributes['content'] = $this->purifier->purify($value);
    }
}
