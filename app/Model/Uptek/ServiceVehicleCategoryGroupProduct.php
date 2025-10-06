<?php

namespace App\Model\Uptek;

use App\Model\Product;
use App\Model\Common\VehicleCategory;
use Illuminate\Database\Eloquent\Model;

class ServiceVehicleCategoryGroupProduct extends Model
{
    public $timestamps = false;

    public function product ()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }
}
