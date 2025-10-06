<?php

namespace App\Model\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';
    protected $fillable = ['id', 'attribute_id', 'product_id', 'value'];
}
