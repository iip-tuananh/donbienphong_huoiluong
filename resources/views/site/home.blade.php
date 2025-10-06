@extends('site.layouts.master')
@section('title'){{ $config->web_title }}@endsection
@section('description'){{ strip_tags(html_entity_decode($config->introduction)) }}@endsection
@section('image'){{@$config->image->path ?? ''}}@endsection

@section('css')

@endsection

@section('content')
    <style>
        /* Giới hạn tiêu đề bài viết còn tối đa 2 dòng + ellipsis */
        .list-post-content-1 h3 a{
            display: -webkit-box;
            -webkit-line-clamp: 2;          /* số dòng tối đa */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;

            /* đảm bảo mọi thẻ có cùng chiều cao dù chỉ 1 dòng hay 2 dòng */
            line-height: 1.4;                /* điều chỉnh theo font của bạn */
            min-height: calc(1.4em * 2);     /* đúng bằng 2 dòng */
        }


    </style>

    <style>
        /* Tiêu đề */
        .list-post-content-1 h3{
            margin:0 0 6px;
            line-height:1.35;
        }

        /* Hiển thị ngày và badge trên CÙNG 1 dòng */
        .list-post-content-1 .post-date{
            /* Ghi đè các rule cũ có thể đang set block/clear/width:100% */
            display:inline-flex !important;
            align-items:center;
            gap:6px;
            width:auto;            /* override nếu theme set 100% */
            clear:none;            /* override nếu theme set clear:both */
            color:#6b7280;
            font-size:12px;
            line-height:1;
            vertical-align:middle;
        }
        .list-post-content-1 .post-date i{ font-size:12px; }

        /* Badge “Tin hot” đặt sát sau post-date */
        .list-post-content-1 .post-date + .hot-badge{
            margin-left:10px;      /* khoảng cách giữa ngày và badge */
        }

        .list-post-content-1 .hot-badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:3px 10px;
            border-radius:999px;
            background:linear-gradient(135deg,#ff7a59 0%,#ff3d3d 100%);
            color:#fff;
            font-weight:600;
            font-size:12px;
            line-height:1;
            box-shadow:0 4px 12px rgba(255,61,61,.25), inset 0 0 0 1px rgba(255,255,255,.25);
            vertical-align:middle;
        }
        .list-post-content-1 .hot-badge__icon{ font-size:14px; line-height:1; }

        /* Tùy chọn: nhảy lửa nhẹ */
        @keyframes flame-pop{0%,100%{transform:translateY(0)}50%{transform:translateY(-1px)}}
        @media (prefers-reduced-motion:no-preference){
            .list-post-content-1 .hot-badge__icon{ animation:flame-pop 1.6s ease-in-out infinite; }
        }

        /* Mobile: cho phép xuống dòng nếu hẹp */
        @media (max-width:576px){
            .list-post-content-1 .post-date{ margin-bottom:6px; }
        }

    </style>

    <style>
        /* Reset bố cục của block này về kiểu thường, trái -> phải */
        .list-post-content-1{
            display:block !important;          /* ghi đè nếu theme dùng flex */
            text-align:left;
        }

        /* Tiêu đề trên, meta ở dòng dưới */
        .list-post-content-1 h3{
            margin:0 0 6px;
        }

        /* Đặt ngày & badge cạnh nhau bên trái */
        .list-post-content-1 .post-date,
        .list-post-content-1 .hot-badge{
            display:inline-flex !important;    /* đứng cùng hàng */
            align-items:center;
            vertical-align:middle;
            float:none !important;
            width:auto !important;
        }

        /* Khoảng cách nhỏ giữa ngày và badge */
        .list-post-content-1 .post-date{ gap:6px; }
        .list-post-content-1 .post-date + .hot-badge{ margin-left:8px; }

        /* Style badge (như cũ) */
        .list-post-content-1 .hot-badge{
            padding:3px 10px; border-radius:999px;
            background:linear-gradient(135deg,#ff7a59 0%,#ff3d3d 100%);
            color:#fff; font-weight:600; font-size:12px; line-height:1;
            box-shadow:0 4px 12px rgba(255,61,61,.25), inset 0 0 0 1px rgba(255,255,255,.25);
        }
        .list-post-content-1 .hot-badge__icon{ font-size:14px; line-height:1; }


    </style>
    <style>
        .post-intro {
            display: block;                                 /* đảm bảo là khối */
            width: 100%;                                    /* NGĂN bị co về 0 */
            max-width: 100%;
            float: none;                                    /* tránh bị float kéo về 0 */
            position: static;                               /* tránh absolute gây co width */
            white-space: normal;
            word-break: break-word;
        }

        /* Clamp 3 dòng + ellipsis */
        .post-intro.clamp-3{
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            line-height: 1.6;
            max-height: calc(1.6em * 3);
        }

        /* Firefox mới hỗ trợ line-clamp không prefix */
        @supports (line-clamp: 3) {
            .post-intro.clamp-3 {
                display: block;
                line-clamp: 3;
                block-size: calc(1.6em * 3);
            }
        }
    </style>

    <div class="content" ng-controller="homePage">
        <!-- hero-slider-wrap     -->
        <div class="hero-carousel-wrap fl-wrap">
            <div class="hero-carousel fl-wrap full-height">
                <div class="swiper-container full-height">
                    <div class="swiper-wrapper">
                        @foreach ($banners as $item)
                            <!-- swiper-slide    -->
                            <div class="swiper-slide">
                                <div class="hero-carousel-item fl-wrap">
                                    <div class="grid-post-item  bold_gpi  fl-wrap">
                                        <div class="grid-post-media gpm_sing">
                                            <div class="bg" data-bg="{{$item->image->path ?? ''}}"></div>
                                            <div class="grid-post-media_title">
                                                <a class="post-category-marker" href="{{$item->link}}"></a>
                                                <h4><a href="{{$item->link}}">{{($item->title)}}</a></h4>
                                                <p class="text-white" style="color: #fff !important;">{{($item->intro)}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- swiper-slide end    -->
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="hero-carousel-controls">
                <div class="hero-carousel-pag"></div>
                <div class="clearfix"></div>
                <div class="hc-cont hc-cont-next"><i class="fas fa-caret-right"></i></div>
                <div class="hc-cont hc-cont-prev"><i class="fas fa-caret-left"></i></div>
            </div>
        </div>


        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="main-container fl-wrap fix-container-init">
                            <div class="section-title" ng-cloak>
                                <h2><% getCategoryCurrent(currentCateId).name %></h2>
                                <h4><% getCategoryCurrent(currentCateId).intro %></h4>
                                <div class="ajax-nav">
                                    <ul>
                                        @foreach($postCategories as $postCate)
                                            <li><a href="javascript:void(0)" ng-click="getPostByCate({{ $postCate->id }})"
                                                   ng-class="{'current_page': currentCateId == {{ $postCate->id }} }"
                                                >{{ $postCate->name }}</a></li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                            <div class="ajax-wrapper fl-wrap">
                                <div class="ajax-loader"><img src="/site/images/loading.gif" alt=""/></div>
                                <div id="ajax-content" class="fl-wrap">
                                    <div class="ajax-inner fl-wrap" style="opacity: 1;">
                                        <div class="list-post-wrap">

                                            <div class="list-post fl-wrap" ng-repeat="post in posts" ng-cloak>
                                                <div class="list-post-media">
                                                    <a href="/<% post.slug %>">
                                                        <div class="bg-wrap">
                                                            <div class="bg" data-bg="<% post.image ? post.image.path : '' %>"
                                                                 ng-style="post.image && {'background-image': 'url(' + post.image.path + ')'}"
                                                            ></div>
                                                        </div>
                                                    </a>


                                                    <span class="post-media_title">© Image Copyrights Title</span>
                                                </div>

                                                <div class="list-post-content">
                                                    <a class="post-category-marker" href="#">
                                                        <% post.category ? post.category.name : '' %>
                                                    </a>

                                                    <h3 class="post-title">
                                                        <a href="/<% post.slug %>"><% post.name %></a>
                                                    </h3>

                                                    <span class="post-date">
                                                        <i class="far fa-clock"></i>
                                                        <% formartDate(post.created_at) | date:'dd/MM/yyyy' %>
                                                      </span>


                                                    <p class="post-intro clamp-3"><% post.intro %></p>
                                                </div>

                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="clearfix"></div>
                            <div class="section-title sect_dec">
                                <h2>Bài viết mới nhất</h2>
                            </div>

                            <div class="grid-post-wrap">
                                <div class="more-post-wrap  fl-wrap">
                                    <div class="list-post-wrap list-post-wrap_column fl-wrap">
                                        <div class="row">
                                            @foreach($postsRecent as $postRecent)
                                                <div class="col-md-6">
                                                    <!--list-post-->
                                                    <div class="list-post fl-wrap">

                                                        <div class="list-post-media">
                                                            <a href="{{ route('front.blogDetail', $postRecent->slug) }}">
                                                                <div class="bg-wrap">
                                                                    <div class="bg" data-bg="{{ $postRecent->image->path ?? '' }}"></div>
                                                                </div>
                                                            </a>

                                                            <span class="post-media_title">&copy; Image Copyrights Title</span>
                                                        </div>
                                                        <div class="list-post-content list-post-content-1">
                                                            <h3 ><a href="{{ route('front.blogDetail', $postRecent->slug) }}" title="{{ $postRecent->name }}">{{ $postRecent->name }}</a>
                                                            </h3>

                                                            <span class="post-date"><i class="far fa-clock"></i>
                                                                {{ \Illuminate\Support\Carbon::parse($postRecent->created_at)->format('d/m/Y') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!--list-post end-->
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>
                                </div>

                            </div>
                            <a href="{{ route('front.blogs') }}" class="dark-btn fl-wrap"> Xem tất cả </a>






                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- sidebar   -->
                        <div class="sidebar-content fl-wrap fix-bar">
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="box-widget-content">
                                    <!-- content-tabs-wrap -->
                                    <div class="content-tabs-wrap tabs-act tabs-widget fl-wrap">
                                        <div class="content-tabs fl-wrap">
                                            <ul class="tabs-menu fl-wrap no-list-style">
                                                <li class="current"><a href="#tab-popular"> {{ $popularCate->name }} </a></li>
                                            </ul>
                                        </div>
                                        <!--tabs -->
                                        <div class="tabs-container">
                                            <!--tab -->
                                            <div class="tab">
                                                <div id="tab-popular" class="tab-content first-tab">
                                                    <div class="post-widget-container fl-wrap">
                                                        <!-- post-widget-item -->
                                                        @foreach($popularPosts as $popularPost)
                                                            <div class="post-widget-item fl-wrap">
                                                                <div class="post-widget-item-media">
                                                                    <a href="{{ route('front.blogDetail', $popularPost->slug) }}"><img src="{{ $popularPost->image->path ?? '' }}"  alt=""></a>

                                                                </div>
                                                                <div class="post-widget-item-content">
                                                                    <h4><a href="{{ route('front.blogDetail', $popularPost->slug) }}">{{ $popularPost->name }}</a></h4>
                                                                    <ul class="pwic_opt">
                                                                        <li><span><i class="far fa-clock"></i>{{ \Illuminate\Support\Carbon::parse($popularPost->created_at)->format('d/m/Y') }}</span></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        @endforeach


                                                    </div>
                                                </div>
                                            </div>
                                            <!--tab  end-->
                                        </div>
                                        <!--tabs end-->
                                    </div>
                                    <!-- content-tabs-wrap end -->
                                </div>
                            </div>
                            <!-- box-widget  end -->

                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="widget-title">Theo dõi chúng tôi</div>
                                <div class="box-widget-content">
                                    <div class="social-widget">
                                        <a href="{{ $config->facebook }}" target="_blank" class="facebook-soc">
                                            <i class="fab fa-facebook-f"></i>
                                            <span class="soc-widget-title">Facebook</span>
                                        </a>
                                        <a href="{{ $config->twitter }}" target="_blank" class="twitter-soc">
                                            <i class="fab fa-twitter"></i>
                                            <span class="soc-widget-title">Twitter</span>
                                        </a>
                                        <a href="{{ $config->youtube }}" target="_blank" class="youtube-soc">
                                            <i class="fab fa-youtube"></i>
                                            <span class="soc-widget-title">Youtube</span>
                                        </a>
                                        <a href="{{ $config->instagram }}" target="_blank" class="instagram-soc">
                                            <i class="fab fa-instagram"></i>
                                            <span class="soc-widget-title">Instagram</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- box-widget  end -->
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="widget-title">Tags</div>
                                <div class="box-widget-content">
                                    <div class="tags-widget">
                                        @foreach($tags as $tag)
                                            <a href="{{ route('front.getPostByTag', $tag->slug) }}">{{ $tag->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- box-widget  end -->
                        </div>
                        <!-- sidebar  end -->
                    </div>

                </div>
                <div class="limit-box fl-wrap"></div>
            </div>
        </section>

        @foreach($categoriesSpecial as $categorySpecial)
            @php
                $postsSpecial = $categorySpecial->posts;
                $firstPost = $postsSpecial->first();
                $postsSpec = $postsSpecial->slice(1)->values();

            @endphp
            <section>
                <div class="container">
                    <div class="section-title sect_dec">
                        <h2>{{ $categorySpecial->name }}</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="list-post-wrap list-post-wrap_column list-post-wrap_column_fw">
                                <!--list-post-->
                                <div class="list-post fl-wrap">
                                    {{--                                    <a class="post-category-marker" href="{{ route('front.blogs', $firstPost->category->slug ?? '') }}">{{ $firstPost->category->name ?? '' }}</a>--}}
                                    <div class="list-post-media">
                                        <a href="{{ route('front.blogDetail', $firstPost->slug) }}">
                                            <div class="bg-wrap">
                                                <div class="bg" data-bg="{{ $firstPost->image->path ?? '' }}"></div>
                                            </div>
                                        </a>



                                        <span class="post-media_title">&copy; Image Copyrights Title</span>
                                    </div>
                                    <div class="list-post-content">
                                        <h3><a href="{{ route('front.blogDetail', $firstPost->slug) }}">{{ $firstPost->name }}  </a>
                                        </h3>
                                        <span class="post-date"><i class="far fa-clock"></i>{{ \Carbon\Carbon::parse($firstPost->created_at)->format('d/m/Y') }}</span>

                                        <p>
                                            {{ $firstPost->intro }}
                                        </p>

                                    </div>
                                </div>
                                <!--list-post end-->
                            </div>
                            <a href="{{ route('front.home-page') }}" class="dark-btn fl-wrap"> Xem tất cả </a>
                        </div>
                        <div class="col-md-7">
                            <div class="picker-wrap-container fl-wrap">
                                <div class="picker-wrap-controls">
                                    <ul class="fl-wrap">
                                        <li><span class="pwc_up"><i class="fas fa-caret-up"></i></span></li>
                                        <li><span class="pwc_pause"><i class="fas fa-pause"></i></span></li>
                                        <li><span class="pwc_down"><i class="fas fa-caret-down"></i></span></li>
                                    </ul>
                                </div>
                                <div class="picker-wrap fl-wrap">
                                    <div class="list-post-wrap  fl-wrap">
                                        <!--list-post-->
                                        @foreach($postsSpec as $postSpec)
                                            <div class="list-post fl-wrap">
                                                <div class="list-post-media">
                                                    <a href="{{ route('front.blogDetail', $postSpec->slug) }}">
                                                        <div class="bg-wrap">
                                                            <div class="bg" data-bg="{{ $postSpec->image->path ?? '' }}"></div>
                                                        </div>
                                                    </a>

                                                    {{-- BADGE overlay --}}
                                                    <span class="post-media_title">&copy; Image Copyrights Title</span>
                                                </div>
                                                <div class="list-post-content">
                                                    <a class="post-category-marker" href="{{ route('front.blogs', $postSpec->category->slug ?? '') }}">{{ $postSpec->category->name ?? '' }}</a>
                                                    <h3><a href="{{ route('front.blogDetail', $postSpec->slug) }}">{{ $postSpec->name }}</a>
                                                    </h3>
                                                    <span class="post-date"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($postSpec->created_at)->format('d/m/Y') }}</span>
                                                    <p class="post-intro clamp-3">{{ $postSpec->intro }}</p>
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                                <div class="controls-limit fl-wrap"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="limit-box"></div>
            </section>




        @endforeach
    </div>
@endsection

@push('scripts')



    <script>
        app.controller('homePage', function ($rootScope, $scope, $interval) {

            $scope.getPostByCate = function (cateId) {
                var $loader = $('.ajax-loader');
                $scope.currentCateId = cateId;
                $.ajax({
                    type: 'GET',
                    url: "/get-post-by-cate?cate_id="+cateId,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    beforeSend: function () {
                        $loader.stop(true, true).fadeIn(100);
                    },
                    success: function (response) {
                        if (response.success) {
                            $scope.posts = response.data;
                            console.log($scope.posts)
                            $scope.$applyAsync();
                        } else {
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $loader.stop(true, true).fadeOut(100);
                    }
                });
            }

            $scope.postCategories = @json($postCategories ?? []);

            $scope.getCategoryCurrent = function (currentCateId) {
                var items = Array.isArray($scope.postCategories) ? $scope.postCategories : [];
                if (currentCateId == null || items.length === 0) return null;

                var target = String(currentCateId);
                for (var i = 0; i < items.length; i++) {
                    var it = items[i];
                    if (it && String(it.id) === target) return it;
                }
                return null;
            };

            @php
                $cats = array_values($postCategories->toArray() ?? []);
                $pick = $cats[0] ?? ($cats[1] ?? null);
                $id = data_get($pick, 'id');
            @endphp
            @if($id)
            $scope.getPostByCate({{ (int)$id }});
            @endif

                $scope.formartDate = function (date) {
                return new Date(date.replace(' ', 'T'));
            }

        })

    </script>

@endpush
