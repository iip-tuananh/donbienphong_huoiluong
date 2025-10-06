@extends('layouts.main')

@section('css')

@endsection

@section('title')
    Chỉnh sửa hạng phòng
@endsection

@section('page_title')
    Chỉnh sửa hạng phòng
@endsection

@section('content')
    <div ng-controller="EditRoom" ng-cloak>
        @include('admin.rooms.form')
    </div>
@endsection

@section('script')
    @include('admin.rooms.Room')
    <script>
        app.controller('EditRoom', function ($scope, $http) {
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};
            @include('admin.rooms.formJs')

            $scope.form = new Room(@json($object), {scope: $scope});
            $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;
                $.ajax({
                    type: 'POST',
                    url: "/admin/rooms/" + "{{ $object->id }}" + "/update",
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
