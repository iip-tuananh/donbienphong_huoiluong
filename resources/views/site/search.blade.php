@extends('site.layouts.master')
@section('title')Tìm kiếm - {{ $config->web_title }}@endsection
@section('description'){{ strip_tags(html_entity_decode($config->introduction)) }}@endsection
@section('image'){{@$config->image->path ?? ''}}@endsection

@section('css')

@endsection


@section('content')
    <style>
        :root{
            --brand-orange: #EE4110;   /* chỉnh theo màu nút của bạn */
            --brand-orange-strong: #C7360E;
            --brand-orange-weak: #FFE3D7;
            --ink-dark: #2c2c2c;
            --white: #fff;
        }


        /* Overlay badge trên ảnh */
        .post-badge{
            position:absolute; top:10px; left:10px;
            display:inline-flex; align-items:center; gap:8px;
            padding:6px 10px; border-radius:999px;
            font-size:14px; font-weight:700; line-height:1;
            box-shadow: 0 6px 14px rgba(0,0,0,.12);
            backdrop-filter: blur(2px);
        }
        .post-badge i{ font-size:12px; }

        /* FREE: nền sáng, viền cam nhạt – hợp tin tức */
        .post-badge.is-free{
            background: #0da306;
            color: #fff;
            border: 1px dashed rgba(238, 65, 16, .45);
        }

        /* PAID: nền cam đậm + chữ trắng */
        .post-badge.is-paid{
            background: linear-gradient(180deg, var(--brand-orange), #ff6a2f);
            color: var(--white);
            border: none;
            text-shadow: 0 1px 0 rgba(0,0,0,.15);
        }


    </style>

    <style>
        .post-widget-item .post-widget-item-media{ position:relative; }
        .post-widget-item .pw-badge{
            position:absolute; left:6px; bottom:6px;
            display:inline-flex; align-items:center; gap:6px;
            padding:3px 6px; border-radius:999px;
            font-size:10px; font-weight:700; line-height:1;
            white-space:nowrap; box-shadow:0 4px 10px rgba(0,0,0,.12);
            backdrop-filter: blur(2px);
        }
        .post-widget-item .pw-badge i{ font-size:10px; }

        /* Free */
        .post-widget-item .pw-badge.is-free{
            background: #0da306;
            color: #fff;
            border: 1px dashed rgba(238,65,16,.45);
        }

        /* Paid */
        .post-widget-item .pw-badge.is-paid{
            background: linear-gradient(180deg, var(--brand-orange), #ff6a2f);
            color: var(--white);
            border:none; text-shadow:0 1px 0 rgba(0,0,0,.15);
        }

        /* Nếu quá chật ở màn nhỏ: chỉ hiện icon, ẩn text */
        @media (max-width: 420px){
            .post-widget-item .pw-badge{ padding:3px 4px; }
            .post-widget-item .pw-badge span{ display:none; } /* nếu bạn để text trong <span> */
        }
    </style>

    <div class="content">
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a>
                    <span>Tìm kiếm</span>
                </div>
                <div class="scroll-down-wrap">
                    <div class="mousey">
                        <div class="scroller"></div>
                    </div>
                    <span>Scroll Down To Discover</span>
                </div>
            </div>
            <div class="pwh_bg"></div>
        </div>
        <!--section   -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="main-container fl-wrap fix-container-init">
                            <div class="section-title">
                                <h2>Có {{ $posts->count() }} kết quả phù hợp với từ khóa "{{ $keyword }}"</h2>
                                <h4></h4>
                            </div>
                            <div class="list-post-wrap">
                                @foreach($posts as $post)
                                    <div class="list-post fl-wrap">
                                        <div class="list-post-media">
                                            <a href="{{ route('front.blogDetail', $post->slug) }}">
                                                <div class="bg-wrap">
                                                    <div class="bg" data-bg="{{ $post->image->path ?? '' }}"></div>
                                                </div>
                                            </a>



                                            <span class="post-media_title">&copy; Image Copyrights Title</span>
                                        </div>
                                        <div class="list-post-content">
                                            <a class="post-category-marker" href="{{ route('front.blogs', $post->category->slug ?? '') }}">{{ $post->category->name ?? '' }}</a>
                                            <h3><a href="{{ route('front.blogDetail', $post->slug) }}">{{ $post->name }}</a></h3>
                                            <span class="post-date"><i class="far fa-clock"></i> {{ \Illuminate\Support\Carbon::parse($post->created_at)->format('d/m/Y') }}</span>
                                            <p>
                                                {{ $post->intro }}
                                            </p>
                                        </div>
                                    </div>

                                @endforeach

                            </div>
                            <div class="clearfix"></div>

                            <!--pagination-->
{{--                            {{ $posts->links('site.pagination.paginate2') }}--}}
                            <!--pagination end-->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- sidebar   -->
                        <div class="sidebar-content fl-wrap fixed-bar">
                            <!-- box-widget  end -->
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="widget-title">Danh mục</div>
                                <div class="box-widget-content">
                                    <ul class="cat-wid-list">
                                        @foreach($categories as $cate)
                                            <li><a href="{{ route('front.blogs', $cate->slug) }}">{{ $cate->name }}</a><span>{{ $cate->total_posts }}</span></li>
                                        @endforeach

                                    </ul>
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
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="widget-title">Follow Us</div>
                                <div class="box-widget-content">
                                    <div class="social-widget">
                                        <a href="{{ $config->facebook }}" target="_blank" class="facebook-soc">
                                            <i class="fab fa-facebook-f"></i>
                                            <span class="soc-widget-title">Likes</span>
                                            <span class="soc-widget_counter"></span>
                                        </a>
                                        <a href="{{ $config->twitter }}" target="_blank" class="twitter-soc">
                                            <i class="fab fa-twitter"></i>
                                            <span class="soc-widget-title">Followers</span>
                                            <span class="soc-widget_counter"></span>
                                        </a>
                                        <a href="{{ $config->youtube }}" target="_blank" class="youtube-soc">
                                            <i class="fab fa-youtube"></i>
                                            <span class="soc-widget-title">Followers</span>
                                            <span class="soc-widget_counter"></span>
                                        </a>
                                        <a href="{{ $config->instagram }}" target="_blank" class="instagram-soc">
                                            <i class="fab fa-instagram"></i>
                                            <span class="soc-widget-title">Followers</span>
                                            <span class="soc-widget_counter"></span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <!-- box-widget  end -->
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="box-widget-content">
                                    <!-- content-tabs-wrap -->
                                    <div class="content-tabs-wrap tabs-act tabs-widget fl-wrap">
                                        <div class="content-tabs fl-wrap">
                                            <ul class="tabs-menu fl-wrap no-list-style">
                                                <li class="current"><a href="#tab-popular"> Bài viết phổ biến </a></li>
                                                <li><a href="#tab-resent">Bài viết mới nhất</a></li>
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
                                            <!--tab -->
                                            <div class="tab">
                                                <div id="tab-resent" class="tab-content">
                                                    <div class="post-widget-container fl-wrap">
                                                        <!-- post-widget-item -->
                                                        @foreach($postsRecent as $postRecent)
                                                            <div class="post-widget-item fl-wrap">
                                                                <div class="post-widget-item-media">
                                                                    <a href="{{ route('front.blogDetail', $postRecent->slug) }}"><img src="{{ $postRecent->image->path ?? '' }}"  alt=""></a>


                                                                </div>
                                                                <div class="post-widget-item-content">
                                                                    <h4><a href="{{ route('front.blogDetail', $postRecent->slug) }}">{{ $postRecent->name }}</a></h4>
                                                                    <ul class="pwic_opt">
                                                                        <li><span><i class="far fa-clock"></i>{{ \Illuminate\Support\Carbon::parse($postRecent->created_at)->format('d/m/Y') }}</span></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>
                                            <!--tab end-->
                                        </div>
                                        <!--tabs end-->
                                    </div>
                                    <!-- content-tabs-wrap end -->
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
        <!-- section end -->
        <!-- section  -->

        <!-- section end -->
    </div>


@endsection

@push('scripts')
    <script>
        $(document).on('change', '#list', function () {
            var url = $(this).find('option:selected').data('url') || '';
            if (url) window.location.href = url;
        });

    </script>
@endpush
