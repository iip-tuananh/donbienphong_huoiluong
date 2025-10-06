<?php

namespace App\Model\Uptek;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class ServiceProduct extends Model
{
    public function product ()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }
}
