@extends('site.layouts.master')
@section('title')Liên hệ - {{ $config->web_title }}@endsection
@section('description'){{ strip_tags(html_entity_decode($config->introduction)) }}@endsection
@section('image'){{@$config->image->path ?? ''}}@endsection

@section('css')

@endsection

@section('content')
    <div class="content" ng-controller="AboutPage">
        <!--section   -->
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a><span>Liên hệ</span>
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
                    <div class="col-md-5">
                        <div class="pr-subtitle prs_big">Thông tin liên hệ</div>
                        <!--card-item -->
                        <ul class="contacts-list fl-wrap">
                            <li><span><i class="fal fa-map-marker"></i> Địa chỉ :</span> <a href="#">{{ $config->address_company }}</a></li>
                            <li><span><i class="fal fa-phone"></i> Hotline :</span> <a href="#">{{ $config->hotline }}</a></li>
                            <li><span><i class="fal fa-envelope"></i> Mail :</span> <a href="#">{{ $config->email }}</a></li>
                        </ul>
                        <!--card-item end -->
                        <div class="contact-social fl-wrap">
                            <span class="cs-title">Mạng xã hội: </span>
                            <ul>
                                <li><a href="{{ $config->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="{{ $config->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="{{ $config->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="{{ $config->youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                        <!-- box-widget -->
                        <div class="box-widget-content fl-wrap">
                            {!! $config->location !!}
                        </div>
                        <!-- box-widget  end -->
                    </div>
                    <div class="col-md-7">
                        <div class="pad-con fl-wrap">
                            <div class="pr-subtitle prs_big">Để lại lời nhắn</div>
                            <p>Mọi thắc mắc cần hỗ trợ, hãy để lại lời nhắn. Chúng tôi sẽ sớm liên hệ lại với bạn</p>
                            <div id="contact-form" style="margin-top: 20px;">
                                <div id="message"></div>
                                <form  id="form-contact" class="custom-form"  name="contactform" ng-cloak>
                                    <fieldset>
                                        <input type="text" name="name" id="name" placeholder="Họ tên *" value=""/>
                                        <div class="invalid-feedback d-block" ng-if="errors['name']"><% errors['name'][0] %></div>

                                        <input type="text"  name="phone" id="phone" placeholder="Số điện thoại" value=""/>
                                        <div class="invalid-feedback d-block" ng-if="errors['phone']"><% errors['phone'][0] %></div>

                                        <input type="text"  name="email" id="email" placeholder="Email*" value=""/>
                                        <div class="invalid-feedback d-block" ng-if="errors['email']"><% errors['email'][0] %></div>


                                        <textarea name="message"  id="comments" cols="40" rows="3" placeholder="Lời nhắn" style="margin-bottom: 10px"></textarea>
                                        <div class="invalid-feedback d-block" ng-if="errors['message']"><% errors['message'][0] %></div>

                                    </fieldset>
                                    <button class="btn   color-bg float-btn" id="button" ng-click="submitContact()">Gửi <i class="fas fa-caret-right"></i></button>
                                </form>
                            </div>
                            <!-- contact form  end-->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        app.controller('AboutPage', function ($rootScope, $scope, $sce, $interval) {
            $scope.errors = [];
            $scope.submitContact = function () {
                var url = "{{route('front.submitContact')}}";
                var data = jQuery('#form-contact').serialize();
                $scope.loading = true;
                jQuery.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            jQuery('#form-contact')[0].reset();
                            $scope.errors = [];
                            $scope.$apply();
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading = false;
                        $scope.$apply();
                    }
                });
            }
        })

    </script>
@endpush
