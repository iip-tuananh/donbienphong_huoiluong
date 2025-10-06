<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class BannerGroupGallery extends Model
{
    protected $table = 'banner_group_galleries';
    protected $fillable = ['group_id', 'image_id'];

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function bannerGroup() {
        return $this->belongsTo(BannerGroup::class, 'group_id');
    }

    public function removeFromDB() {
        if ($this->image) $this->image->removeFromDB();
        $this->delete();
    }
}
