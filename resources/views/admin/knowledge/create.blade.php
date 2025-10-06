@extends('layouts.main')

@section('page_title')
    Thêm mới bài viết kiến thức
@endsection

@section('title')
    Thêm mới bài viết kiến thức
@endsection

@section('title')
    Thêm mới bài viết kiến thức
@endsection
@section('content')
    <div ng-controller="CreateBlog" ng-cloak>
        @include('admin.knowledge.form')
    </div>
@endsection
@section('script')
    @include('admin.knowledge.Knowledge')

    <script>
        app.controller('CreateBlog', function ($scope, $http) {
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};

            $scope.form = new Knowledge({}, {scope: $scope});

            @include('admin.blog.formJs')
                $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;

                $.ajax({
                    type: 'POST',
                    url: "/admin/knowledge",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('knowledge.index') }}";
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
