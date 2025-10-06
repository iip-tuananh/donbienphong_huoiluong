<?php

namespace App\Model\Admin;

use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class WorkFlow extends BaseModel
{
    protected $table = 'workflow';
    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'content' => 'array',
    ];

    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public static function getDataForEdit($id)
    {
        $post = self::where('id', $id)
            ->with([
                'image',
            ])
            ->firstOrFail();

        $resultsArray = is_array($post->content)
            ? $post->content
            : json_decode($post->content, true) ?? [];

        $resultsObjects = array_map(function(array $item) {
            return (object) $item;
        }, $resultsArray);

        $post->content = $resultsObjects;

        return $post;
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->with([
                'image'
            ])
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
}
