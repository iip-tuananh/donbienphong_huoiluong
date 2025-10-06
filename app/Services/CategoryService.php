<?php

namespace App\Services;

use App\Model\Admin\Category;

class CategoryService {
    public function getChildCategory($category, $level = null) {
        $category_child_ids = [];
        $category_child_ids = $this->getAllChildCategory($category, $category_child_ids, $level);
        $child_categories = Category::query()->with('tours')->withCount('tours')
            ->whereIn('id', $category_child_ids)->latest()->get();

        return $child_categories;
    }

    private function getAllChildCategory($category, array &$category_child_ids = [], $level = null) {
        $child_categories = Category::query()->where('parent_id', $category->id)->get();

        if($child_categories->isNotEmpty()) {
            foreach ($child_categories as $child) {
                if($level && $child->level > $level) {
                    break;
                }

                array_push($category_child_ids, $child->id);
                $this->getAllChildCategory($child, $category_child_ids, $level);
            }
        }

        return $category_child_ids;
    }
}
