<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSheets extends Model
{
    protected $table = 'time_sheets';

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $appends = ['hours'];

    protected $fillable = [
        'task_name', 'date', 'minutes', 'project_id'
    ];

    private int $id;
    private string $task_name;
    private string $date;
    private int $minutes;
    private int $project_id;
    private int $user_id;

    public function getHoursAttribute()
    {
        return number_format(($this->getAttribute('minutes') / 60), 2, '.', '');
    }
}
