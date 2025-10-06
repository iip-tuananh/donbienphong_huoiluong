<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    @include('site.partials.head')
    @yield('css')
</head>

<body ng-app="App">


<div id="main">
    <!-- progress-bar  -->
    <div class="progress-bar-wrap">
        <div class="progress-bar color-bg"></div>
    </div>
    <div id="translate_select"></div>

    @include('site.partials.header')

    <div id="wrapper">
        @yield('content')

        @include('site.partials.footer')

    </div>



</div>

<script src="/site/js/jquery.min.js"></script>
<script src="/site/js/plugins.js"></script>
<script src="/site/js/scripts.js"></script>



    <script>
        var CSRF_TOKEN = "{{ csrf_token() }}";
        window.USER_AVATAR_URL = "{{ $customer->avatar->path ?? '/site/img/user.png' }}";
    </script>

    @include('site.partials.angular_mix')



    <script>
        app.controller('headerPartial', function ($rootScope, $scope, $interval, $window) {

            $scope.search = function () {
                if (!$scope.keywords || !$scope.keywords.trim()) {
                    alert('Vui lòng nhập từ khóa tìm kiếm!');
                    return;
                }

                // Xây URL cơ bản
                var url = '/tim-kiem?keywords=' + encodeURIComponent($scope.keywords.trim());

                // Điều hướng
                $window.location.href = url;
            };

        });

    </script>


    @stack('scripts')
</body>

</html>
