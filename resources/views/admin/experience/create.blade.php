@extends('layouts.main')

@section('page_title')
    Thêm mới trải nghiệm
@endsection

@section('title')
    Thêm mới trải nghiệm
@endsection

@section('title')
    Thêm mới trải nghiệm
@endsection
@section('content')
    <div ng-controller="CreateExperience" ng-cloak>
        @include('admin.experience.form')
    </div>
@endsection
@section('script')
    @include('admin.experience.Experience')

    <script>
        app.controller('CreateExperience', function ($scope, $http) {
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};

            $scope.form = new Experience({}, {scope: $scope});

            @include('admin.experience.formJs')
                $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;

                $.ajax({
                    type: 'POST',
                    url: "/admin/experience",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('experience.index') }}";
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
