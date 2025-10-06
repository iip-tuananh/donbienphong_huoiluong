<?php

namespace App\Model\Admin;

use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class AboutPage extends BaseModel
{
    protected $table = 'about_page';
    protected $dates = ['created_at', 'updated_at'];

    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'g7_id', 'id');
    }

    public function banner()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'banner');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function image_second()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_second');
    }

    public static function getDataForEdit($id)
    {
        $post = self::where('id', $id)
            ->with([
                'banner',
                'image',
                'image_second'
            ])
            ->firstOrFail();

        return $post;
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

}
