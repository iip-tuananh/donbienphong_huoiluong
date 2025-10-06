@extends('site.layouts.master')
@section('title')
    <title>{{ "Không tìm thấy trang - " . ucfirst($_SERVER['HTTP_HOST']) }}</title>
@endsection
@section('css')

@endsection

@section('content')
    <main id="main" class="">
        <div id="primary" class="content-area">
            <main id="main" class="site-main container pt" role="main">
                <section class="error-404 not-found mt mb">
                    <div class="row">
                        <div class="col medium-3"><span class="header-font" style="font-size: 6em; font-weight: bold; opacity: .3">404</span></div>
                        <div class="col medium-9">
                            <header class="page-title">
                                <h1 class="page-title">Oops! That page can’t be found.</h1>
                            </header>
                            <div class="page-content">
                                <p>It looks like nothing was found at this location. Maybe try one of the links below or a search?</p>
{{--                                <form method="get" class="searchform" action="https://noithathoanganh.vn/" role="search">--}}
{{--                                    <div class="flex-row relative">--}}
{{--                                        <div class="flex-col flex-grow">--}}
{{--                                            <input type="search" class="search-field mb-0" name="s" value="" id="s" placeholder="Tìm kiếm…" autocomplete="off">--}}
{{--                                        </div>--}}
{{--                                        <div class="flex-col">--}}
{{--                                            <button type="submit" class="ux-search-submit submit-button secondary button icon mb-0" aria-label="Submit">--}}
{{--                                                <i class="icon-search"></i>				</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="live-search-results text-left z-top"><div class="autocomplete-suggestions" style="position: absolute; display: none; max-height: 300px; z-index: 9999;"></div></div>--}}
{{--                                </form>--}}
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>

    </main>

@endsection
@push('scripts')

@endpush
