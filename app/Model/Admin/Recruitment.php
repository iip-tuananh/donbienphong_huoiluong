<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Recruitment extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'recruitments';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return true;
    }

    public static function searchByFilter($request)
    {
        $result = self::query();

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();

        return $result;
    }

    public static function getForSelect()
    {
        return self::select(['id', 'title as name'])
            ->orderBy('id', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        return self::query()->with(['image'])
            ->where('id', $id)
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }


}
