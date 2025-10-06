@extends('layouts.main')

@section('css')

@section('title')
Chỉnh sửa danh mục bài viết
@endsection

@section('page_title')
Chỉnh sửa danh mục bài viết
@endsection

@section('content')
<div ng-controller="EditCategory" ng-cloak>
    @include('admin.post_categories.form')
</div>
@endsection

@section('script')
@include('admin.post_categories.PostCategory')
<script>
    app.controller('EditCategory', function ($scope, $http) {
    $scope.form = new PostCategory(@json($object), {scope: $scope});
    // console.log($scope.form);
        $scope.show_home_page = [
            {'name': 'hiển thị', 'value': '1'},
            {'name': 'không', 'value': '0'},
        ];
    $scope.loading = {};
    $scope.submit = function() {
      $scope.loading.submit = true;
      $.ajax({
        type: 'POST',
        url: "/admin/post-categories/" + "{{ $object->id }}" + "/update",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: $scope.form.submit_data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('PostCategory.index') }}";
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
