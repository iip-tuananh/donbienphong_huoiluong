@extends('layouts.main')

@section('title')
    Thêm chính sách
@endsection

@section('page_title')
    Thêm chính sách
@endsection

@section('title')
    Thêm chính sách
@endsection
@section('content')
    <div ng-controller="CreatePolicy" ng-cloak>
        @include('admin.policies.form')
    </div>
@endsection
@section('script')
    @include('admin.policies.Policy')
    <script>
        app.controller('CreatePolicy', function ($scope, $http) {
            $scope.form = new Policy({}, {scope: $scope});
            $scope.loading = {};

            // Submit Form tạo mới
            $scope.submit = function () {
                let url = "{!! route('policies.store') !!}";
                $scope.loading.submit = true;

                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: $scope.form.submit_data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('policies.index') }}";
                            $scope.errors = null;
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    },
                });
            }
        })
    </script>
@endsection
