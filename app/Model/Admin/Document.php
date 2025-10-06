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
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Document extends BaseModel
{

    protected $table = 'documents';
    protected $dates = ['created_at', 'updated_at'];

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }


    public function galleries()
    {
        return $this->hasMany(DocumentGallery::class, 'document_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(DocumentVideo::class, 'document_id', 'id');
    }

    public function getParentCategoryAttribute()
    {
        if ($this->category) {
            if ($this->category->parent_id == 0) {
                return null;
            }

            return $this->category->parent;
        }
        return null;
    }

    public static function searchByFilter($request)
    {
        $result = self::with([
            'user',
        ]);

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        if (!empty($request->user_id)) {
            $result = $result->where('user_id', $request->user_id);
        }


        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        $post = self::where('id', $id)
            ->with([
                'galleries' => function ($q) {
                    $q->select(['id', 'document_id'])
                        ->with(['image']);
                },
                'videos'
            ])
            ->firstOrFail();

        return $post;
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

    public function send()
    {
        foreach (User::all() as $user) {
            $notification = new Notification();
            $notification->url = route("Post.show", $this->id, false);
            $notification->content = Auth::user()->name . " vừa đăng bài viết mới <b>" . $this->name . "</b>";
            $notification->status = 0;
            $notification->receiver_id = $user->id;
            $notification->created_by = Auth::user()->id;
            $notification->save();

            $notification->send();
        }
    }

    public function getRelate() {
        return self::query()->whereNotIn('id', [$this->id])->get();
    }

    public function syncVideo($videos) {
        $this->videos()->delete();

        if($videos) {
            foreach ($videos as $video) {
                $obj = new DocumentVideo();
                $obj->document_id = $this->id;
                $obj->link = $video['link'];

                $obj->save();
            }
        }


    }

    public function syncGalleries($galleries)
    {
        if ($galleries) {
            $exist_ids = [];
            foreach ($galleries as $g) {
                if (isset($g['id'])) array_push($exist_ids, $g['id']);
            }
            $deleted = DocumentGallery::where('document_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, DocumentGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = DocumentGallery::find($g['id']);
                else $gallery = new DocumentGallery();

                $gallery->document_id = $this->id;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, DocumentGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, DocumentGallery::class, null);
                }
            }
        }
    }

}
