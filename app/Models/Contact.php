<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    //
    use SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'dealer_id',
        'contact_type',
        'company',
        'salutation',
        'name',
        'surname',
        'email',
        'street',
        'street_nr',
        'zipcode',
        'city',
        'country',
        'telephone',
        'note',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public static $contact_type = [
        1 => 'Manufacturer',
        2 => 'Distributor',
        3 => 'Dealer',
    ];

    public static $salutation = [
        1 => 'Herr',
        2 => 'Frau',
        3 => 'Divers',
    ];
}
