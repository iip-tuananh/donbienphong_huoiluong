@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa dịch vụ
@endsection

@section('page_title')
Chỉnh sửa dịch vụ
@endsection

@section('content')
<div ng-controller="Service" ng-cloak>
  @include('admin.services.form')
</div>
@endsection
@section('script')
@include('admin.services.Service')
<script>
    app.controller('Service', function ($scope, $http) {
        $scope.form = new Service(@json($object), {scope: $scope});
        $scope.loading = {};

        $scope.submit = function() {
            $scope.loading.submit = true;
            let data = $scope.form.submit_data;
            $.ajax({
                type: 'POST',
                url: "/admin/services/" + "{{ $object->id }}" + "/update",
                headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = "{{ route('services.index') }}";
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
