<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    use SoftDeletes;
    protected $table = 'ads';

    public static $size = [
        1 => 'Small',
        2 => 'Medium',
        3 => 'Large',
        4 => 'Extra Large',
    ];

    public static $status = [
        1 => 'Yes',
        2 => 'No',
    ];

    public function getImageUrl()
    {
        if ($this->image) {
            if (Storage::disk('local')->exists("public/ad_image/" . $this->image)) {
                return asset('storage/ad_image') . "/" . $this->image;
            }
        }
        return asset('assets/images/default-image.png');
    }
}
