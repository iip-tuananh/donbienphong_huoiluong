@extends('layouts.main')

@section('page_title')
    Thêm mới hạng phòng
@endsection

@section('title')
    Thêm mới hạng phòng
@endsection

@section('title')
    Thêm mới hạng phòng
@endsection
@section('content')
    <div ng-controller="CreateRoom" ng-cloak>
        @include('admin.rooms.form')
    </div>
@endsection
@section('script')
    @include('admin.rooms.Room')

    <script>
        app.controller('CreateRoom', function ($scope, $http) {
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};

            $scope.form = new Room({}, {scope: $scope});

            @include('admin.rooms.formJs')
                $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;

                $.ajax({
                    type: 'POST',
                    url: "/admin/rooms",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('Room.index') }}";
                        } else {
                            toastr.warning(response.message);
                            $scope.errors = response.errors;
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });
            }

        });
    </script>
@endsection
