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
@endsection
@section('js')
@endsection
@section('content')
    <div class="content">
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
                                <a class="post-category-marker"
                                   href="">{{ $blog->category->name ?? '' }}</a>
                                <div class="clearfix"></div>
                                <h1>{{ $blog->name }}</h1>
                                <div class="clearfix"></div>
                                <div class="author-link"><a href="#"><img src="/site/images/avatar/2.jpg" alt="">
                                        <span>By Admin</span></a></div>
                                <span class="post-date"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($blog->created)->format('d/m/Y') }}</span>

                            </div>
                            <!-- single-post-header end   -->
                            <!-- single-post-media   -->
                            {{-- <div class="single-post-media fl-wrap">
                    <img style="width:100%;" src="{{$blog_detail->image}}" alt="{{$blog_detail->image}}">
                   </div> --}}
                            <!-- single-post-media end   -->
                            <!-- single-post-content   -->
                            <div class="single-post-content spc_column fl-wrap">
                                <div class="single-post-content_column">
                                    <div class="share-holder ver-share fl-wrap">
                                        <div class="share-title">Chia Sẻ <br> Bài Viết:</div>
                                        <div class="share-container  isShare"></div>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
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

                                <div class="single-post-content_text" id="font_chage">
                                    {!! $blog->body !!}
                                </div>

                            </div>
                            <!-- single-post-content  end   -->
                            <div class="limit-box2 fl-wrap"></div>

                            <div class="more-post-wrap  fl-wrap" style="margin-top: 30px;">
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
                        <!-- sidebar   -->
                        <div class="sidebar-content fl-wrap fixed-bar fixbar-action">
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
{{--                                                <li class="current"><a href="#tab-popular"> Bài viết mới </a></li>--}}
{{--                                            </ul>--}}
{{--                                        </div>--}}
{{--                                        <!--tabs -->--}}
{{--                                        <div class="tabs-container">--}}
{{--                                            <!--tab -->--}}
{{--                                            <div class="tab">--}}
{{--                                                <div id="tab-popular" class="tab-content first-tab">--}}
{{--                                                    <div class="post-widget-container fl-wrap">--}}
{{--                                                        @foreach ($blognew as $item)--}}
{{--                                                            <!-- post-widget-item -->--}}
{{--                                                            <div class="post-widget-item fl-wrap">--}}
{{--                                                                <div class="post-widget-item-media">--}}
{{--                                                                    <a--}}
{{--                                                                        href="{{ route('detailBlog', ['slug' => $item->slug]) }}"><img--}}
{{--                                                                            src="{{ $item->image }}" alt=""></a>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="post-widget-item-content">--}}
{{--                                                                    <h4><a--}}
{{--                                                                            href="{{ route('detailBlog', ['slug' => $item->slug]) }}">{{ languageName($item->title) }}</a>--}}
{{--                                                                    </h4>--}}
{{--                                                                    <ul class="pwic_opt">--}}
{{--                                                                        <li><span><i class="far fa-clock"></i>--}}
{{--                                                                                {{ date_format($item->created_at, 'd/m/Y') }}</span>--}}
{{--                                                                        </li>--}}
{{--                                                                        <li><span><i--}}
{{--                                                                                    class="fal fa-eye"></i> Admin</span>--}}
{{--                                                                        </li>--}}
{{--                                                                    </ul>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                            <!-- post-widget-item end -->--}}
{{--                                                        @endforeach--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <!--tab  end-->--}}

{{--                                        </div>--}}
{{--                                        <!--tabs end-->--}}
{{--                                    </div>--}}
{{--                                    <!-- content-tabs-wrap end -->--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <!-- box-widget  end -->
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
@endpush
