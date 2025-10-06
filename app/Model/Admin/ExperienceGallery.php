<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class ExperienceGallery extends Model
{
    protected $table = 'experience_gallery';
    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function removeFromDB() {
        if ($this->image) $this->image->removeFromDB();
        $this->delete();
    }
}
