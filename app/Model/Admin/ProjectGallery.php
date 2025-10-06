<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class ProjectGallery extends Model
{
    protected $table = 'project_galleries';
    protected $fillable = ['project_id', 'image_id'];

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function removeFromDB() {
        if ($this->image) $this->image->removeFromDB();
        $this->delete();
    }
}
