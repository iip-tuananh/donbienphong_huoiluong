@extends('layouts.main')

@section('title')
Thêm mới tin tuyển dụng
@endsection

@section('page_title')
Thêm mới tin tuyển dụng
@endsection

@section('content')
<div ng-controller="Recruitment" ng-cloak>
  @include('admin.recruitments.form')
</div>
@endsection
@section('script')
@include('admin.recruitments.Recruitment')
<script>
  app.controller('Recruitment', function ($scope, $http) {
    $scope.form = new Recruitment({}, {scope: $scope});
    $scope.loading = {}

    $scope.submit = function(publish = 0) {
      $scope.loading.submit = true;
      let data = $scope.form.submit_data;
      data.append('publish', publish);
      $.ajax({
        type: 'POST',
        url: "{!! route('recruitments.store') !!}",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('recruitments.index') }}";
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
