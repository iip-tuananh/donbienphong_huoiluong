<?php

namespace App\Model\Admin;
use App\Helpers\FileHelper;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;

class BlockBanner extends BaseModel
{
    protected $table = 'block_banners';


    public static function searchByFilter($request) {

        $result = self::with([]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }


    public static function getForSelect() {
        $result = self::select(['id', 'name'])
            ->orderBy('sort_order', 'asc')
            ->get();

        return $result;
    }


    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function canEdit()
    {
        return $this->created_by == Auth::user()->id;
    }

    public function canDelete ()
    {
        return Auth::user()->id == $this->created_by;
    }


}
