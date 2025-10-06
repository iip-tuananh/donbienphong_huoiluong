<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use App\Helpers\FileHelper;
use File as FileSystem;
use App\Model\Common\File;
use Illuminate\Support\Facades\Auth;

class Service extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'services';
    protected $fillable = ['name', 'description', 'content', 'status', 'cate_id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public const XUAT_BAN = 1;
    public const LUU_NHAP = 0;

    public const STATUSES = [
        [
            'id' => self::XUAT_BAN,
            'name' => 'Xuất bản',
            'type' => 'success'
        ],
        [
            'id' => self::LUU_NHAP,
            'name' => 'Lưu nháp',
            'type' => 'danger'
        ],
    ];

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function image_label()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_label');
    }

    public function galleries()
    {
        return $this->hasMany(ServiceGallery::class, 'service_id', 'id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'cate_id');
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
        $result = self::query()->with(['category']);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)
            ->with([
                'image',
                'image_label'
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

            $deleted = ServiceGallery::where('service_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, ServiceGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = ServiceGallery::find($g['id']);
                else $gallery = new ServiceGallery();

                $gallery->service_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, ServiceGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, ServiceGallery::class, null);
                    // FileHelper::uploadFile($file, 'service_gallery', $gallery->id, ServiceGallery::class, null, 1);
                }
            }
        }
    }
}
