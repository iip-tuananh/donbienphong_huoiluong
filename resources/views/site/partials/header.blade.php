<header class="main-header"  ng-controller="headerPartial">
    <!-- top bar -->
    <div class="top-bar fl-wrap">
        <div class="container">
            <div class="date-holder">
                <span class="date_num"></span>
                <span class="date_mounth"></span>
                <span class="date_year"></span>
            </div>
            <div class="header_news-ticker-wrap">
                <div class="header_news-ticker fl-wrap">
                    <ul>
                        @foreach($posts as $p)
                            <li style="font-size: 15px;"><a href="{{ route('front.blogDetail', $p->slug) }}" style="">{{ $p->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="n_contr-wrap">
                    <div class="n_contr p_btn"><i class="fas fa-caret-left" style="color: #fff"></i></div>
                    <div class="n_contr n_btn"><i class="fas fa-caret-right" style="color: #fff"></i></div>
                </div>
            </div>

        </div>
    </div>
    <!-- top bar end -->
    <div class="header-inner fl-wrap">
        <div class="container">
            <!-- logo holder  -->
            <a href="{{ route('front.home-page') }}" class="logo-holder"><img src="{{ $config->image->path ?? '' }}" alt="{{ $config->web_title }}"></a>
            <!-- logo holder end -->
            <div class="search_btn htact show_search-btn"><i class="far fa-search"></i> <span class="header-tooltip">Search</span></div>
            <!-- header-search-wrap -->
            <div class="header-search-wrap novis_sarch">
                <div class="widget-inner">
                    <form>
                        <input name="keyword" type="text" class="search" placeholder="Nhập từ khóa của bạn..." value="" ng-model="keywords"/>
                        <button class="search-submit" id="submit_btn" ng-click="search()"><i class="fa fa-search transition"></i> </button>
                    </form>
                </div>
            </div>
            <!-- header-search-wrap end -->
            <!-- nav-button-wrap-->
            <div class="nav-button-wrap">
                <div class="nav-button">
                    <span></span><span></span><span></span>
                </div>
            </div>
            <!-- nav-button-wrap end-->
            <!--  navigation -->
            <div class="nav-holder main-menu">
                <nav>
                    <ul>
                        <li>
                            <a href="{{ route('front.home-page') }}">Trang Chủ</a>
                        </li>
                        @foreach($postsCategory as $postCategory)
                            @if($postCategory->childs->count())
                                <li>
                                    <a href="{{ route('front.blogs', $postCategory->slug) }}" class="">{{ $postCategory->name }}<i class="fas fa-caret-down"></i></a>
                                    <ul>
                                        @foreach($postCategory->childs as $child)
                                            <li><a href="{{ route('front.blogs', $child->slug) }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li><a href="{{ route('front.blogs', $postCategory->slug) }}">{{ $postCategory->name }}</a></li>
                            @endif
                        @endforeach

                        <li><a href="{{ route('front.contact') }}">Liên Hệ</a></li>
                    </ul>
                </nav>
            </div>
            <!-- navigation  end -->
        </div>
    </div>
</header>
