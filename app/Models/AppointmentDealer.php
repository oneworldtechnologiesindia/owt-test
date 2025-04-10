<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentDealer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'appointment_dealer';

    public static $status = [
        1 => 'Pending',
        2 => 'Confirmed',
        3 => 'Completed',
        4 => 'Cancel',
        5 => 'Cancel By Dealer',
        6 => 'Reschedule',
        7 => 'Confirmed',
        8 => 'Expired'
    ];
    public static $appoType = [
        0 => 'Appointment',
        1 => 'Zoom-Meeting',
    ];
}
