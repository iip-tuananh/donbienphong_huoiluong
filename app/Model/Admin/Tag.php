<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Vanthao03596\HCVN\Models\Province;

class Tag extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'tags';
    protected $fillable = ['id', 'code', 'name'];
    protected $dates = ['created_at', 'updated_at'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function searchByFilter($request)
    {
        $result = self::query();

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::query()->where('id', $id)
            ->firstOrFail();
    }

    public static function getForSelect()
    {
        return self::select(['id', 'name', 'code'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'tagable');
    }


    public function canDelete()
    {
        if ($this->products()->count() > 0 || $this->posts()->count() > 0) {
            return false;
        }

        return true;
    }

}
