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

class Project extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'projects';
    protected $fillable = ['name', 'description', 'content', 'status', 'area', 'location', 'service', 'completion_time', 'is_highlight'];

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
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image')->orderBy('id', 'desc');
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
        $result = self::query();

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public function galleries()
    {
        return $this->hasMany(ProjectGallery::class, 'project_id', 'id');
    }

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)
            ->with([
                'image',
                'galleries.image'
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

            $deleted = ProjectGallery::where('project_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, ProjectGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = ProjectGallery::find($g['id']);
                else $gallery = new ProjectGallery();

                $gallery->project_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, ProjectGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, ProjectGallery::class, null);
                    // FileHelper::uploadFile($file, 'service_gallery', $gallery->id, ServiceGallery::class, null, 1);
                }
            }
        } else {
            $deleted = ProjectGallery::where('project_id', $this->id)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, ProjectGallery::class);
                }
                $item->removeFromDB();
            }
        }
    }
}
