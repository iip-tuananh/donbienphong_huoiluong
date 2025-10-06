@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa bài viết
@endsection

@section('page_title')
Chỉnh sửa bài viết
@endsection

@section('content')
<div ng-controller="Post" ng-cloak>
  @include('admin.abouts_page.form')
</div>
@endsection
@section('script')
@include('admin.abouts_page.Post')
<script>
  app.controller('Post', function ($scope, $http, $timeout) {
    $scope.form = new Post(@json($object), {scope: $scope});
    $scope.loading = {};
    @include('admin.posts.formJs')

    $scope.submit = function(publish = 0) {
      $scope.loading.submit = true;
      let data = $scope.form.submit_data;
      data.append('publish', publish);
      $.ajax({
        type: 'POST',
        url: "/admin/about-page/" + "{{ $object->id }}" + "/update",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('about-page.index') }}";
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
