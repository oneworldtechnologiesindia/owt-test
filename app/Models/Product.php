<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'product';

    public function connectionsView($value)
    {
        $names = "";
        if ($value) {
            $connections = ProductConnection::query()
                ->get()
                ->pluck('connection_name', 'id')
                ->toArray();
            $connectionsArr = explode(", ", $value);
            foreach ($connections as $connkey => $exelist) {
                if (in_array($connkey, $connectionsArr)) {
                    $names .= ($names) ? ", " : "";
                    $names .= $exelist;
                }
            }
        }
        return $names;
    }
    public function executionView($value)
    {
        $names = "";
        if ($value) {
            $execution = ProductExecution::query()
                ->get()
                ->pluck('execution_name', 'id')
                ->toArray();
            $executionArr = explode(",", $value);
            foreach ($execution as $exekey => $exelist) {
                if (in_array($exekey, $executionArr)) {
                    $names .= ($names) ? ", " : "";
                    $names .= $exelist;
                }
            }
        }
        return $names;
    }
}
