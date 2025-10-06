<?php

namespace App\Model\G7;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class BillReturnProduct extends Model
{

    public function product ()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }

	public function bill_product ()
    {
        return $this->belongsTo(BillProduct::class, 'bill_product_id','id');
    }
}
