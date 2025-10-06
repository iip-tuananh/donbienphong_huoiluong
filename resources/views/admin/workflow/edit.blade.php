@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa văn hóa doanh nghiệp
@endsection

@section('page_title')
  Chỉnh sửa văn hóa doanh nghiệp
@endsection

@section('content')
<div ng-controller="Post" ng-cloak>
  @include('admin.workflow.form')
</div>
@endsection
@section('script')
@include('admin.workflow.Workflow')
<script>
  app.controller('Post', function ($scope, $http, $timeout) {
    $scope.form = new Workflow(@json($object), {scope: $scope});
    $scope.loading = {};
    @include('admin.posts.formJs')

    $scope.submit = function() {
      $scope.loading.submit = true;
      let data = $scope.form.submit_data;
      $.ajax({
        type: 'POST',
        url: "/admin/workflow/update",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
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
