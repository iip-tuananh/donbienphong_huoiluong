<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class DesignDetail extends Model
{
    protected $table = 'design_details';
    protected $fillable = [];

    public function design_order()
    {
        return $this->belongsTo(DesignOrder::class);
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_layer');
    }
}
