<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\ProductCategory;
use App\Model\G7\G7Product;
use App\Model\G7\G7ProductPrice;
use App\Model\Traits\HasTagTrait;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Illuminate\Support\Facades\Auth;
use App\Model\Common\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use File as FileSystem;

class Product extends BaseModel
{
    use HasTagTrait;

    const CON_HANG = 1;
    const HET_HANG = 2;

    const IS_PIN = 1;
    const NOT_PIN = 2;

    const STATE = [
        1 => 'Còn hàng',
        2 => 'Hết hàng'
    ];

    protected $fillable = ['name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at',
        'price', 'cate_id', 'base_price', 'body', 'intro', 'slug', 'short_des', 'manufacturer_id', 'origin_id'];

    use Sluggable;
    use SluggableScopeHelpers;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public const STATUS_SUCCESS = 1;
    public const STATUS_DANGER = 0;

    public const STATUSES = [
        [
            'id' => 1,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Khóa',
            'type' => 'danger'
        ]
    ];

    public function canDelete()
    {
        return true;
    }

    public function canEdit()
    {
        return Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function image_back()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_back');
    }

     public function galleries()
     {
         return $this->hasMany(ProductGallery::class, 'product_id', 'id');
     }

    public function product_rates()
    {
        return $this->hasMany(ProductRate::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cate_id', 'id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'product_posts', 'product_id', 'post_id')->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values', 'product_id', 'attribute_id')->withPivot('value');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function origin()
    {
        return $this->belongsTo(Origin::class, 'origin_id');
    }

    public function category_specials()
    {
        return $this->belongsToMany(CategorySpecial::class, 'product_category_special', 'product_id', 'category_special_id');
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class, 'product_id');
    }

    public function getLinkAttribute()
    {
        if ($this->use_url_custom) {
            return '/san-pham/' . $this->url_custom;
        }
        return route('front.product-detail', $this->slug);
    }

    public function getPercentDiscountAttribute()
    {
        $percentDiscount = round((($this->base_price - $this->price) / $this->base_price) * 100 );

        return $percentDiscount;
    }


    public static function searchByFilter($request)
    {
        $result = self::with([
            'category',
            'image',
            'image_back',
        ]);

        if (!empty($request->name)) {
            $result = $result->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%')
                ->orWhere('code', 'like', '%' . $request->name . '%');
            });
        }

        if (!empty($request->cate_id)) {
            $result = $result->where('cate_id', $request->cate_id);
        }

        if (!empty($request->cate_special_id)) {
            $cate_special_id = $request->cate_special_id;
            $result = $result->whereHas('category_specials', function ($q) use ($cate_special_id) {
                $q->where('category_special_id', $cate_special_id);
            });
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (!empty($request->state)) {
            $result = $result->where('state', $request->state);
        }


        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::select(['id', 'name'])
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        $product = self::where('id', $id)
            ->with([
                'category' => function ($q) {
                    $q->select(['id', 'name']);
                },
                'image',
                'image_back',
                'manufacturer',
                'videos',
                'galleries' => function ($q) {
                    $q->select(['id', 'product_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'attributeValues'
            ])
            ->firstOrFail();

        $product->category_special_ids = $product->category_specials->pluck('id')->toArray();
        $product->attributeValues->map(function ($attribute) {
            $attribute->attribute_id = $attribute->id;
            $attribute->value = $attribute->pivot->value;
            return $attribute;
        });

        $tags = $product->tags->map(function ($tag) {
            $tag->name = '<a href="'.route('front.search').'?keyword='.$tag->name.'">'.$tag->name.'</a>' ;
            return $tag;
        });

        $product->tags_str = $tags->implode('name', ', ');

        return $product;
    }

    public static function findSlug($slug)
    {
        $object = self::findBySlug($slug);

        if (!$object) {
            $object = self::query()->where('url_custom', $slug)->first();
        }

        return self::where('id', $object->id)
            ->with([
                'category' => function ($q) {
                    $q->select(['id', 'name', 'slug']);
                },
                'image',
                'image_back',
                'galleries' => function ($q) {
                    $q->select(['id', 'product_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'attributeValues',
                'product_rates' => function ($q) {
                    $q->with(['images'])->where('status', 2);
                }
            ])
            ->firstOrFail();
    }

    public static function getRelate($id, $cate_id)
    {
        return self::where('id', '<>', $id)
            ->where([
                'status' => 1,
                'cate_id' => $cate_id
            ])
            ->orderBy('created_at', 'desc')->get();
    }

    public function generateCode()
    {
        $this->code = "HH-" . generateCode(6, $this->id);
        $this->save();
    }

    public function syncAttributes($attributes)
    {
        // $this->attributeValues()->detach();
        // $syncData = [];
        // foreach ($attributes as $attribute) {
        //     $syncData[$attribute['attribute_id']] = [
        //         'value' => $attribute['value'],
        //         'product_id' => $this->id,
        //     ];
        // }

        // $this->attributeValues()->sync($syncData);
        $this->attributeValues()->detach();
        foreach ($attributes as $attribute) {
            $this->attributeValues()->attach($attribute['attribute_id'], ['value' => $attribute['value']]);
        }
    }

    public function syncGalleries($galleries)
    {
        if ($galleries) {
            $exist_ids = [];
            foreach ($galleries as $g) {
                if (isset($g['id'])) array_push($exist_ids, $g['id']);
            }
            $deleted = ProductGallery::where('product_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, ProductGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = ProductGallery::find($g['id']);
                else $gallery = new ProductGallery();

                $gallery->product_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, ProductGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    // FileHelper::uploadFile($file, 'product_gallery', $gallery->id, ProductGallery::class, null, 99);
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, ProductGallery::class, null);
                }
            }
        } else {
            $deleted = ProductGallery::where('product_id', $this->id)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, ProductGallery::class);
                }
                $item->removeFromDB();
            }
        }
    }

    public function syncDocuments($documents, $folder)
    {
        $folderDir = implode(DIRECTORY_SEPARATOR, ["public", "uploads", $folder]);
        $attachments = [$this->attachments];

        if ($documents) {
            foreach ($documents as $document)  {
                $filename = $document->getClientOriginalName();
                $name = Str::slug(str_replace("/", "", $filename));
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $destinationFileName = $name . '-' . time() . '-' . randomString(4);
                $destinationFile = $destinationFileName . '.' . $extension;
                $destinationPath = base_path() . DIRECTORY_SEPARATOR . $folderDir;

                if (!is_dir($destinationPath)) {
                    FileSystem::makeDirectory($destinationPath, 0777, true);
                }

                $document->move($destinationPath, $destinationFile);

                $path = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["uploads", $folder, $destinationFile]);

                array_push($attachments, $path);
            }
            $this->attachments = join(', ', $attachments);
            $this->save();
        }
    }

    public static function filter($request, $product_ids)
    {
        $productIsPin = self::query()->where('is_pin', 1)->whereIn('id', $product_ids)
            ->with([
                'category' => function ($q) {
                    $q->select(['id', 'name']);
                },
                'image',
                'manufacturer',
                'galleries' => function ($q) {
                    $q->select(['id', 'product_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'attributeValues'
            ])
            ->select(['*']);

        $productInStock = self::query()->where('state', 1)->whereIn('id', $product_ids)
            ->with([
                'category' => function ($q) {
                    $q->select(['id', 'name']);
                },
                'image',
                'manufacturer',
                'galleries' => function ($q) {
                    $q->select(['id', 'product_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'attributeValues'
            ])
            ->select(['*']);

        $productOutStock = self::query()->where('state', 2)->whereIn('id', $product_ids)
            ->with([
                'category' => function ($q) {
                    $q->select(['id', 'name']);
                },
                'image',
                'manufacturer',
                'galleries' => function ($q) {
                    $q->select(['id', 'product_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'attributeValues'
            ])
            ->select(['*']);

        if ($keyword = $request->get('keyword')) {
            $productIsPin->where(function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('manufacturer', function ($q) use ($keyword) {
                            $q->where('manufacturers.name', 'like', '%' . $keyword . '%');
                        });
                })->orWhereHas('tags', function ($q) use ($keyword){
                    $q->where('tags.name', 'like', '%' . $keyword . '%');
                });
            });

            $productInStock->where(function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('manufacturer', function ($q) use ($keyword) {
                            $q->where('manufacturers.name', 'like', '%' . $keyword . '%');
                        });
                })->orWhereHas('tags', function ($q) use ($keyword){
                    $q->where('tags.name', 'like', '%' . $keyword . '%');
                });
            });

            $productOutStock->where(function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('manufacturer', function ($q) use ($keyword) {
                            $q->where('manufacturers.name', 'like', '%' . $keyword . '%');
                        });
                })->orWhereHas('tags', function ($q) use ($keyword){
                    $q->where('tags.name', 'like', '%' . $keyword . '%');
                });
            });

        }

        if ($request->get('minPrice')) {
            $productIsPin->where('price', '>=', $request->get('minPrice'));
            $productInStock->where('price', '>=', $request->get('minPrice'));
            $productOutStock->where('price', '>=', $request->get('minPrice'));
        }

        if ($request->get('maxPrice')) {
            $productIsPin->where('price', '<=', $request->get('maxPrice'));
            $productInStock->where('price', '<=', $request->get('maxPrice'));
            $productOutStock->where('price', '<=', $request->get('maxPrice'));
        }

        $query = $productIsPin->union($productInStock)->union($productOutStock)->orderBy('is_pin')->orderBy('state');

        if ($sort = $request->get('sort')) {
            if ($sort == 'lasted') {
                $query->orderBy('created_at', 'desc');
            } else if ($sort == 'priceAsc') {
                $query->orderBy('price', 'asc');
            } else if ($sort == 'priceDesc') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function scopeSort($query, $request)
    {
        $query->orderBy('is_pin')->orderBy('state')->orderBy('updated_at', 'desc');
    }

    public function scopeFilterV2($query, $filters)
    {
        $query = self::query();
        if ($filters) {
            $filters = array_merge(...array_values($filters));
            if (@$filters['manu']) {
                $query->whereIn('manufacturer_id', $filters['manu']);
            }
            if (@$filters['origin']) {
                $query->whereIn('origin_id', $filters['origin']);
            }

            if (@$filters['prices']) {
                $prices = $filters['prices'];

                $query->where(function ($q) use ($prices) {
                    foreach ($prices as $price) {
                        $price = json_decode($price, true);
                        if (count($price) > 1) {
                            $q->orWhere(function ($q) use ($price) {
                                $q->where('price', '>=', $price[0])
                                    ->where('price', '<=', $price[1]);
                            });
                        } else {
                            if ($price[0] == 16000000) {
                                $q->orWhere('price', '>=', 15000000);
                            } else {
                                $q->orWhere('price', '<=', $price[0]);
                            }
                        }
                    }
                });
            }
        }

        return $query;
    }
}
