<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerRecordComment extends Model
{
    use SoftDeletes;

    protected $connection = 'server';
    protected $table = 'character_record_comments';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
