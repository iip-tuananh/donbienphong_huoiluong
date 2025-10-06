<?php

namespace App\Http\View\Composers;

use App\Model\Admin\About;
use App\Model\Admin\Category;
use App\Model\Admin\Config;
use App\Model\Admin\Consultant;
use App\Model\Admin\Partner;
use App\Model\Admin\Policy;
use App\Model\Admin\Post;
use App\Model\Admin\PostCategory;
use App\Model\Admin\Store;
use Illuminate\View\View;

class FooterComposer
{
    /**
     * Compose Settings Menu
     * @param View $view
     */
    public function compose(View $view)
    {
        $config = Config::query()->get()->first();

        $postsCategory = PostCategory::query()->with('childs')
            ->where('parent_id', 0)->orderBy('sort_order')->get();

        $view->with(['config' => $config, 'postsCategory' => $postsCategory]);
    }
}
