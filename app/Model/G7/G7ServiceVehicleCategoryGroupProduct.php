<?php

namespace App\Model\G7;

use Illuminate\Database\Eloquent\Model;

class G7ServiceVehicleCategoryGroupProduct extends Model
{
    public $timestamps = false;

    public function product ()
    {
        return $this->belongsTo(G7Product::class, 'g7_product_id','id');
    }
}
