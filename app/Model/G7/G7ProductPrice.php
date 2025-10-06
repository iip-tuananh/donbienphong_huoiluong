<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use App\Model\Product;
use App\Model\Common\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Auth;

class G7ProductPrice extends BaseModel
{

    protected $fillable = ['g7_id', 'product_id', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
