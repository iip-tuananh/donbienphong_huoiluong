<?php

namespace App\Model\Traits;

use App\Model\Admin\Tag;
use App\Model\Admin\Tagable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Modules\Announcement\Entities\Announcement;

/**
 * Trait HasTagTrait
 * @mixin Model
 */
trait HasTagTrait
{
    /**
     * @return mixed
     */
    public function tags() {
        return $this->morphToMany(Tag::class,'tagable');
    }

    /** gán tags cho sản phẩm
     * @param $tag_ids
     */
    public function addTags($tag_ids) {
        foreach ($tag_ids as $tag_id) {
            Tagable::query()->create([
                'tagable_id' => $this->id,
                'tagable_type' => get_class($this),
                'tag_id' => $tag_id
            ]);
        }
    }

    /** update tags cho sản phẩm
     * @param $tag_ids
     */
    public function updateTags($tag_ids) {
        $tag_ids_current = $this->tags->pluck('id')->toArray();
        $tag_ids_delete = array_diff($tag_ids_current, $tag_ids);

        Tagable::query()->where([
            'tagable_id' => $this->id,
            'tagable_type' => get_class($this),
        ])->whereIn('tag_id', $tag_ids_delete)->delete();

        foreach ($tag_ids as $tag_id) {
            $exists = Tagable::query()->where([
                'tagable_id' => $this->id,
                'tagable_type' => get_class($this),
                'tag_id' => $tag_id,
            ])->exists();
            if(! $exists) {
                Tagable::query()->create([
                    'tagable_id' => $this->id,
                    'tagable_type' => get_class($this),
                    'tag_id' => $tag_id
                ]);
            }

        }
    }

    public function deleteTags() {
        Tagable::query()->where([
            'tagable_id' => $this->id,
            'tagable_type' => get_class($this),
        ])->delete();
    }
}
