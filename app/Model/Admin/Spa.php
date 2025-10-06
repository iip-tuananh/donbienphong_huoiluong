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

class Spa extends BaseModel
{
    protected $table = 'spa';
    protected $dates = ['created_at', 'updated_at'];


    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }


    public static function getDataForEdit($id)
    {
        $post = self::where('id', $id)
            ->with([
                'blocks',
                'blocks.galleries' => function ($q) {
                    $q->select(['id', 'block_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'image'
            ])
            ->firstOrFail();

        return $post;
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->with([
                'image'
            ])
            ->firstOrFail();
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

}
