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

class Config extends BaseModel
{
    protected $table = 'configs';
    protected $fillable = [];

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function favicon()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'favicon');
    }

    public function qr()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'qr');
    }

    public function video()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'video');
    }

    public function galleries()
    {
        return $this->hasMany(ConfigGallery::class, 'config_id', 'id');
    }

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)
            ->with([
                'video',
                'image', 'favicon', 'qr',
                'galleries' => function ($q) {
                    $q->select(['id', 'config_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                ])
            ->firstOrFail();
    }

    public function syncGalleries($galleries)
    {
        if ($galleries) {
            $exist_ids = [];
            foreach ($galleries as $g) {
                if (isset($g['id'])) array_push($exist_ids, $g['id']);
            }

            $deleted = ConfigGallery::where('config_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = ConfigGallery::find($g['id']);
                else $gallery = new ConfigGallery();

                $gallery->config_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) $gallery->image->removeFromDB();
                    $file = $g['image'];
                    FileHelper::uploadFile($file, 'config_gallery', $gallery->id, ConfigGallery::class, null, 1);
                }
            }
        }
    }

}
