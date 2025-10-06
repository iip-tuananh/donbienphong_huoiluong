<?php

namespace App\Model\G7;

use App\Model\G7\G7Product;
use Illuminate\Database\Eloquent\Model;

class G7ServiceProduct extends Model
{
    protected $table = 'g7_service_products';

    public function product ()
    {
        return $this->belongsTo(G7Product::class, 'g7_product_id','id');
    }
}
