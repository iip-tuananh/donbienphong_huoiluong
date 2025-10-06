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

class CloudPool extends BaseModel
{
    protected $table = 'cloud_pool';
    protected $fillable = [];

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public static function getDataForEdit($id)
    {
        $obj = self::where('id', $id)
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

        return $obj;
    }


    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable');
    }
}
