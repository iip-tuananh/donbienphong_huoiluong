<?php

namespace App\Model\Admin;
use App\Model\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Faker\Provider\Base;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Policy extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'policies';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
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

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)->firstOrFail();
    }
}
