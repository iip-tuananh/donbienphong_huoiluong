<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class DocumentGallery extends Model
{
    protected $table = 'document_galleries';
    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function removeFromDB() {
        if ($this->image) $this->image->removeFromDB();
        $this->delete();
    }
}
