<footer class="fl-wrap main-footer">
    <div class="container">
        <!-- footer-widget-wrap -->
        <div class="footer-widget-wrap fl-wrap">
            <div class="row">
                <!-- footer-widget -->
                <div class="col-md-4">
                    <div class="footer-widget">
                        <div class="footer-widget-content">
                            <a href="{{ route('front.home-page') }}" class="footer-logo"><img src="{{ $config->image->path ?? '' }}" alt=""></a>
                            <p>
                                {{ $config->introduction }}
                            </p>
                            <div class="footer-social fl-wrap">
                                <ul>
                                    <li><a href="{{ $config->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="{{ $config->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="{{ $config->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="{{ $config->youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer-widget  end-->
                <!-- footer-widget -->
                <div class="col-md-2">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Danh mục </div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box fl-wrap">
                                <ul>
                                    @foreach($postsCategory as $postCategory)
                                        <li> <a href="{{ route('front.blogs', $postCategory->slug) }}">{{ $postCategory->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer-widget  end-->
                <!-- footer-widget -->
                <div class="col-md-2">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Liên kết</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box fl-wrap">
                                <ul>
                                    <li> <a href="{{ route('front.home-page') }}">Trang chủ</a></li>
                                    <li> <a href="{{ route('front.abouts') }}">Về chúng tôi</a></li>
                                    <li> <a href="{{ route('front.contact') }}">Liên hệ</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer-widget  end-->
                <!-- footer-widget -->
                <div class="col-md-4">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Đăng ký</div>
                        <div class="footer-widget-content">
                            <div class="subcribe-form fl-wrap">
                                <p>Để lại email để luôn nhận được những tin tức và cập nhật mới nhất.</p>
                                <form id="subscribe" class="fl-wrap">
                                    <input class="enteremail" name="email" id="subscribe-email" placeholder="Your Email" spellcheck="false" type="text">
                                    <button type="submit" id="subscribe-button" class="subscribe-button color-bg">Send </button>
                                    <label for="subscribe-email" class="subscribe-message"></label>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer-widget  end-->
            </div>
        </div>
        <!-- footer-widget-wrap end-->
    </div>
    <div class="footer-bottom fl-wrap">
        <div class="container">
            <div class="copyright"><span style="color: #fff">&#169; {{ $config->short_name_company }} 2025</span> . All rights reserved. </div>
            <div class="to-top"> <i class="fas fa-caret-up"></i></div>
            {{--            <div class="subfooter-nav">--}}
            {{--                <ul>--}}
            {{--                    <li><a href="#">Terms & Conditions</a></li>--}}
            {{--                    <li><a href="#">Privacy Policy</a></li>--}}
            {{--                </ul>--}}
            {{--            </div>--}}
        </div>
    </div>
</footer>
