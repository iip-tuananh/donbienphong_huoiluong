<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;
use Illuminate\Database\Eloquent\Model;
use Vanthao03596\HCVN\Models\Province;

class Tagable extends Model
{
    protected $table = 'tagables';
    protected $fillable = ['id', 'tagable_id', 'tagable_type', 'tag_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function tag() {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function products() {
        return $this->morphedByMany(Product::class, 'tagable');
    }
}
