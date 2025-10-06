@extends('layouts.main')

@section('title')
Thêm mới danh mục
@endsection

@section('page_title')
Thêm mới danh mục
@endsection

@section('title')
Thêm mới danh mục
@endsection
@section('content')
<div ng-controller="CreateCategory" ng-cloak>
  @include('admin.blog_categories.form')
</div>
@endsection

@section('script')
@include('admin.blog_categories.PostCategory')
<script>
  app.controller('CreateCategory', function ($scope, $http) {
    $scope.form = new PostCategory({}, {scope: $scope});
    $scope.loading = {};
    $scope.submit = function() {
      $scope.loading.submit = true;
      $.ajax({
        type: 'POST',
        url: "{!! route('blogCategory.store') !!}",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: $scope.form.submit_data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('blogCategory.index') }}";
          } else {
            toastr.warning(response.message);
            $scope.errors = response.errors;
          }
        },
        error: function(e) {
          toastr.error('Đã có lỗi xảy ra');
        },
        complete: function() {
          $scope.loading.submit = false;
          $scope.$applyAsync();
        }
      });
    }
  });
</script>
@endsection
