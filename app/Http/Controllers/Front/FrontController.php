<?php

namespace App\Http\Controllers\Front;

use App\Helpers\FileHelper;
use App\Http\Traits\ResponseTrait;
use App\Model\Admin\About;
use App\Model\Admin\AboutPage;
use App\Model\Admin\Achievement;
use App\Model\Admin\ArticleSubmission;
use App\Model\Admin\BannerGroup;
use App\Model\Admin\Block;
use App\Model\Admin\Blog;
use App\Model\Admin\Business;
use App\Model\Admin\Category;
use App\Model\Admin\CategorySpecial;
use App\Model\Admin\CloudPool;
use App\Model\Admin\Config;
use App\Model\Admin\Document;
use App\Model\Admin\DocumentGallery;
use App\Model\Admin\DocumentVideo;
use App\Model\Admin\Experience;
use App\Model\Admin\Gallery;
use App\Model\Admin\Moving;
use App\Model\Admin\Policy;
use App\Model\Admin\PolivicyDetail;
use App\Model\Admin\PostCategorySpecial;
use App\Model\Admin\Product;
use App\Model\Admin\Project;
use App\Model\Admin\Room;
use App\Model\Admin\ServiceSpa;
use App\Model\Admin\Spa;
use App\Model\Admin\Tag;
use App\Model\Admin\Tagable;
use App\Model\Admin\Team;
use App\Model\Admin\Term;
use App\Model\Admin\Topic;
use App\Model\Admin\Tour;
use App\Model\Admin\WhyUs;
use App\Model\Admin\WorkFlow;
use App\Model\Common\Permission;
use App\Model\Common\Role;
use App\Model\Common\User;
use App\Services\CategoryService;
use App\Services\PostService;
use Google\Service\ShoppingContent\Resource\Pos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Validator;
use Response;
use App\Http\Controllers\Controller;
use App\Model\Admin\Banner;
use App\Model\Admin\Contact;
use App\Model\Admin\DesignDetail;
use App\Model\Admin\DesignOrder;
use App\Model\Admin\Partner;
use App\Model\Admin\Post;
use App\Model\Admin\PostCategory;
use App\Model\Admin\ProductRate;
use App\Model\Admin\Review;
use App\Model\Admin\Service;
use App\Model\Admin\ServiceType;
use App\Model\Admin\Voucher;
use DB;
use Mail;
use SluggableScopeHelpers;

class FrontController extends Controller
{
    use ResponseTrait;

    public $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function homePage() {
        //start
        $banners = Banner::query()->with(['image'])
            ->whereNull('type')
            ->latest()->get();
        $postCategories = PostCategory::query()->where('show_home_page', 1)
            ->where('parent_id', 0)->orderBy('sort_order')->get();
        // bài viết mới nhất
        $postsRecent = Post::query()->with(['category', 'image'])->where('status', 1)->latest()->limit(6)->get();
        // bài viết danh mục đặc biệt
        $categoriesSpecial = CategorySpecial::query()->has('posts')
            ->where('show_home_page', 1)
            ->whereNotIn('id', [11])
            ->orderBy('order_number')->get();

        $categoriesSpecial->each(function ($cat) {
            $posts = $cat->posts()
                ->with(['image','category'])
                ->latest('created_at')
                ->limit(5)
                ->get();
            $cat->setRelation('posts', $posts);
        });

        // danh mục bài viết nổi bật
        $popularCate = CategorySpecial::query()->where('id', 11)->first();
        $popularPosts = $popularCate->posts()->with('image')
            ->where('status', 1)->latest()->limit(6)->get();
        $tags = Tag::select('tags.*')->distinct()
            ->join('tagables as t', 't.tag_id', '=', 'tags.id')
            ->join('posts as p', 'p.id', '=', 't.tagable_id')
            ->where('t.tagable_type', Post::class)
            ->where('p.status', 1)
            ->get();

        return view('site.home', compact('banners','postCategories', 'postsRecent', 'categoriesSpecial', 'popularPosts', 'popularCate', 'popularCate', 'tags'));
    }

    public function sendPost(Request $request)
    {
        return view('site.send_post');
    }


    public function submitSendPost(Request $request)
    {
        $rule  =  [
            'title'        => 'required|string|max:180',
            'author_name'  => 'required|string|max:150',
            'content_html' => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];

        $messages = [
            'title.required'        => 'Vui lòng nhập tiêu đề bài viết',
            'author_name.required'  => 'Vui lòng nhập tên tác giả',
            'attachment.file'       => 'Tệp đính kèm không hợp lệ',
            'attachment.mimes'      => 'Chỉ cho phép tệp .doc, .docx hoặc .pdf',
            'attachment.max'        => 'Kích thước tệp tối đa là 10MB',
        ];

        $validate = Validator::make(
            $request->all(),
            $rule,
            $messages
        );

        if ($validate->fails()) {
            return $this->responseErrors('Gửi yêu cầu thất bại!', $validate->errors());
        }

        DB::beginTransaction();
        try {
            $obj = new ArticleSubmission();
            $obj->title = $request->title;
            $obj->author_name = $request->author_name;
            $obj->customer_id = auth('customer')->id();
            $obj->content_html = $request->content_html;

            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file       = $request->file('attachment');

                // Thư mục đích: public/user_posts
                $destPath   = public_path('user_posts');
                File::ensureDirectoryExists($destPath, 0755, true);

                // Tạo tên file an toàn + duy nhất: slug-timestamp-rand.ext
                $ext        = $file->getClientOriginalExtension();
                $base       = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeBase   = Str::slug(Str::limit($base, 80, '')); // tránh tên quá dài/ký tự lạ
                $filename   = $safeBase . '-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $ext;

                // Di chuyển file vào public/user_posts
                $file->move($destPath, $filename);

                // Lưu đường dẫn/metadata vào DB (tùy bạn có cột nào)
                $obj->attachment_path = 'user_posts/' . $filename;
                $obj->attachment_name = $file->getClientOriginalName();

            }

            $obj->save();

            DB::commit();
            return $this->responseSuccess('Gửi bài viết thành công!');
        } catch (\Exception $exception){
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->responseErrors('Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    public function getPostByCate(Request $request) {
        $category = PostCategory::query()->where('id', $request->cate_id)->first();
        $cateIds = [$category->id];

        if(! $category->parent_id) {
            $lv2Ids = PostCategory::query()
                ->where('parent_id', $category->id)
                ->pluck('id');

            $lv3Ids = PostCategory::query()
                ->whereIn('parent_id', $lv2Ids)
                ->pluck('id');

            $cateIds = $lv2Ids->merge($lv3Ids)->unique()->values()->all();

            if(! $cateIds) {  $cateIds = [$category->id]; }
        }

        $posts = Post::query()->with('category', 'image')->whereIn('cate_id', $cateIds)->where('status', 1)->latest()
            ->take(5)->get();

        $json = new \stdClass();
        $json->success = true;
        $json->data = $posts;

        return Response::json($json);
    }

    public function getPostByTag(Request $request, $slug) {
        $sortMap = [
            'name_asc'  => ['posts.name', 'asc'],
            'name_desc' => ['posts.name', 'desc'],
            'date_asc'  => ['posts.created_at', 'asc'],
            'date_desc' => ['posts.created_at', 'desc'],
        ];

        if (isset($sortMap[$request->get('sort')])) {
            list($sortColumn, $sortDirection) = $sortMap[$request->get('sort')];
        } else {
            $sortColumn = 'id';
            $sortDirection = 'desc';
        }

        $tag = Tag::query()->where('slug', $slug)->first();

        $posts = Post::query()
            ->with(['category', 'image'])
            ->join('tagables', 'posts.id', '=', 'tagables.tagable_id')
            ->where('posts.status', 1)
            ->where('tagables.tagable_type', Post::class)
            ->where('tagables.tag_id', $tag->id)
            ->select('posts.*')
            ->distinct()
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12, ['posts.*'])->appends($request->only('sort'));


        $tags = Tag::select('tags.*')->distinct()
            ->join('tagables as t', 't.tag_id', '=', 'tags.id')
            ->join('posts as p', 'p.id', '=', 't.tagable_id')
            ->where('t.tagable_type', Post::class)
            ->where('p.status', 1)
            ->get();

        $categories = PostCategory::query()
            ->where('parent_id', 0)
            ->orderBy('sort_order')
            ->withCount(['posts' => function ($q) {
                $q->where('status', 1);
            }])
            ->with(['childs' => function ($q) {
                $q->withCount(['posts' => function ($q) {
                    $q->where('status', 1);
                } ]);
            }])
            ->get()
            ->map(function ($cat) {
                $cat->total_posts = $cat->childs->isNotEmpty()
                    ? $cat->childs->sum('posts_count')
                    : $cat->posts_count;
                return $cat;
            });

        // danh mục bài viết phổ biến
        $popularCate = CategorySpecial::query()->where('id', 15)->first();
        $popularPosts = $popularCate->posts()->with('image')
            ->where('status', 1)->latest()->limit(4)->get();

        // bài viết mới nhất
        $postsRecent = Post::query()->with(['category', 'image'])->where('status', 1)->latest()->limit(6)->get();

        return view('site.blog_tag', compact('posts', 'tag', 'postsRecent', 'tags', 'popularPosts', 'postsRecent', 'categories'));
    }


    public function blogs(Request $request, $slug = null) {
        $category = null;
        $sortMap = [
            'name_asc'   => ['name', 'asc'],
            'name_desc'  => ['name', 'desc'],
            'date_asc'   => ['created_at', 'asc'],
            'date_desc'  => ['created_at', 'desc'],
        ];

        if (isset($sortMap[$request->get('sort')])) {
            list($sortColumn, $sortDirection) = $sortMap[$request->get('sort')];
        } else {
            $sortColumn = 'id';
            $sortDirection = 'desc';
        }

        $parentCateLv1 = null;
        $parentCateLv2 = null;

        if($slug) {
            $category = PostCategory::query()->where('slug', $slug)->first();


            // $category truyền vào là danh mục cha
            if(! $category->parent_id) {
                $lv2Ids = PostCategory::query()
                    ->where('parent_id', $category->id)
                    ->pluck('id');

                $lv3Ids = PostCategory::query()
                    ->whereIn('parent_id', $lv2Ids)
                    ->pluck('id');

                $cateIds = $lv2Ids->merge($lv3Ids)->unique()->values()->all();

                if(! $cateIds) {  $cateIds = [$category->id]; }

            } else {
                $cateIds = [$category->id];
                $parentCateLv1 = PostCategory::query()->where('id', $category->parent_id)->first();

            }

            $posts = Post::query()->with(['image', 'category'])
                ->where('status', 1)->whereIn('cate_id', $cateIds)->orderBy($sortColumn, $sortDirection)
                ->paginate(8)->appends($request->only('sort'));
        } else {
            $posts = Post::query()->with(['image', 'category'])
                ->where('status', 1)->orderBy($sortColumn, $sortDirection)
                ->paginate(8)->appends($request->only('sort'));
        }

        PostService::attachAccess($posts, auth('customer')->id());

        $categories = PostCategory::query()
            ->where('parent_id', 0)
            ->orderBy('sort_order')
            ->withCount(['posts' => function ($q) {
                $q->where('status', 1);
            }])
            ->with(['childs' => function ($q) {
                $q->withCount(['posts' => function ($q) {
                    $q->where('status', 1);
                } ]);
            }])
            ->get()
            ->map(function ($cat) {
                $cat->total_posts = $cat->childs->isNotEmpty()
                    ? $cat->childs->sum('posts_count')
                    : $cat->posts_count;
                return $cat;
            });

        $tags = Tag::select('tags.*')->distinct()
            ->join('tagables as t', 't.tag_id', '=', 'tags.id')
            ->join('posts as p', 'p.id', '=', 't.tagable_id')
            ->where('t.tagable_type', Post::class)
            ->where('p.status', 1)
            ->get();

        // danh mục bài viết phổ biến
        $popularCate = CategorySpecial::query()->where('id', 15)->first();
        $popularPosts = $popularCate->posts()->with('image')
            ->where('status', 1)->latest()->limit(4)->get();

        // bài viết mới nhất
        $postsRecent = Post::query()->with(['category', 'image'])->where('status', 1)->latest()->limit(6)->get();

        return view('site.blog', compact('posts', 'category', 'categories', 'tags', 'popularPosts', 'postsRecent', 'parentCateLv1', 'parentCateLv2'));
    }


    public function blogDetail(Request $request, $slug) {
        $blog = Post::query()->with(['image', 'user_create', 'category', 'files'])->where('slug', $slug)->first();
        $files = $blog->files ?? collect();

        list($docs, $pdfs) = $files->partition(function ($f) {
            $name = (string) $f->original_name;
            return (bool) preg_match('/\.(docx?)$/i', $name);
        });

        if ($docs->isEmpty()) {
            $docs = $files->filter(function ($f) {
                return in_array($f->mime, [
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ], true);
            });
        }

        if ($pdfs->isEmpty()) {
            $pdfs = $files->filter(function ($f) {
                return $f->mime === 'application/pdf';
            });
        }

        $blog->docs = $docs->values();
        $blog->pdfs = $pdfs->values();

        $othersBlog = Post::query()->with(['image', 'category'])
            ->whereNotIn('id', [$blog->id])
            ->where('cate_id', $blog->cate_id)
            ->where('status', 1)->latest()->limit(2)
            ->get();

        $categories = PostCategory::query()
            ->where('parent_id', 0)
            ->orderBy('sort_order')
            ->withCount(['posts' => function ($q) {
                $q->where('status', 1);
            }])
            ->with(['childs' => function ($q) {
                $q->orderBy('sort_order')
                    ->withCount(['posts' => function ($qq) {
                        $qq->where('status', 1);
                    }])
                    ->with(['childs' => function ($qq) {
                        $qq->orderBy('sort_order')
                            ->withCount(['posts' => function ($qqq) {
                                $qqq->where('status', 1);
                            }]);
                    }]);
            }])
            ->get()
            ->map(function ($cat) {
                $cat->total_posts = $cat->childs->isNotEmpty()
                    ? $cat->childs->sum(function ($child) {
                        return $child->posts_count + $child->childs->sum('posts_count');
                    })
                    : $cat->posts_count;

                $cat->childs->each(function ($child) {
                    $child->total_posts = $child->posts_count + $child->childs->sum('posts_count');
                });

                return $cat;
            });


        $tags = Tag::select('tags.*')->distinct()
            ->join('tagables as t', 't.tag_id', '=', 'tags.id')
            ->join('posts as p', 'p.id', '=', 't.tagable_id')
            ->where('t.tagable_type', Post::class)
            ->where('p.status', 1)
            ->get();
        // danh mục bài viết phổ biến
        $popularCate = CategorySpecial::query()->where('id', 15)->first();
        $popularPosts = $popularCate->posts()->with('image')
            ->where('status', 1)->latest()->limit(4)->get();

        // bài viết mới nhất
        $postsRecent = Post::query()->with(['category', 'image'])->where('status', 1)->latest()->limit(6)->get();

        return view('site.blog_detail', compact('blog', 'othersBlog', 'categories', 'tags','postsRecent', 'popularPosts'));
    }































































    public function getProductList(Request $request, $slug = null) {
        $sortMap = [
            'name_asc'   => ['name', 'asc'],
            'name_desc'  => ['name', 'desc'],
            'price_asc'  => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'date_asc'   => ['created_at', 'asc'],
            'date_desc'  => ['created_at', 'desc'],
        ];
        if (isset($sortMap[$request->get('sort')])) {
            list($sortColumn, $sortDirection) = $sortMap[$request->get('sort')];
        } else {
            $sortColumn = 'id';
            $sortDirection = 'desc';
        }

        $category = null;
        if($slug) {
            $category = Category::query()->where('slug', $slug)->first();
            $products = Product::query()->with(['category', 'image'])->where('status', 1)
                ->where('cate_id', $category->id)
                ->orderBy($sortColumn, $sortDirection)
                ->paginate(20)->appends($request->only('sort'));
        } else {
            $products = Product::query()->with(['category', 'image'])->where('status', 1)
                ->orderBy($sortColumn, $sortDirection)
                ->paginate(20)->appends($request->only('sort'));
        }

        $allCates = Category::query()->orderBy('sort_order', 'asc')->get();
        $banner = Banner::query()->with('image')->where('type',1)->first();
        $blogs = Post::query()->with(['image'])->where('status', 1)
            ->where('type', 'post')
            ->latest()->get()->take(5);

        return view('site.product_list', compact('products', 'banner', 'category', 'allCates', 'blogs'));
    }

    public function getProductDetail(Request $request, $slug = null) {
        $product = Product::query()->with(['category', 'image', 'galleries.image'])->where('slug', $slug)->first();
        $productsLq = Product::query()->with(['image'])
            ->where('cate_id', $product->category->id)
            ->where('status', 1)
            ->whereNotIn('id', [$product->id])->latest()->take(5)->get();
        $allCates = Category::query()->orderBy('sort_order', 'asc')->get();
        $banner = Banner::query()->with('image')->where('type',1)->first();
        $blogs = Post::query()->with(['image'])->where('status', 1)
            ->where('type', 'post')
            ->latest()->get()->take(5);

        $viewed = $request->session()->get('viewed_products', []);

        if (($key = array_search($product->id, $viewed)) !== false) {
            unset($viewed[$key]);
        }

        array_unshift($viewed, $product->id);

        $viewed = array_slice($viewed, 0, 4);

        $request->session()->put('viewed_products', $viewed);

        $viewedIds = session('viewed_products', []);
        $viewedProducts = [];
        if ($viewedIds) {
            $viewedProducts = Product::query()->with(['image'])
                ->whereIn('id', $viewedIds)
                ->orderByRaw(\DB::raw("FIELD(id,".implode(',', $viewedIds).")"))
                ->get();
        }

        return view('site.product_detail', compact('product','productsLq', 'allCates', 'banner','blogs', 'viewedProducts'));
    }

    public function getPolicy(Request $request, $slug)
    {
        $policy = Policy::query()->where('slug', $slug)->first();
        $policeis = Policy::query()->whereNotIn('id', [$policy->id])->where('status', 1)->get();

        return view('site.policy', compact('policy', 'policeis'));
    }

    public function projects(Request $request, $slug = null) {
        $projects = Project::query()->with(['image'])->where('status', 1)->latest()->paginate(10);
        $banner = Banner::query()->with('image')->where('type',4)->first();
        $projectsHighlights = Project::query()->with(['image'])->where('status', 1)
            ->where('is_highlight', 1)
            ->latest()->get();

        return view('site.projects', compact('projects', 'banner', 'projectsHighlights'));
    }

    public function getProjectDetail(Request $request, $slug) {
        $project = Project::query()->with(['image', 'galleries.image'])->where('slug', $slug)->first();
        $projectsRe = Project::query()->with(['image', 'galleries.image'])
            ->where('status', 1)
            ->whereNotIn('id', [$project->id])
            ->get();
        $banner = Banner::query()->with('image')->where('type',4)->first();

        return view('site.project_detail', compact('project', 'projectsRe', 'banner'));
    }


    public function services(Request $request) {
        $category = null;
        $services = Service::query()->with(['image', 'image_label'])->where('status', 1)->latest()->paginate(15);
        $banner = Banner::query()->with('image')->where('type',5)->first();
        $blogs = Post::query()->with(['image'])->where('status', 1)
            ->where('type', 'post')
            ->latest()->get()->take(5);

        return view('site.services', compact('services', 'category', 'banner','blogs'));
    }

    public function getServiceDetail(Request $request, $slug) {
        $service = Service::query()->with(['image'])->where('slug', $slug)->first();
        $otherServices = Service::query()->with(['image'])->where('status', 1)
            ->whereNotIn('id', [$service->id])->latest()->take(5)->get();
        $banner = Banner::query()->with('image')->where('type',5)->first();

        return view('site.service_detail', compact('service', 'otherServices', 'banner'));
    }




    public function sumenh(Request $request) {
        $title = "Về cơ quan chủ quản";
        $content = Config::query()->first()->web_des;

        return view('site.about_us', compact('title', 'content'));
    }

    public function tochuc(Request $request) {
        $title = "Về tạp chí";
        $content = Config::query()->first()->web_des_2;

        return view('site.about_us', compact('title', 'content'));
    }

    public function huongdanguibai(Request $request) {
        $title = "Hướng dẫn gửi bài";
        $content = Config::query()->first()->web_des_3;

        return view('site.about_us', compact('title', 'content'));
    }

    public function abouts(Request $request) {
        $banner = Banner::query()->with('image')->where('type',6)->first();
        $about = AboutPage::query()->with('image','image_second','banner')->first();

        return view('site.about_us', compact('banner', 'about'));
    }

    public function contact(Request $request) {
        $config = Config::query()->find(1);
        $banner = Banner::query()->with('image')->where('type',3)->first();

        return view('site.contact_us', compact('config', 'banner'));
    }

    public function postContact(Request $request)
    {
        $rule  =  [
            'name'  => 'required',
            'message'  => 'required',
            'phone' => 'required|regex:/^(0)[0-9]{9,11}$/',
        ];

        $validate = Validator::make(
            $request->all(),
            $rule,
            [
                'name.required' => 'Vui lòng nhập họ tên',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại không đúng định dạng',
                'message.required' => 'Vui lòng nhập nội dung',
            ]
        );

        if ($validate->fails()) {
            return $this->responseErrors('Gửi yêu cầu thất bại!', $validate->errors());
        }

        $contact = new Contact();
        $contact->user_name = $request->name;
        $contact->phone_number = $request->phone ?? null;
        $contact->content = $request->message ?? null;
        $contact->email = $request->email ?? null;
        $contact->save();

        return $this->responseSuccess('Gửi yêu cầu thành công!');
    }


    public function searchProduct(Request $request) {
        $keyword = $request->keywords;
        $posts =  Post::query()->with(['image'])
            ->where('status',1)
            ->where('name', 'like', '%'.$keyword.'%')->get();
        $categories = PostCategory::query()
            ->where('parent_id', 0)
            ->orderBy('sort_order')
            ->withCount(['posts' => function ($q) {
                $q->where('status', 1);
            }])
            ->with(['childs' => function ($q) {
                $q->withCount(['posts' => function ($q) {
                    $q->where('status', 1);
                } ]);
            }])
            ->get()
            ->map(function ($cat) {
                $cat->total_posts = $cat->childs->isNotEmpty()
                    ? $cat->childs->sum('posts_count')
                    : $cat->posts_count;
                return $cat;
            });

        $tags = Tag::select('tags.*')->distinct()
            ->join('tagables as t', 't.tag_id', '=', 'tags.id')
            ->join('posts as p', 'p.id', '=', 't.tagable_id')
            ->where('t.tagable_type', Post::class)
            ->where('p.status', 1)
            ->get();

        // danh mục bài viết phổ biến
        $popularCate = CategorySpecial::query()->where('id', 15)->first();
        $popularPosts = $popularCate->posts()->with('image')
            ->where('status', 1)->latest()->limit(4)->get();

        // bài viết mới nhất
        $postsRecent = Post::query()->with(['category', 'image'])->where('status', 1)->latest()->limit(6)->get();

        return view('site.search', compact('posts', 'keyword', 'postsRecent', 'popularPosts', 'categories', 'tags'));
    }

























































































    public function about_page(Request $request, $slug) {
        $category = PostCategory::findBySlug($slug);
        $post = Post::query()->with('image')->where('cate_id', $category->id)
            ->where('status', 1)
            ->firstOrFail();

        return view('site.about_page', compact('post', 'category'));
    }





    public function knowledge(Request $request, $slug = null) {
        $category = null;
        $allCate = PostCategory::query()->where('type', PostCategory::TYPE_KIENTHUC)
            ->where('parent_id', 0)
            ->get();

        if($slug) {
            $category = PostCategory::query()->with('parent')->where('slug', $slug)->first();

            // danh muc con
            if($category->parent_id) {
                $posts = Post::query()->with(['image'])->where('status', 1)
                    ->where('cate_id', $category->id)
                    ->where('type', 'knowledge')
                    ->latest()->paginate(6);
            } else {
                $categoryIds = PostCategory::query()->where('parent_id', $category->id)->pluck('id')->toArray();

                $posts = Post::query()->with(['image'])->where('status', 1)
                    ->whereIn('cate_id', $categoryIds)
                    ->where('type', 'knowledge')
                    ->latest()->paginate(6);
            }
        } else {
            $posts = Post::query()->with(['image'])->where('status', 1)
                ->where('type', 'knowledge')
                ->latest()->paginate(6);
        }

        $otherBlog = Post::query()->with(['image', 'category', 'user_create'])
            ->where('type', 'knowledge')
            ->where('status', 1)->latest()->limit(3)
            ->get();

        return view('site.knowledge', compact('posts', 'category', 'allCate', 'otherBlog'));
    }

    public function getKnowledgeDetail(Request $request, $slug) {
        $blog = Post::query()->with(['image', 'user_create'])->where('slug', $slug)->first();
        $otherBlog = Post::query()->with(['image', 'category', 'user_create'])
            ->where('type', 'knowledge')
            ->where('status', 1)->latest()->limit(3)
            ->get();
        $allCate = PostCategory::query()->where('type', 4)
            ->where('parent_id', 0)
            ->get();

        return view('site.knowledge_detail', compact('blog', 'otherBlog', 'allCate'));
    }


    public function roomCategory(Request $request) {
        $rooms = Room::query()->with(['galleries' => function ($q) {
                    $q->select(['id', 'room_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                }])->where('status', Room::XUAT_BAN)->get();

        $locale = LaravelLocalization::getCurrentLocale();

        if ($locale === 'en') {
            foreach ($rooms as $room) {
                $room->name = $room->name_eng;
                $room->area = $room->area_eng;
                $room->view = $room->view_eng;
                $room->bedrooms = $room->bedrooms_eng;
                $room->maximum_occupancy = $room->maximum_occupancy_eng;
                $room->description = $room->description_eng;
                $room->highlight = $room->highlight_eng;
                $room->amenities = $room->amenities_eng;
            }
        }

        return view('site.hang_phong', compact('rooms'));
    }

    public function cuisine(Request $request, $slug) {
        $categoryPost = PostCategory::findBySlug($slug);
        $post = Post::query()->with(['image', 'blocks.galleries.image'])
            ->where('cate_id', $categoryPost->id)
            ->where('status', Post::XUAT_BAN)
            ->first();

        $locale = LaravelLocalization::getCurrentLocale();

        if ($locale === 'en' && $post && $post->blocks) {
            foreach ($post->blocks as $block) {
                $block->title = $block->title_eng;
                $block->body  = $block->body_eng;
            }
        }

        return view('site.am_thuc', compact('post'));
    }
    public function spa(Request $request) {
        $spa = Spa::query()->with(['blocks.galleries.image'])->find(1);
        $servicesSpa = ServiceSpa::query()->with('image')->get();
        $experienceSpa = Experience::query()->with(['galleries.image', 'image'])->get();

        $locale = LaravelLocalization::getCurrentLocale();
        if($locale == 'en') {
            $spa->title = $spa->title_eng;
            $spa->intro = $spa->intro_eng;
            $spa->body = $spa->body_eng;

            $servicesSpa = $servicesSpa->map(function ($service) {
                $service->name = $service->name_eng;
                $service->body = $service->body_eng;

                return $service;
            });

            $experienceSpa = $experienceSpa->map(function ($ser) {
                $ser->name = $ser->name_eng;
                $ser->body = $ser->body_eng;

                return $ser;
            });
        }

        if ($locale === 'en' && $spa && $spa->blocks) {
            foreach ($spa->blocks as $block) {
                $block->title = $block->title_eng;
                $block->body  = $block->body_eng;
            }
        }

        return view('site.spa', compact('spa', 'servicesSpa', 'experienceSpa'));
    }

    public function getDataExperience(Request $request, $id) {
        $json = new \stdClass();
        $json->success = true;
        $data = Experience::getDataForEdit($id);

        $locale = LaravelLocalization::getCurrentLocale();

        if($locale == 'en') {
            $data->name = $data->name_eng;
            $data->body = $data->body_eng;
        }

        $json->data = $data;


        return Response::json($json);
    }

    public function cloudPool(Request $request)
    {
        $data = CloudPool::query()->with(['blocks.galleries.image'])->find(1);
        $locale = LaravelLocalization::getCurrentLocale();

        if ($locale === 'en' && $data && $data->blocks) {
            foreach ($data->blocks as $block) {
                $block->title = $block->title_eng;
                $block->body  = $block->body_eng;
            }

            $data->title = $data->title_eng;
        }

        return view('site.cloud_pool', compact('data'));
    }

    public function promotionService() {
        $service_type = ServiceType::query()->where('type', ServiceType::UU_DAI);
        $service_type_ids = $service_type->pluck('id')->toArray();
        $service_types = $service_type->get();
        $listService = Service::query()->with(['image'])->whereIn('service_type_id', $service_type_ids)->get();

        $locale = LaravelLocalization::getCurrentLocale();

        if($locale == 'en') {
            $service_types = $service_types->map(function ($ser) {
                $ser->name = $ser->name_eng;
                return $ser;
            });

            $listService = $listService->map(function ($ser) {
                $ser->name = $ser->name_eng;
                $ser->description = $ser->description_eng;
                $ser->content = $ser->content_eng;
                return $ser;
            });
        }

        return view('site.uu_dai', compact('listService', 'service_types'));
    }

    public function experienceService() {
        $service_type = ServiceType::query()->where('type', ServiceType::TRAI_NGHIEM);
        $service_type_ids = $service_type->pluck('id')->toArray();
        $service_types = $service_type->get();

        $locale = LaravelLocalization::getCurrentLocale();
        $listService = Service::query()->with(['image', 'galleries.image'])->whereIn('service_type_id', $service_type_ids)->get();

        if($locale == 'en') {
            $service_types = $service_types->map(function ($ser) {
                $ser->name = $ser->name_eng;
                return $ser;
            });

            $listService = $listService->map(function ($ser) {
                $ser->name = $ser->name_eng;
                $ser->description = $ser->description_eng;
                $ser->content = $ser->content_eng;
                return $ser;
            });
        }

        return view('site.trai_nghiem', compact('listService', 'service_types'));
    }

    public function resortService() {
        $service_type = ServiceType::query()->where('type', ServiceType::GOI_NGHI_DUONG);
        $service_type_ids = $service_type->pluck('id')->toArray();
        $service_types = $service_type->get();
        $listService = Service::query()->with(['image', 'galleries.image'])->whereIn('service_type_id', $service_type_ids)->get();

        $locale = LaravelLocalization::getCurrentLocale();

        if($locale == 'en') {
            $service_types = $service_types->map(function ($serv) {
                $serv->name = $serv->name_eng;
                return $serv;
            });

            $listService = $listService->map(function ($ser) {
                $ser->name = $ser->name_eng;
                $ser->description = $ser->description_eng;
                $ser->content = $ser->content_eng;
                return $ser;
            });
        }

        return view('site.goi_dich_vu', compact('listService', 'service_types'));
    }

    public function detailResortService($slug) {
        $service = Service::query()->with(['image', 'galleries.image'])->where('slug', $slug)->first();

        $locale = LaravelLocalization::getCurrentLocale();

        if($locale == 'en') {
            $service->name = $service->name_eng;
            $service->content = $service->content_eng;
        }

        return view('site.chi_tiet_goi_dich_vu', compact('service'));
    }

    public function questionQA() {
        $topics = Topic::query()->with(['questions'])->latest()->get();

        return view('site.cau_hoi', compact('topics'));
    }

    public function bookingRoom() {
        return view('site.booking');
    }

    public function getServiceTab(Request $request) {
        $type = $request->get('type');
        $slug = $request->get('slug');
        if ($slug == 'all') {
            $service_type_ids = ServiceType::query()->where('type', $type)->pluck('id')->toArray();
        } else {
            $service_type_ids = ServiceType::query()->where('type', $type)->where('slug', $slug)->pluck('id')->toArray();
        }
        $listService = Service::query()->with(['image', 'galleries.image'])->whereIn('service_type_id', $service_type_ids)->get();

        $locale = LaravelLocalization::getCurrentLocale();

        if($locale == 'en') {
            $listService = $listService->map(function ($ser) {
                $ser->name = $ser->name_eng;
                $ser->description = $ser->description_eng;
                $ser->content = $ser->content_eng;
                return $ser;
            });
        }

        return Response::json(['success' => true, 'data' => $listService]);
    }

    public function searchTour(Request $request) {
        $departure = $request->query('departure');
        $destination = $request->query('destination');
        $tourName  = $request->query('tourName');

        $query = Tour::query();

        if (!empty($departure)) {
            $query->where('start_off', 'like', '%' . $departure . '%');
        }

        if (!empty($destination)) {
            $query->where('destination', 'like', '%' . $destination . '%');
        }

        if (!empty($tourName)) {
            $query->orWhere(function($q) use ($tourName) {
                $q->where('title', 'like', '%' . $tourName . '%')
                    ->orWhere('title_short', 'like', '%' . $tourName . '%');
            });
        }

        $results = $query->get();

        return view('site.tours.result_search', ['results' => $results]);
    }

    public function tourDetail(Request $request, $slug) {
        $tour = Tour::with(['image', 'category.image', 'category.parent.image'])->where('slug', $slug)->first();
        $tourSuggest = Tour::query()->with(['image'])
            ->where('cate_id', $tour->cate_id)
            ->whereNotIn('id', [$tour->id])
            ->latest()->take('10')->get();
        $config = Config::query()->first();

        return view('site.tours.tour_detail', compact('tour', 'tourSuggest', 'config'));
    }

    public function bookingTour(Request $request, $slug = null) {
        $tours = Tour::query()->where('status', Tour::XUAT_BAN)->latest()->get();
        $tour = null;
        if($slug) {
            $tour = Tour::findBySlug($slug);
        }

        return view('site.tours.booking', compact('tours', 'tour'));
    }

    public function submitBooking(Request $request)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $translate = [
                'tour_id.required' => 'Vui lòng chọn tour',
                'tour_id.not_in' => 'Vui lòng chọn tour',
                'customer_name.required' => 'Vui lòng nhập họ tên',
                'customer_phone.required' => 'Vui lòng nhập số điện thoại',
                'customer_phone.regex' => 'Số điện thoại không đúng định dạng',
                'customer_address.required' => 'Vui lòng nhập địa chỉ',
                'customer_email.required' => 'Vui lòng nhập email',
            ];

            $validate = \Illuminate\Support\Facades\Validator::make(
                $request->all(),
                [
                    'tour_id' => 'required|not_in:0',
                    'customer_name' => 'required',
                    'customer_phone' => 'required|regex:/^(0)[0-9]{9,11}$/',
                    'customer_address' => 'required',
                    'customer_email' => 'required',
                ],
                $translate
            );

            $json = new \stdClass();

            if ($validate->fails()) {
                $json->success = false;
                $json->errors = $validate->errors();
                $json->message = "Thao tác thất bại!";
                return \Illuminate\Support\Facades\Response::json($json);
            }


            $tour = Tour::query()->find($request->tour_id);
            $client = new \GuzzleHttp\Client();
            $googleSheetUrl = 'https://script.google.com/macros/s/AKfycbw-CuBIT2K70yZRc8dP_Y7ie39rkz4FJIloXN3_DCvjYORKBGupfJnp1_e8vpZrXd1wtQ/exec';

            $response = $client->post($googleSheetUrl, [
                'form_params' => [
                    'tour'    => $tour->title_short,
                    'customer_name'    => $request->customer_name,
                    'customer_address' => $request->customer_address,
                    'customer_phone'   => $request->customer_phone,
                    'customer_email'   => $request->customer_email,
                    'customer_time'   => $request->customer_time,
                    'customer_ticket'  => $request->customer_ticket,
                    'customer_content' => $request->customer_content,
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            if (!isset($result['status']) || $result['status'] != 'success') {
                throw new \Exception("Không thể gửi dữ liệu lên Google Sheet: " . $response->getBody());
            }

            DB::commit();
            return Response::json(['success' => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }

    }

    public function getInfoTour(Request $request, $id) {
        $tour = Tour::with('image')->find($id);
        $json = new \stdClass();
        $json->success = true;
        $json->data = $tour;
        $json->message = "Lấy dữ liệu thành công";

        return Response::json($json);
    }

    public function aboutUs(Request $request) {
        $banners = Banner::query()->with('image')->latest()->get();
        $config = Config::query()->first();
        $newBlogs = Post::with(['image'])->where(['status'=>1])
            ->orderBy('id','DESC')
            ->select(['id','name','slug'])
            ->limit(6)->get();

        return view('site.about_us', compact('config', 'banners', 'newBlogs'));
    }

    // ajax load product home page
    public function loadProductHomePage(Request $request)
    {
        $category = CategorySpecial::findBySlug($request->handle);
        $products = $category->products()->with([
            'image', 'galleries',
            'product_rates' => function($q) {
                $q->where('status', 2);
            }
            ])->where('status', 1)->limit(10)->orderBy('created_at', 'desc')->get();
        $html = '';
        foreach ($products as $product) {
            $html .= view('site.partials.item_product', compact('product', 'category'))->render();
        }

        return Response::json([
            'html' => $html,
        ]);
    }

    // ajax get product quick view
    public function getProductQuickView(Request $request)
    {
        $product = Product::findBySlug($request->slug);
        $product->image = $product->image;
        $product->image_back = $product->image_back;
        $product->galleries = $product->galleries->map(function ($gallery) {
            $gallery->image = $gallery->image;
            return $gallery;
        });
        $product->product_rates = $product->product_rates;
        $attributes = [];
        foreach ($product->attributeValues as $attribute) {
            if(!isset($attributes[$attribute->id])) {
                $attributes[$attribute->id] = [
                    'name' => $attribute->name,
                    'values' => [$attribute->pivot->value]
                ];
            } else {
                $attributes[$attribute->id]['values'][] = $attribute->pivot->value;
            }
        }
        $product->attributes = $attributes;
        // $html = view('site.partials.quick_view_product', compact('product'))->render();

        return Response::json([
            'data' => $product,
        ]);

    }

    public function showProductDetail($slug) {
        try {
            $categories = Category::getAllCategory();
            $product = Product::findSlug($slug);
            $attributes = [];
            foreach ($product->attributeValues as $attribute) {
                if(!isset($attributes[$attribute->id])) {
                    $attributes[$attribute->id] = [
                        'name' => $attribute->name,
                        'values' => [$attribute->pivot->value]
                    ];
                } else {
                    $attributes[$attribute->id]['values'][] = $attribute->pivot->value;
                }
            }
            $product->attributes = $attributes;

            // sản phẩm tương tự
            $productsRelated = $product->category->products()->whereNotIn('id', [$product->id])->orderBy('created_at', 'desc')->get();

            $category = Category::query()->where('id', $product->cate_id)->first();

            $arr_product_rate_images = [];
            foreach ($product->product_rates as $rate) {
                foreach ($rate->images as $image) {
                    $arr_product_rate_images[] = $image->path;
                }
            }

            return view('site.products.product_detail', compact('categories', 'product', 'productsRelated', 'category', 'arr_product_rate_images'));
        }catch (\Exception $exception) {
            return view('site.errors');
            Log::error($exception);
        }
    }


    public function showProductCategory(Request $request, $categorySlug = null)
    {
        $categories = Category::parent()->with('products')->orderBy('sort_order')->get();
        $category = Category::findBySlug($categorySlug);
        $sort = $request->get('sort') ?: 'lasted';
        if($category) {
            $category_parent_id = $category->parent ? $category->parent->id : null;
            $arr_category_id = array_merge($category->childs->pluck('id')->toArray(), [$category->id, $category_parent_id]);

            $products = Product::where('status', 1)->whereIn('cate_id', $arr_category_id)->orderBy('created_at', 'desc')->paginate(12);
        } else {
            $category = CategorySpecial::findBySlug($categorySlug);
            $products = $category->products()->where('status', 1)->orderBy('created_at', 'desc')->paginate(12);
        }

        $categorySpecial = CategorySpecial::query()->with(['products' => function($q) {$q->where('status', 1)->limit(5);}])
            ->has('products')
            ->where('type',10)
            ->where('show_home_page', 1)
            ->orderBy('order_number')->get();

        $title = $category->name;
        $short_des = $category->short_des;
        $title_sub = $category->name;
        if(! $category) {
            return view('site.errors');
        }

        return view('site.products.product_category', compact('categories', 'category', 'sort', 'categorySpecial', 'products', 'title', 'short_des', 'title_sub'));
    }

    public function loadMoreProduct(Request $request)
    {
        $category = Category::query()->find($request->cate_id);

        $products = Product::query()->where('status', 1);

        if ($sort = $request->get('sort')) {
            if ($sort == 'lasted') {
                $products->orderBy('created_at', 'desc');
            } else if ($sort == 'priceAsc') {
                $products->orderBy('price', 'asc');
            } else if ($sort == 'priceDesc') {
                $products->orderBy('price', 'desc');
            }
        } else {
             $products->orderBy('created_at', 'desc');
        }

        $product_all_ids = $category->products()->pluck('id')->toArray();

        if( $request->product_ids_load_more) {
            $products->whereIn('id', array_diff($product_all_ids, $request->product_ids_load_more));
        }

        $products = $products->where('cate_id', $category->id)->limit(1)->get();

        // mảng product id
        $product_ids = $products->pluck('id')->toArray();

        $html = '';

        $product_ids_ = array_merge($request->product_ids_load_more ?? [], $product_ids);

        $hasProductsNextPage = false;

        if($product_ids && Product::query()->whereNotIn('id', $product_ids_)->count()) $hasProductsNextPage = true;

        foreach ($products as $product) {
            $html .= view( 'site.partials.card_product', compact('product', 'category'))->render();
        }


        return Response::json([
            'html' => $html,
            'product_ids' => $product_ids,
            'hasProductsNextPage' => $hasProductsNextPage,
        ]);

    }




    // Blogs
    public function listBlog(Request $request, $slug)
    {
        $category = PostCategory::where('slug', $slug)->first();
        $data['blogs'] = Post::with(['image'])->where(['status'=>1,'cate_id'=>$category->id])
            ->orderBy('id','DESC')
            ->select(['id','name','intro','created_at','slug', 'cate_id'])
            ->paginate(12);

        $data['cate_title'] = $category->name;
        $data['categories'] = PostCategory::with([
            'posts' => function ($query){
                $query->where(['status'=>1])->get();
            }
        ])->where(['parent_id' => 0, 'show_home_page' => 1])->latest()->get();
        $data['newBlogs'] = Post::with(['image'])->where(['status'=>1])
            ->orderBy('id','DESC')
            ->select(['id','name','slug'])
            ->limit(6)->get();

        return view('site.blogs.list', $data);
    }

    public function indexBlog(Request $request, $slug = null)
    {
        $banners = Banner::query()->with('image')->latest()->get();
        $data['newBlogs'] = Post::with(['image'])->where(['status'=>1])
            ->orderBy('id','DESC')
            ->select(['id','name','slug'])
            ->limit(5)->get();
        $category = null;
        if($slug) {
            $category = PostCategory::findBySlug($slug);

            $data['blogs'] = Post::with(['image', 'category'])->where(['status'=>1])
                ->where('cate_id', $category->id)
                ->orderBy('id','DESC')
                ->select(['id','name','intro','created_at','slug'])
                ->paginate(10);
        } else {
            $data['blogs'] = Post::with(['image', 'category'])->where(['status'=>1])
                ->orderBy('id','DESC')
                ->select(['id','name','intro','created_at','slug'])
                ->paginate(10);

            $data['cate_title'] = 'Tin tức';
            $data['categories'] = PostCategory::with([
                'posts' => function ($query){
                    $query->where(['status'=>1])->get();
                }
            ])->where(['parent_id' => 0, 'show_home_page' => 1])->latest()->get();
        }


        return view('site.blogs.list', compact('data', 'banners', 'category'));
    }

    public function detailBlog(Request $request, $slug)
    {
        $banners = Banner::query()->with('image')->latest()->get();
        $blog = Post::with(['image', 'user_create'])->where('slug', $slug)->first();
        $category = PostCategory::where('id', $blog->cate_id)->first();

        $data['other_blogs'] = Post::with(['image'])->where(['status'=>1,'cate_id'=>$blog->cate_id])
        ->where('id', '!=', $blog->id)
        ->select(['id','name','intro','created_at','slug'])
        ->limit(6)->inRandomOrder()->get();

        $data['newBlogs'] = Post::with(['image'])->where(['status'=>1])
            ->orderBy('id','DESC')
            ->select(['id','name','slug'])
            ->limit(5)->get();

        $data['blog_title'] = $blog->name;
        $data['blog_des'] = $blog->intro;
        $data['categories'] = PostCategory::with([
            'posts' => function ($query){
                $query->where(['status'=>1])->get();
            }
        ])->where(['parent_id' => 0, 'show_home_page' => 1])->latest()->get();
        $data['newBlogs'] = Post::with(['image'])->where(['status'=>1])
        ->orderBy('id','DESC')
        ->select(['id','name','slug'])
        ->limit(6)->get();
        $data['blog'] = $blog;
        $data['cate_title'] = $category->name;
        $data['category'] = $category;

        return view('site.blogs.detail', compact('data', 'banners'));
    }

    public function policy(Request $request) {
        $policy = PolivicyDetail::query()->get();

        return view('site.chinh_sach_bao_mat', compact('policy'));
    }

    public function terms(Request $request) {
        $term = Term::query()->with(['details'])->find(1);

        return view('site.dieu_khoan', compact('term'));
    }


    public function moving(Request $request) {
        $moving = Moving::find(1);

        return view('site.moving', compact('moving'));
    }



    public function getDataMedia(Request $request, $id) {
        $media = Document::query()->with('galleries')->find($id);

        $json = new \stdClass();
        $json->success = true;
        $json->message = "Thao tác thành công!";
        $json->data = view('site.partials.media_img', compact('media'))->render();

        return Response::json($json);
    }

    // Tìm kiếm
    public function autoSearchComplete(Request $request)
    {
        if (isset($request->keyword)) {
            $products = Product::with(['image'])->where('name','LIKE','%'.$request->keyword.'%')->where('status', 1)->orderBy('id','DESC')->limit(10)->get();
            $view = view("site.partials.ajax_search_results",compact('products'))->render();
        } else {
            $view = '';
        }

        return Response::json([
            'html'=>$view
        ]);
    }

    public function resetData() {
        \Illuminate\Support\Facades\DB::table('orders')->truncate();
        \Illuminate\Support\Facades\DB::table('contacts')->truncate();
    }

    // laster buy products
    public function lasterBuyProducts() {
        $product = \DB::table('products')
        ->where('status', 1)
        ->leftJoin('files', function($join) {
            $join->on('files.model_id', '=', 'products.id')
            ->where('files.custom_field', 'image')->where('files.model_type', Product::class);
        })
        ->inRandomOrder()->first(['products.id', 'products.name', 'products.slug', 'files.path']);
        return Response::json([
            'product' => $product,
        ]);
    }

    // review
    public function submitReview(Request $request) {
        $rule  =  [
            'name' => 'required',
            'email'  => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone'  => 'required|regex:/^(0)[0-9]{9,11}$/',
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'required',
            'galleries' => 'required|array|min:1|max:5',
            'galleries.*.image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'desc' => 'required',
            'product_id' => 'required|exists:products,id',
        ];

        $validate = Validator::make(
            $request->all(),
            $rule,
            [
                'name.required' => 'Vui lòng nhập họ tên',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại không đúng định dạng',
                'email.required' => 'Vui lòng nhập email',
                'email.regex' => 'Email không đúng định dạng',
                'rating.required' => 'Vui lòng đánh giá sản phẩm',
                'rating.numeric' => 'Đánh giá không hợp lệ',
                'rating.min' => 'Đánh giá không hợp lệ',
                'rating.max' => 'Đánh giá không hợp lệ',
                'title.required' => 'Vui lòng nhập tiêu đề',
                'galleries.required' => 'Vui lòng chọn ít nhất 1 hình ảnh',
                'galleries.array' => 'Dữ liệu không hợp lệ',
                'galleries.min' => 'Vui lòng chọn ít nhất 1 hình ảnh',
                'galleries.max' => 'Vui lòng chọn tối đa 5 hình ảnh',
                'desc.required' => 'Vui lòng nhập nội dung đánh giá',
                'galleries.*.image.image' => 'Vui lòng chọn file hình ảnh',
                'galleries.*.image.mimes' => 'File không hợp lệ',
                'galleries.*.image.max' => 'File không được lớn hơn 5MB',
                'product_id.required' => 'Sản phẩm không hợp lệ',
                'product_id.exists' => 'Sản phẩm không hợp lệ',
            ]
        );


        if ($validate->fails()) {
            return $this->responseErrors('Gửi yêu cầu thất bại!', $validate->errors());
        }

        $store_data = [
            'product_id' => $request->product_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'rating' => $request->rating,
            'title' => $request->title,
            'desc' => $request->desc,
        ];

		DB::beginTransaction();
		try {
			$object = new ProductRate();
			$object->fill($store_data);
			$object->save();

            $galleries = $request->galleries;
			foreach ($galleries as $gallery) {
                if (isset($gallery['image'])) {
                    $file = $gallery['image'];
                    FileHelper::uploadFile($file, 'product_rate', $object->id, ProductRate::class, 'image', 1);
                }
            }

			DB::commit();
			return $this->responseSuccess('Gửi đánh giá thành công!');
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    // Tạo thiết kế
    public function productCustom(Request $request) {
        $product = Product::with(['image', 'image_back', 'galleries'])->find($request->product_id);
        return view('site.products.product_custom', compact('product'));
    }

    // Tìm kiếm trang list product

    // đặt hàng thiết kế
    public function designOrder(Request $request) {
        $formCustom = json_decode($request->input('formCustom'), true);
        $dataFront = json_decode($request->input('dataFront'), true);
        $dataBack = json_decode($request->input('dataBack'), true);
        $dataFrontImage = $request->file('dataFrontImage');
        $dataBackImage = $request->file('dataBackImage');
        $imageFront = $request->file('imageFront');
        $imageBack = $request->file('imageBack');

        $rule  =  [
            'name' => 'required',
            'email'  => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone'  => 'required|regex:/^(0)[0-9]{9,11}$/',
            'address' => 'required',
            'product_id' => 'required|exists:products,id',
        ];

        $validate = Validator::make(
            $formCustom,
            $rule,
            [
                'name.required' => 'Vui lòng nhập họ tên',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại không đúng định dạng',
                'address.required' => 'Vui lòng nhập địa chỉ',
                'email.required' => 'Vui lòng nhập email',
                'email.regex' => 'Email không đúng định dạng',
                'product_id.required' => 'Sản phẩm không hợp lệ',
                'product_id.exists' => 'Sản phẩm không hợp lệ',
            ]
        );

        if ($validate->fails()) {
            return $this->responseErrors('Gửi yêu cầu thất bại!', $validate->errors());
        }

        $design_order = new DesignOrder();
        $design_order->customer_name = $formCustom['name'];
        $design_order->customer_phone = $formCustom['phone'];
        $design_order->customer_email = $formCustom['email'];
        $design_order->customer_address = $formCustom['address'];
        $design_order->customer_note = $formCustom['note'];
        $design_order->product_id = $formCustom['product_id'];
        $design_order->product_name = $formCustom['product_name'];
        $design_order->product_price = $formCustom['product_price'];
        $design_order->product_quantity = $formCustom['product_quantity'] ?? 1;
        $design_order->product_color = $formCustom['product_color'] ?? null;
        $design_order->product_size_length = $formCustom['product_size_length'] ?? null;
        $design_order->product_size_width = $formCustom['product_size_width'] ?? null;
        $design_order->product_size_height = $formCustom['product_size_height'] ?? null;
        $design_order->product_attributes = $formCustom['product_attributes'] ?? null;
        $design_order->save();

        if ($imageFront) {
            FileHelper::uploadFileToCloudflare($imageFront, $design_order->id, DesignOrder::class, 'image_front');
        }
        if ($imageBack) {
            FileHelper::uploadFileToCloudflare($imageBack, $design_order->id, DesignOrder::class, 'image_back');
        }

        // Gán ảnh vào đúng vị trí trong dataFront
        if ($dataFront) {
            foreach ($dataFront as $index => $layer) {
                $design_detail = new DesignDetail();
                $design_detail->design_order_id = $design_order->id;
                $design_detail->group = $layer['group'];
                $design_detail->type = $layer['type'];
                $design_detail->design_text = $layer['design_text'];
                $design_detail->design_color = $layer['design_color'] ?? null;
                $design_detail->design_font = $layer['design_font'] ?? null;
                $design_detail->design_font_size = $layer['design_font_size'] ?? null;
                $design_detail->design_font_weight = $layer['design_font_weight'] ?? null;
                $design_detail->design_font_style = $layer['design_font_style'] ?? null;
                $design_detail->save();
                if ($layer['type'] === 'image' && $request->hasFile("dataFrontImage.$index")) {
                    $image = $request->file("dataFrontImage.$index");
                    FileHelper::uploadFileToCloudflare($image, $design_detail->id, DesignDetail::class, 'image_layer');
                }
            }
        }

        // Gán ảnh vào đúng vị trí trong dataBack
        if ($dataBack) {
            foreach ($dataBack as $index => $layer) {
                $design_detail = new DesignDetail();
                $design_detail->design_order_id = $design_order->id;
                $design_detail->group = $layer['group'];
                $design_detail->type = $layer['type'];
                $design_detail->design_text = $layer['design_text'];
                $design_detail->design_color = $layer['design_color'] ?? null;
                $design_detail->design_font = $layer['design_font'] ?? null;
                $design_detail->design_font_size = $layer['design_font_size'] ?? null;
                $design_detail->design_font_weight = $layer['design_font_weight'] ?? null;
                $design_detail->design_font_style = $layer['design_font_style'] ?? null;
                $design_detail->save();
                if ($layer['type'] === 'image' && $request->hasFile("dataBackImage.$index")) {
                    $image = $request->file("dataBackImage.$index");
                    FileHelper::uploadFileToCloudflare($image, $design_detail->id, DesignDetail::class, 'image_layer');
                }
            }
        }

        return $this->responseSuccess('Đặt hàng thành công!');
    }

    public function clearData()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        User::query()->where('id',2 )->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
