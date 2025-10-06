@extends('site.layouts.master')
@section('title')
    {{ $blog->name }} - {{ $config->web_title }}
@endsection
@section('description')
    {{ strip_tags(html_entity_decode($config->introduction)) }}
@endsection
@section('image')
    {{@$config->image->path ?? ''}}
@endsection

@section('css')
    <link type="text/css" rel="stylesheet" href="/site/css/editor-content.css">

@endsection

@section('content')
    <style>
        /* Style gọn, khớp với box-widget có sẵn */
        .post-paycard {
            border: 1px solid #e8edf2;
            border-radius: 12px;
            padding: 14px;
            background: #fff
        }

        .post-paycard-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px
        }

        .ppc-badge {
            display: inline-block;
            font-size: .85rem;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #e8edf2
        }

        .ppc-badge.is-free {
            background: #f1f8f5;
            color: #0b7a3b;
            border-color: #d5efe3
        }

        .ppc-badge.is-paid {
            background: #fff5f0;
            color: #b23c17;
            border-color: #ffd9c9
        }

        .ppc-title {
            margin: 0;
            font-size: 1.05rem;
            text-align: left
        }

        .post-paycard-price {
            margin: 8px 0 12px
        }

        .ppc-price-row {
            align-items: flex-end;
            gap: 10px
        }

        .ppc-price-current {
            font-weight: 700;
            font-size: 1.05rem
        }

        .ppc-price-old {
            text-decoration: line-through;
            color: #9aa3ae
        }

        .ppc-price-free {
            font-weight: 700;
            color: #0b7a3b
        }

        .post-paycard-actions {
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px
        }

        .ppc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none
        }

        .ppc-btn-primary {
            background: #ff6a00;
            color: #fff
        }

        .ppc-btn-primary:hover {
            filter: brightness(.96)
        }

        .ppc-btn-ghost {
            background: #fff;
            border: 1px solid #e8edf2;
            color: #111
        }

        .ppc-inline-form {
            display: inline
        }

        .post-paycard-meta {
            margin: 8px 0 0;
            padding: 10px 2px;
            border: 1px dashed #e8edf2;
            border-radius: 10px;
            list-style: none
        }

        .post-paycard-meta li {
            display: flex;
            margin: 4px 0
        }

        .post-paycard-meta span {
            color: #6b7280;
            min-width: 110px
        }

        @media (max-width: 600px) {
            .ppc-title {
                font-size: 1rem
            }

            .ppc-price-current {
                font-size: 1.1rem
            }
        }

    </style>
    <style>
        /* Paywall styles */
        .single-post-content_text.is-locked {
            position: relative
        }

        .paywall-excerpt {
            max-height: clamp(220px, 40vh, 420px);
            overflow: hidden;
            /* tạo fade cuối đoạn preview */
            -webkit-mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 1) 70%, rgba(0, 0, 0, 0));
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 1) 70%, rgba(0, 0, 0, 0));
        }

        .paywall-overlay {
            position: absolute;
            inset: auto 0 0 0; /* nằm dưới cùng */
            display: flex;
            justify-content: center;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, .96) 30%, rgba(255, 255, 255, 1));
            padding: 26px 12px 14px;
        }

        .paywall-card {
            width: min(380px, 100%);
            border: 1px solid #e8edf2;
            border-radius: 12px;
            background: #fff;
            padding: 14px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
        }

        .paywall-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .85rem;
            border: 1px solid #e8edf2;
            margin-bottom: 6px
        }

        .paywall-badge.is-paid {
            background: #fff5f0;
            color: #b23c17;
            border-color: #ffd9c9
        }

        .paywall-badge.is-free {
            background: #f1f8f5;
            color: #0b7a3b;
            border-color: #d5efe3
        }

        .paywall-price {
            margin: 4px 0 8px;
            font-size: 1.15rem
        }

        .paywall-price .old {
            margin-left: 8px;
            color: #9aa3ae;
            text-decoration: line-through
        }

        .paywall-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 8px
        }

        .pw-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer
        }

        .pw-btn-primary {
            background: #ff6a00;
            color: #fff !important;
        }

        .pw-btn-ghost {
            background: #fff;
            color: #111;
            border-color: #e8edf2
        }

        @media (max-width: 600px) {
            .paywall-card {
                padding: 12px
            }
        }


        /* đặt chiều cao vùng overlay (có thể chỉnh) */
        .single-post-content_text.is-locked {
            position: relative;
            --pw-h: 120px;
        }

        /* phần preview: chừa chỗ bằng padding-bottom = chiều cao overlay */
        .paywall-excerpt {
            max-height: clamp(220px, 40vh, 420px);
            overflow: hidden;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 1) 70%, rgba(0, 0, 0, 0));
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 1) 70%, rgba(0, 0, 0, 0));
            padding-bottom: var(--pw-h); /* <<< quan trọng */
        }

        /* overlay chỉ chiếm đúng phần đã chừa ở đáy */
        .paywall-overlay {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: var(--pw-h); /* <<< khớp với padding-bottom */
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom,
            rgba(255, 255, 255, 0),
            rgba(255, 255, 255, .96) 45%, #fff 90%);
            padding: 10px 12px;
        }

        /* để click được nút bên trong nhưng không chặn phần preview phía trên */
        .paywall-overlay {
            pointer-events: none;
        }

        .paywall-card {
            pointer-events: auto;
        }

        @media (max-width: 600px) {
            .single-post-content_text.is-locked {
                --pw-h: 140px;
            }

            /* nếu mobile cần cao hơn */
        }


    </style>

    <style>
        /* ===== Single post header (scoped) ===== */
        .single-post-header h1 {
            display: flex; /* đặt title và badge cùng hàng */
            align-items: center;
            gap: 10px;
            flex-wrap: wrap; /* nếu tiêu đề dài sẽ tự xuống dòng đẹp */
            margin: 10px 0 12px;
            line-height: 1.25;
        }

        /* Pill Tin hot */
        .single-post-header .hot-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff7a59 0%, #ff3d3d 100%);
            color: #fff;
            font-weight: 700;
            font-size: 14px; /* lớn hơn list để cân xứng H1 */
            line-height: 1;
            box-shadow: 0 6px 18px rgba(255, 61, 61, .28),
            inset 0 0 0 1px rgba(255, 255, 255, .26);
            white-space: nowrap; /* luôn gọn như một “pill” */
            transform: translateY(0);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .single-post-header .hot-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(255, 61, 61, .34),
            inset 0 0 0 1px rgba(255, 255, 255, .3);
            filter: saturate(1.02);
        }

        .single-post-header .hot-badge__icon {
            font-size: 16px;
            line-height: 1;
        }

        /* Nhảy lửa rất nhẹ; tôn trọng reduced motion */
        @keyframes flame-pop {
            0%, 100% {
                transform: translateY(0)
            }
            50% {
                transform: translateY(-1px)
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            .single-post-header .hot-badge__icon {
                animation: flame-pop 1.6s ease-in-out infinite;
            }
        }

        /* Giữ category marker gọn gàng phía trên */
        .single-post-header .post-category-marker {
            display: inline-block;
            margin-bottom: 6px;
        }

        /* Mobile tinh chỉnh khoảng cách */
        @media (max-width: 576px) {
            .single-post-header h1 {
                gap: 8px;
            }

            .single-post-header .hot-badge {
                font-size: 13px;
                padding: 5px 10px;
            }
        }

    </style>
    <style>
        /* --- Post Videos --- */
        .post-videos { margin-top: 28px; }
        .pv-head { display:flex; align-items:end; gap:12px; margin-bottom:12px; }
        .pv-title { margin:0; font-size:20px; font-weight:700; color:#0f172a; }
        .pv-sub { color:#64748b; font-size:13px; }

        /* Grid responsive */
        .pv-grid {
            display:grid; gap:16px;
            grid-template-columns: repeat(12, 1fr);
        }
        @media (max-width: 575.98px) { .pv-grid { grid-template-columns: repeat(4, 1fr); } }
        @media (min-width: 576px) and (max-width: 991.98px) { .pv-grid { grid-template-columns: repeat(8, 1fr); } }

        .pv-card {
            grid-column: span 4; /* mobile: 1–2 card/row tùy màn hình */
            background:#fff; border:1px solid #eef0f4; border-radius:14px; overflow:hidden;
            box-shadow:0 4px 14px rgba(3,64,153,.06);
            transition:transform .2s ease, box-shadow .2s ease;
        }
        @media (min-width: 576px) { .pv-card { grid-column: span 4; } } /* 2 cards/row */
        @media (min-width: 992px) { .pv-card { grid-column: span 4; } } /* 4 cards/row */
        .pv-card:hover { transform: translateY(-2px); box-shadow:0 10px 24px rgba(3,64,153,.12); }

        .pv-thumb { position:relative; aspect-ratio:16/9; background:#000; }
        .pv-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
        .pv-play {
            position:absolute; right:12px; bottom:12px; width:48px; height:48px;
            border:0; border-radius:50%; display:grid; place-items:center; cursor:pointer;
            background:#034099; color:#fff; box-shadow:0 6px 16px rgba(3,64,153,.4);
            transition: transform .15s ease, background .15s ease;
        }
        .pv-play:hover { transform: scale(1.06); background:#003a86; }

        .pv-card-title {
            font-size:15px; line-height:1.45; font-weight:600; color:#1e293b;
            padding:12px 14px 14px; margin:0; display:-webkit-box; -webkit-line-clamp:2;
            -webkit-box-orient:vertical; overflow:hidden;
        }

        /* Modal player */
        .pv-modal { position:fixed; inset:0; display:none; z-index:9000; }
        .pv-modal.is-open { display:block; }
        .pv-modal__backdrop { position:absolute; inset:0; background:rgba(17,24,39,.6); }
        .pv-modal__dialog {
            position:relative; max-width:min(1000px, 92vw); margin:20vh auto; background:#000;
            border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.45);
        }
        .pv-modal__body { position:relative; aspect-ratio:16/9; }
        .pv-modal__body iframe { position:absolute; inset:0; width:100%; height:100%; }
        .pv-modal__close {
            position:absolute; top:8px; right:10px; width:40px; height:40px; border:0; border-radius:10px;
            background:rgba(255,255,255,.15); color:#fff; font-size:28px; line-height:1; cursor:pointer;
        }
        .pv-modal__close:hover { background:rgba(255,255,255,.25); }

    </style>

    <div class="content" ng-controller="blogPage">
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a>
                    <a href="{{ route('front.blogs', $blog->category->slug ?? '') }}">{{ $blog->category->name ?? '' }}</a>
                    <span>{{ $blog->name }}</span>
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
                            <!-- single-post-header  -->
                            <div class="single-post-header fl-wrap">
                                <a class="post-category-marker" href="#">{{ $blog->category->name ?? '' }}</a>
                                <div class="clearfix"></div>
                                <h1>{{ $blog->name }}

                                </h1>
                                <div class="clearfix"></div>
                                <div class="author-link"><a href="#"><img src="/site/images/avatar/2.jpg" alt="">
                                        <span>By Admin</span></a></div>
                                <span class="post-date"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($blog->created)->format('d/m/Y') }}</span>

                            </div>
                            <!-- single-post-header end   -->
                            <!-- single-post-media   -->
                            <div class="single-post-media fl-wrap" style="margin-top: 20px">
                                @if ($blog->pdfs->count())
                                    @foreach($blog->pdfs as $filePdf)
                                        <div class="pdf-container fs-wrap">
                                            <a target="_blank" class="link" title="Tải file"
                                               style="color: #034099; font-weight: 600; margin-bottom: 10px; display: block; font-size: 15px; text-decoration: none; text-align: left;"
                                               href="/uploads/files/{{ $filePdf->file_name }}"><img
                                                    src="/site/images/icondownload.png" style="width: 15px !important;"
                                                    class="mr-2"> Tải file : {{ $filePdf->original_name }}</a><br>
                                            <iframe style="border: medium none;" height="660px" width="100%"
                                                    src="/uploads/files/{{ $filePdf->file_name }}" title=""
                                                    allowfullscreen=""></iframe>
                                        </div>
                                        <div class="clearfix"></div>
                                    @endforeach
                                @endif

                                @if ($blog->docs->count())
                                    @foreach($blog->docs as $fileDoc)
                                        @php
                                            $publicUrl = url('uploads/files/'.$fileDoc->file_name);
                                        @endphp

                                        <div class="docx-container fs-wrap">
                                            <a target="_blank" class="link"
                                               href="{{ $publicUrl }}"
                                               style="color:#034099;font-weight:600;margin-bottom:10px;display:block;font-size:15px;text-decoration:none;text-align:left;">
                                                <img src="/site/images/icondownload.png" style="width:15px !important;"
                                                     class="mr-2">
                                                Tải file : {{ $fileDoc->original_name }}
                                            </a>

                                            <iframe width="100%" height="660" style="border:0;"
                                                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($publicUrl) }}"
                                                    allowfullscreen></iframe>
                                        </div>
                                        <div class="clearfix"></div>
                                    @endforeach

                                @endif

                            </div>



                            <div class="single-post-content  fl-wrap">
                                <div class="clearfix"></div>

                                <div class="single-post-content_text editor-content" id="font_chage">
                                    {!! $blog->body !!}
                                </div>

                                @php
                                    // Helper: lấy YouTube ID từ nhiều dạng URL (PHP 7.2 compatible)
                                    $ytId = function ($url) {
                                        $url = (string)$url;
                                        $patterns = [
                                            '/youtu\.be\/([A-Za-z0-9_\-]{11})/i',
                                            '/v=([A-Za-z0-9_\-]{11})/i',
                                            '/embed\/([A-Za-z0-9_\-]{11})/i',
                                            '/shorts\/([A-Za-z0-9_\-]{11})/i',
                                        ];
                                        foreach ($patterns as $p) {
                                            if (preg_match($p, $url, $m)) return $m[1];
                                        }
                                        return null;
                                    };

                                    // Chuẩn hoá dữ liệu videos
                                    $videos = collect($blog->videos ?? [])->filter(function($v){
                                        return !empty($v['link'] ?? null);
                                    });
                                @endphp

                                @if($videos->count())
                                    <section class="post-videos">
                                        <div class="pv-head">
                                            <h3 class="pv-title">Videos</h3>
                                        </div>

                                        <div class="pv-grid">
                                            @foreach($videos as $v)
                                                @php
                                                    $id = $ytId($v['link']);
                                                    if (!$id) continue;
                                                    $title = !empty($v['title']) ? $v['title'] : 'Video';
                                                @endphp
                                                <article class="pv-card" data-yt="{{ $id }}" data-title="{{ e($title) }}">
                                                    <div class="pv-thumb">
                                                        <img src="https://img.youtube.com/vi/{{ $id }}/hqdefault.jpg"
                                                             alt="{{ e($title) }}" loading="lazy">
                                                        <button class="pv-play" aria-label="Phát video">
                                                            <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
                                                                <path d="M8 5v14l11-7z" fill="currentColor"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <h4 class="pv-card-title">{{ $title }}</h4>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @endif

                                <div class="pv-modal" id="pvModal" aria-hidden="true" role="dialog" aria-label="YouTube player">
                                    <div class="pv-modal__backdrop" data-close-modal></div>
                                    <div class="pv-modal__dialog" role="document">
                                        <button class="pv-modal__close" aria-label="Đóng" data-close-modal>&times;</button>
                                        <div class="pv-modal__body">
                                            <iframe id="pvPlayer" src="" title="YouTube video player" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    allowfullscreen loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>


                                <div class="single-post-footer fl-wrap">
                                    <div class="post-single-tags">
                                        <span class="tags-title"><i class="fas fa-tag"></i> Tags : </span>
                                        <div class="tags-widget">
                                            @foreach($blog->tags as $tag)
                                                <a href="{{ route('front.getPostByTag', $tag->slug) }}">{{ $tag->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="limit-box2 fl-wrap"></div>


                            <div class="more-post-wrap  fl-wrap" style="margin-top: 60px">
                                <div class="pr-subtitle prs_big">Bài viết liên quan</div>
                                <div class="list-post-wrap list-post-wrap_column fl-wrap">
                                    <div class="row">
                                        @foreach($othersBlog as $otherBlog)
                                            <div class="col-md-6">
                                                <!--list-post-->
                                                <div class="list-post fl-wrap">
                                                    <a class="post-category-marker"
                                                       href="{{ route('front.blogs', $otherBlog->category->slug ?? '') }}">{{ $otherBlog->category->name ?? '' }}</a>
                                                    <div class="list-post-media">
                                                        <a href="{{ route('front.blogDetail', $otherBlog->slug) }}">
                                                            <div class="bg-wrap">
                                                                <div class="bg"
                                                                     data-bg="{{ $otherBlog->image->path ?? '' }}"></div>
                                                            </div>
                                                        </a>
                                                        <span
                                                            class="post-media_title">&copy; Image Copyrights Title</span>
                                                    </div>
                                                    <div class="list-post-content">
                                                        <h3>
                                                            <a href="{{ route('front.blogDetail', $otherBlog->slug) }}">{{ $otherBlog->name }}</a>
                                                        </h3>
                                                        <span class="post-date"><i class="far fa-clock"></i>{{ \Illuminate\Support\Carbon::parse($otherBlog->created_at)->format('d/m/Y') }}</span>
                                                    </div>
                                                </div>
                                                <!--list-post end-->
                                            </div>

                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <div class="sidebar-content fl-wrap fixed-bar">
                            <!-- box-widget  end -->
                            <!-- box-widget -->
                            <div class="box-widget fl-wrap">
                                <div class="widget-title">Danh mục</div>
                                <div class="box-widget-content">
                                    <ul class="cat-wid-list">
                                        @foreach($categories as $cate)
                                            <li>
                                                <a href="{{ route('front.blogs', $cate->slug) }}">{{ $cate->name }}</a><span>{{ $cate->total_posts }}</span>
                                            </li>
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
{{--                            <div class="box-widget fl-wrap">--}}
{{--                                <div class="box-widget-content">--}}
{{--                                    <!-- content-tabs-wrap -->--}}
{{--                                    <div class="content-tabs-wrap tabs-act tabs-widget fl-wrap">--}}
{{--                                        <div class="content-tabs fl-wrap">--}}
{{--                                            <ul class="tabs-menu fl-wrap no-list-style">--}}
{{--                                                <li class="current"><a href="#tab-popular"> Bài viết phổ biến </a></li>--}}
{{--                                                <li><a href="#tab-resent">Bài viết mới nhất</a></li>--}}
{{--                                            </ul>--}}
{{--                                        </div>--}}

{{--                                        <div class="tabs-container">--}}
{{--                                            <!--tab -->--}}
{{--                                            <div class="tab">--}}
{{--                                                <div id="tab-popular" class="tab-content first-tab">--}}
{{--                                                    <div class="post-widget-container fl-wrap">--}}
{{--                                                        <!-- post-widget-item -->--}}
{{--                                                        @foreach($popularPosts as $popularPost)--}}
{{--                                                            <div class="post-widget-item fl-wrap">--}}
{{--                                                                <div class="post-widget-item-media">--}}
{{--                                                                    <a href="{{ route('front.blogDetail', $popularPost->slug) }}"><img--}}
{{--                                                                            src="{{ $popularPost->image->path ?? '' }}"--}}
{{--                                                                            alt=""></a>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="post-widget-item-content">--}}
{{--                                                                    <h4>--}}
{{--                                                                        <a href="{{ route('front.blogDetail', $popularPost->slug) }}">{{ $popularPost->name }}</a>--}}
{{--                                                                    </h4>--}}
{{--                                                                    <ul class="pwic_opt">--}}
{{--                                                                        <li><span><i class="far fa-clock"></i>{{ \Illuminate\Support\Carbon::parse($popularPost->created_at)->format('d/m/Y') }}</span>--}}
{{--                                                                        </li>--}}
{{--                                                                    </ul>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        @endforeach--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <!--tab  end-->--}}
{{--                                            <!--tab -->--}}
{{--                                            <div class="tab">--}}
{{--                                                <div id="tab-resent" class="tab-content">--}}
{{--                                                    <div class="post-widget-container fl-wrap">--}}
{{--                                                        <!-- post-widget-item -->--}}
{{--                                                        @foreach($postsRecent as $postRecent)--}}
{{--                                                            <div class="post-widget-item fl-wrap">--}}
{{--                                                                <div class="post-widget-item-media">--}}
{{--                                                                    <a href="{{ route('front.blogDetail', $postRecent->slug) }}"><img--}}
{{--                                                                            src="{{ $postRecent->image->path ?? '' }}"--}}
{{--                                                                            alt=""></a>--}}


{{--                                                                </div>--}}
{{--                                                                <div class="post-widget-item-content">--}}
{{--                                                                    <h4>--}}
{{--                                                                        <a href="{{ route('front.blogDetail', $postRecent->slug) }}">{{ $postRecent->name }}</a>--}}
{{--                                                                    </h4>--}}
{{--                                                                    <ul class="pwic_opt">--}}
{{--                                                                        <li><span><i class="far fa-clock"></i>{{ \Illuminate\Support\Carbon::parse($postRecent->created_at)->format('d/m/Y') }}</span>--}}
{{--                                                                        </li>--}}
{{--                                                                    </ul>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        @endforeach--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <!--tab end-->--}}
{{--                                        </div>--}}
{{--                                        <!--tabs end-->--}}
{{--                                    </div>--}}
{{--                                    <!-- content-tabs-wrap end -->--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>
                        <!-- sidebar  end -->
                    </div>


                </div>
                <div class="limit-box fl-wrap"></div>
            </div>
        </section>
        <!-- section end -->

    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            // Mở/đóng modal
            var modal  = document.getElementById('pvModal');
            var player = document.getElementById('pvPlayer');

            function openModal(ytId) {
                // privacy-enhanced mode + autoplay
                player.src = 'https://www.youtube-nocookie.com/embed/' + ytId + '?autoplay=1&rel=0';
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
            function closeModal() {
                player.src = ''; // stop
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            modal.addEventListener('click', function(e){
                if (e.target.hasAttribute('data-close-modal')) closeModal();
            });
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
            });

            // Gắn sự kiện vào thẻ video
            var triggers = document.querySelectorAll('.pv-card .pv-play, .pv-card .pv-thumb');
            Array.prototype.forEach.call(triggers, function(el){
                el.addEventListener('click', function(){
                    var card = el.closest('.pv-card');
                    var id = card && card.getAttribute('data-yt');
                    if (id) openModal(id);
                });
            });
        })();
    </script>


    <script>
        app.controller('blogPage', function ($rootScope, $scope, $interval) {


        })
    </script>
@endpush
