<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUsers extends Model
{
    protected $table = 'project_users';

    protected $fillable = [
        'user_id',
        'project_id'
    ];
}
