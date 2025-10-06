@extends('layouts.main')

@section('page_title')
    Thêm mới tư liệu truyền thông
@endsection

@section('title')
    Thêm mới tư liệu truyền thông
@endsection

@section('title')
    Thêm mới tư liệu truyền thông
@endsection
@section('content')
    <div ng-controller="CreateExperience" ng-cloak>
        @include('admin.document.form')
    </div>
@endsection
@section('script')
    @include('admin.document.DocumentObj')

    <script>
        app.controller('CreateExperience', function ($scope, $http) {
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};

            $scope.form = new DocumentObj({}, {scope: $scope});

            @include('admin.document.formJs')
                $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;

                $.ajax({
                    type: 'POST',
                    url: "/admin/document",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('document.index') }}";
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
