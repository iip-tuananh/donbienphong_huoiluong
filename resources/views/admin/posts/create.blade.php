@extends('layouts.main')

@section('title')
Thêm mới bài viết
@endsection

@section('page_title')
Thêm mới bài viết
@endsection

@section('content')
<div ng-controller="Post" ng-cloak>
  @include('admin.posts.form')
</div>
@endsection
@section('script')
@include('admin.posts.Post')

<script>
    const input = document.getElementById('file-input');
    input.addEventListener('change', e => {
        const files = Array.from(e.target.files || []);
        window.addFilesFromInput(files);
        input.value = ''; // reset để lần sau chọn cùng tên vẫn nhận
    });

    // Kéo–thả: gắn vào chính .upload-box (label cũng OK)
    const box = document.querySelector('.upload-box');
    ['dragenter','dragover'].forEach(ev => {
        box.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); box.style.background='#f0f6ff'; });
    });
    ['dragleave','drop'].forEach(ev => {
        box.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); box.style.background=''; });
    });
    box.addEventListener('drop', e => {
        const files = Array.from(e.dataTransfer.files || []);
        window.addFilesFromInput(files);
    });
</script>


<script>
  app.controller('Post', function ($scope, $http, $timeout) {
    $scope.form = new Post({}, {scope: $scope});
    $scope.loading = {}

    @include('admin.posts.formJs')
    $scope.submit = function(publish = 0) {
        console.log( $scope.form)
      $scope.loading.submit = true;
      let data = $scope.form.submit_data;
      console.log(data)
      $.ajax({
        type: 'POST',
        url: "/admin/posts",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('Post.index') }}";
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
