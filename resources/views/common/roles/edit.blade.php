@extends('layouts.main')

@section('css')
@endsection

@section('title')
    Sửa chức vụ
@endsection

@section('content')
<div ng-controller="Common" ng-cloak>
	@include('common.roles.form')
</div>

@endsection

@section('script')
<script>
app.controller('Common', function ($scope, $http) {
	$scope.form = @json($object);

	@include('common.roles.formJs')

	$scope.form.permissions = @json($rolePermissions);

	$scope.submit = function() {
		$scope.loading.submit = true;
		$.ajax({
			type: 'POST',
			url: "{!! route('Role.update', $object->id) !!}",
			headers: {
				'X-CSRF-TOKEN': CSRF_TOKEN
			},
			data: $scope.form,
			success: function(response) {
				if (response.success) {
					toastr.success(response.message);
					window.location.href = "{{ route('Role.index') }}";
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
