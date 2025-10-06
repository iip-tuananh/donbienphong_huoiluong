@extends('site.layouts.master')
@section('title'){{ $title }} - {{ $config->web_title }}@endsection
@section('description'){{ strip_tags(html_entity_decode($config->introduction)) }}@endsection
@section('image'){{@$config->image->path ?? ''}}@endsection

@section('css')
    <link type="text/css" rel="stylesheet" href="/site/css/editor-content.css">

@endsection

@section('content')

    <div class="content">
        <!--section   -->
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a><span>Về chúng tôi</span>
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
        <!-- section end  -->
        <!--section   -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-title sect_dec">
                            <h2 style="font-size: 22px">{{ $title }}</h2>
                        </div>
                        <div class="about-wrap editor-content ">
                          {!! $content !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="sec-dec"></div>
        </section>
        <!--about end   -->
    </div>

@endsection

@push('scripts')



@endpush
