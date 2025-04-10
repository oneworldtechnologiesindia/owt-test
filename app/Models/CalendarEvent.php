<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use SoftDeletes;

    protected $table = 'calendar_events';

    public static $category = [
        'danger' => 'Danger',
        'success' => 'Success',
        'primary' => 'Primary',
        'info' => 'Info',
        'dark' => 'Dark',
        'warning' => 'Warning'
    ];
}
