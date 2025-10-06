<?php

namespace App\Model\Admin;
use App\Helpers\FileHelper;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;

class Moving extends BaseModel
{
    protected $table = 'movings';
    protected $fillable = [];

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public static function getDataForEdit($id)
    {
        $obj = self::where('id', $id)
            ->with([
                'image'
            ])
            ->firstOrFail();

        return $obj;
    }

}
