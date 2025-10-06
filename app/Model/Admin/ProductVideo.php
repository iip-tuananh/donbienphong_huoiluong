<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class ProductVideo extends Model
{
    protected $table = 'product_videos';
    protected $fillable = ['link', 'video', 'product_id'];
}
