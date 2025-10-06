@extends('layouts.main')

@section('title')
Thêm mới dự án
@endsection

@section('page_title')
Thêm mới dự án
@endsection

@section('content')
<div ng-controller="Project" ng-cloak>
@include('admin.projects.form')
</div>
@endsection
@section('script')
@include('admin.projects.Project')
<script>
app.controller('Project', function ($scope, $http) {
    $scope.form = new Project({}, {scope: $scope});
    $scope.loading = {}

    $scope.submit = function() {
        $scope.loading.submit = true;
        let data = $scope.form.submit_data;
        $.ajax({
            type: 'POST',
            url: "{{route('projects.store')}}",
            headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                window.location.href = "{{ route('projects.index') }}";
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

    @include('admin.services.formJs');
});
</script>
@endsection
