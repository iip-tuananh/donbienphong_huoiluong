<?php

namespace App\Http\Controllers\Front;

use App\Helpers\FileHelper;
use App\Http\Traits\ResponseTrait;
use App\Mail\NewOrder;
use App\Model\Admin\ArticleSubmission;
use App\Model\Admin\Config;
use App\Model\Admin\Order;
use App\Model\Admin\OrderDetail;
use App\Model\Admin\Product;
use App\Model\Admin\Rent;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Admin\Category;
use App\Model\Admin\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Kjmtrue\VietnamZone\Models\Province;
use Tymon\JWTAuth\Facades\JWTAuth;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Province as Vanthao03596Province;
use Vanthao03596\HCVN\Models\Ward;

class CustomerController extends Controller
{
    use ResponseTrait;

    public function getProfile(Request $request) {
        $data['customer'] = auth('customer')->user();

        $data['blogs'] = ArticleSubmission::query()
            ->where('customer_id', $data['customer']['id'])->latest()
            ->paginate(15);

        return view('site.customer.profile', $data);
    }

    public function updateProfile(Request $request) {
        $validateArr = [
            'fullname' => 'required',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png|max:5000'
        ];

        if($request->current_password && $request->new_password) {
            $validateArr['current_password'] = 'required';
            $validateArr['new_password'] = 'required|min:6|confirmed';
        }


        $validate = Validator::make(
            $request->all(),
            $validateArr, [
                'current_password.required'     => 'Vui lòng nhập mật khẩu hiện tại.',
                'new_password.required'         => 'Vui lòng nhập mật khẩu mới.',
                'new_password.min'              => 'Mật khẩu mới phải ít nhất 6 ký tự.',
                'new_password.confirmed'        => 'Xác nhận mật khẩu không khớp.',
            ]
        );




        $json = new \stdClass();

        if ($validate->fails()) {
            $json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
        }

        $customer = Customer::query()->find(auth('customer')->user()->id);


        if($request->current_password && $request->new_password) {
            if (! Hash::check($request->current_password, $customer->password)) {
                $json->success = false;
                $json->errors  = ['current_password' => ['Mật khẩu hiện tại không đúng.']];
                $json->message = 'Thao tác thất bại!';
                return Response::json($json);
            }

            $customer->password = bcrypt($request->new_password);
            $customer->save();
        }


        $customer->fullname = $request->fullname;

        $customer->save();

        if($request->avatar) {
            if($customer->avatar) {
                FileHelper::deleteFileFromCloudflare($customer->avatar, $customer->id, Customer::class, 'image');
            }
            FileHelper::uploadFileToCloudflare($request->avatar, $customer->id, Customer::class, 'image');
        }

        $json->success = true;
        $json->message = "Thao tác thành công!";
        return Response::json($json);
    }
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'current_password'              => 'required',
            'new_password'                  => 'required|min:6|confirmed',
        ], [
            'current_password.required'     => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'              => 'Mật khẩu mới phải ít nhất 6 ký tự.',
            'new_password.confirmed'        => 'Xác nhận mật khẩu không khớp.',
        ]);

        $json = new \stdClass();

        if ($validator->fails()) {
            $json->success = false;
            $json->errors  = $validator->errors();
            $json->message = 'Thao tác thất bại!';
            return Response::json($json);
        }

        $customer = auth('customer')->user();

        if (! Hash::check($request->current_password, $customer->password)) {
            $json->success = false;
            $json->errors  = ['current_password' => ['Mật khẩu hiện tại không đúng.']];
            $json->message = 'Thao tác thất bại!';
            return Response::json($json);
        }

        $customer->password = bcrypt($request->new_password);
        $customer->save();

        $json->success = true;
        $json->message = 'Đổi mật khẩu thành công!';

        return Response::json($json);
    }

    public function getListRentalOrders(Request $request) {
        $orders = Rent::query()->with(['variant' => function($q) {
            $q->with(['image', 'product']);
        }, 'warehouse'])
            ->where('customer_id', auth('customer')->id())->latest()->paginate(10);
        $data['orders'] = $orders;

        return view('site.customers.rental_order', $data);
    }

    public function getListBuyOrders(Request $request) {
        $orders = Order::query()
            ->withCount('details')
            ->with(['customer', 'details.product'])
            ->where('customer_id', auth('customer')->id())
            ->latest()->paginate(10);

        $orders->getCollection()->transform(function($order) {
            $order->total_amount = $order->details->sum(function($d) {
                return $d->price * $d->qty;
            });
            return $order;
        });

        $data['orders'] = $orders;

        return view('site.customers.buy_order', $data);
    }

    public function getDetailOrder(Request $request, $id) {
        $order = Order::query()
            ->withCount('details')
            ->with(['customer', 'details' => function($q) {
                $q->with(['product.product', 'product.image']);
            }])
            ->where('id', $id)
            ->first();

        $data['order'] = $order;

        return view('site.customers.buy_order_detail', $data);
    }


    public function getListProducts(Request $request, $parentSlug, $slug = null) {
        $filterGroups = collect($request->query())
            ->except(['page', 'sort'])
            ->map(function ($value) {
                return $value ? explode(',', $value) : [];
            })
            ->filter(function ($arr) {
                return !empty($arr);
            })
            ->all();
        $tags = Tag::query()->pluck('id', 'code');

        $currentSlug = $slug ?: $parentSlug;
        $category = Category::with(['image', 'parent'])
            ->where('slug', $currentSlug)
            ->firstOrFail();

        $categoryParent = $slug ? $category->parent : $category;
        $childCategories = $this->categoryService->getChildCategory($categoryParent);
        $childIds = $childCategories->pluck('id')->all();

        $allCategories = Category::where('parent_id', 0)
            ->with(['childs' => function ($q) {
                $q->withCount([
                    'products as products_with_default_count' => function ($q2) {
                        $q2->whereHas('variants', function ($q3) {
                            $q3->where('is_default', 1);
                        });
                    }
                ]);
            }])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::withCount([
            'products as products_with_default_count' => function ($q2) {
                $q2->whereHas('variants', function ($q3) {
                    $q3->where('is_default', 1);
                });
            }
        ])
            ->whereIn('id', $childIds)
            ->latest()
            ->get();

        //1108

        if(! $category->parent) {
            $cateIds = $childIds;
        } else {
            $cateIds = [$category->id];
        }


        $productIdsInProductCategory = ProductCategory::query()->whereIn('category_id', $cateIds)->pluck('product_id')->toArray();


        $productQuery = Product::query()->whereIn('id', $productIdsInProductCategory);
        //END 1108


        foreach ($filterGroups as $kTag => $filterGroup) {
            $productTagIds = ProductTagValue::query()->where('tag_id', $tags[$kTag])
                ->whereIn('tag_value_id', $filterGroup)->pluck('product_id')->toArray();

            $productQuery->whereIn('id', $productTagIds);
        }



        $allowed = $productQuery->pluck('id');

        $subLatest = DB::table('product_model_has_items as pmi')
            ->join('products as p', 'p.id', '=', 'pmi.product_id')
            ->whereColumn('pmi.product_model_id', 'product_models.id')
            ->when($allowed->isNotEmpty(), function ($q) use ($allowed) {
                $q->whereIn('p.id', $allowed);
            })
            ->orderByDesc('p.created_at')
            ->limit(1)
            ->select('pmi.product_id');

        $productModels = ProductModel::where('cate_id', $categoryParent->id)
            ->addSelect(['picked_product_id' => $subLatest])
            ->get();

        $products = Product::whereIn('id', $productModels->pluck('picked_product_id')->filter())
            ->get()
            ->keyBy('id');

        $productModels->each(function ($m) use ($products) {
            $m->setRelation('pickedProduct', $products->get($m->picked_product_id));
        });

        $productModelIds = $productModels->pluck('pickedProduct.id')->filter(function ($v) { return !is_null($v); })
            ->unique()->values()->all();


        $productQuery->whereIn('id', $productModelIds);






        $productIds = $productQuery->pluck('id')->all();

        $sortMap = [
            'name_asc'   => ['products.name', 'asc'],
            'name_desc'  => ['products.name', 'desc'],
            'price_asc'  => ['min_daily_rental_price', 'asc'],
            'price_desc' => ['min_daily_rental_price', 'desc'],
            'date_asc'   => ['created_at', 'asc'],
            'date_desc'  => ['created_at', 'desc'],
        ];
        if (isset($sortMap[$request->get('sort')])) {
            list($sortColumn, $sortDirection) = $sortMap[$request->get('sort')];
        } else {
            $sortColumn = 'id';
            $sortDirection = 'desc';
        }

        $productVariants = $this->productService
            ->getVariantsByProduct($productIds, null, true)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12)
            ->appends($request->only('sort'));

        $productVariants->getCollection()->transform(function ($variant) {
            $attrs = [];
            if ($variant->product && $variant->product->attrs) {
                $attributeGroups = $variant->product->attrs->groupBy('id');
                foreach ($attributeGroups as $group) {
                    $first = $group->first();
                    $attrs[] = [
                        'name'   => $first->name,
                        'image'  => isset($first->image->path) ? $first->image->path : '',
                        'values' => $group->pluck('pivot.value')->implode(', '),
                    ];
                }
            }
            $variant->attrs = $attrs;
            return $variant;
        });

        $tags = Tag::with('values')
            ->where('cate_id', $categoryParent->id)
            ->get();

        return view('site.products.product_category', compact(
            'productVariants',
            'category',
            'categoryParent',
            'categories',
            'allCategories',
            'tags'
        ));
    }


}
