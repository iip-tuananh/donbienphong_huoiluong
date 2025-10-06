<?php

namespace App\Http\View\Composers;

use App\Model\Admin\Banner;
use App\Model\Admin\Category;
use App\Model\Admin\PostCategory;
use Illuminate\View\View;

class MenuHomePageComposer
{
    /**
     * Compose Settings Menu
     * @param View $view
     */
    public function compose(View $view)
    {
        $categories = Category::query()->with(['childs'])
            ->where(['parent_id' => 0])
            ->orderBy('sort_order')
            ->get();

        $postCategories = PostCategory::query()
            ->where(['parent_id' => 0])
            ->orderBy('sort_order')
            ->get();


        $view->with(['categories' => $categories, 'postCategories' => $postCategories]);
    }
}
