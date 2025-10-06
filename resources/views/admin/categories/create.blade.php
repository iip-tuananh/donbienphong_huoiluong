@extends('layouts.main')

@section('css')

@endsection


@section('title')
Thêm mới danh mục hàng hóa
@endsection

@section('page_title')
Thêm mới danh mục hàng hóa
@endsection

@section('content')
<div ng-controller="CreateCategory" ng-cloak>
  @include('admin.categories.form')
</div>
@endsection

@section('script')
@include('admin.categories.Category')
<script>
  app.controller('CreateCategory', function ($scope, $http) {
    $scope.form = new Category({}, {scope: $scope});
    $scope.loading = {};
    $scope.show_home_page = [
            {'name': 'hiển thị', 'value': '1'},
            {'name': 'không', 'value': '0'},
        ];

    $scope.mode = 'create';
    $scope.submit = function() {
      $scope.loading.submit = true;
      $.ajax({
        type: 'POST',
        url: "{!! route('Category.store') !!}",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: $scope.form.submit_data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('Category.index') }}";
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

      $(document).on('click', '.remove-category', function (e) {
          e.preventDefault();
          $scope.form.parent_id = null;
      })

  });
</script>
@endsection
