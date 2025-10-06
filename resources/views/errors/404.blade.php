@extends('site.layouts.master')
@section('title')
    {{ $config->web_title }}
@endsection
@section('description')
    {{ $config->web_des }}
@endsection
@section('image')
@endsection

@section('css')

@endsection

@section('content')
    <section class="page-header">
        <div class="page-header__bg" style="background-image: url(/site/images/backgrounds/page-header-bg-1-1.jpg);"></div>
        <div class="container">
            <h2 class="page-header__title">404 Error</h2>
            <ul class="firdip-breadcrumb list-unstyled">
                <li><a href="index.html">Trang chủ</a></li>
                <li><span>404 Error</span></li>
            </ul>
        </div>
    </section>
    <section class="error-404">
        <div class="container">
            <div class="error-404__img">
                <div class="error-404__img__item">
                    <img src="/site/images/shapes/error-1-2.png" alt="image">
                </div>
                <div class="error-404__img__item error-404__img__item--two">
                    <img src="/site/images/shapes/error-1-1.png" alt="image">
                </div>
            </div>
            <h3 class="error-404__sub-title">Oops! Có lỗi xảy ra</h3>
            <p class="error-404__text">Không tìm thấy tài nguyên bạn yêu cầu</p>
            <form action="#" class="error-404__search">
                <input type="text" id="error-search" placeholder="Type Here">
                <button type="submit" class="error-404__search__btn" aria-label="search submit">
                    <span><i class="icon-search"></i></span>
                </button>
            </form>
            <div class="error-404__btns text-center">
                <a href="{{ route('front.home-page') }}" class="firdip-btn firdip-btn--base error-404__btn"><span>Quay lại trang chủ</span></a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
