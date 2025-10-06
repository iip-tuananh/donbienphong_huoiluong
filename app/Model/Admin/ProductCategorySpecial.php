<?php

namespace App\Model\Admin;

use App\Model\BaseModel;

class ProductCategorySpecial extends BaseModel
{
    protected $table = 'product_category_special';
    protected $fillable = ['product_id', 'category_special_id'];

}
