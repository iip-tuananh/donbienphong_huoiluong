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

class Block extends BaseModel
{
    protected $table = 'blocks';


    public static function searchByFilter($request) {

        $result = self::with([]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public function galleries()
    {
        return $this->hasMany(BlockGallery::class, 'block_id', 'id');
    }

    public function syncGalleries($galleries)
    {
        if ($galleries) {
            $exist_ids = [];
            foreach ($galleries as $g) {
                if (isset($g['id'])) array_push($exist_ids, $g['id']);
            }
            $deleted = BlockGallery::where('block_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, BlockGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = BlockGallery::find($g['id']);
                else $gallery = new BlockGallery();

                $gallery->block_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, BlockGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, BlockGallery::class, null);
                }
            }
        }
    }

    public static function getForSelect() {
        $result = self::select(['id', 'name'])
            ->orderBy('sort_order', 'asc')
            ->get();

        return $result;
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'image',
                'galleries' => function ($q) {
                    $q->select(['id', 'block_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
            ])
            ->firstOrFail();
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
