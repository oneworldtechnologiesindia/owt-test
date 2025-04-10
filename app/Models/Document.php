<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    public static $types = [
        1 => 'hq-terms-condition',
        2 => 'hq-privacy-policy',
        3 => 'dealer-terms-condition',
        4 => 'dealer-withdraw-policy'
    ];
}
