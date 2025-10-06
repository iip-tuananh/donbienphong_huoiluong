@extends('layouts.main')

@section('css')
@endsection

@section('title')
    Sửa người dùng
@endsection

@section('content')
<div ng-controller="Common" ng-cloak>
	@include('common.users.form')
</div>

@endsection

@section('script')
@include('partial.classes.common.User')
<script>
app.controller('Common', function ($scope, $http) {
	$scope.form = new User(@json($object), {scope: $scope});
	$scope.form.roles = $scope.form.roles.map(val => val.id);
	console.log($scope.form.roles);

	@include('common.users.formJs')

	$scope.submit = function() {
		$scope.loading.submit = true;
		let data = $scope.form.submit_data;
		$.ajax({
			type: 'POST',
			url: "{!! route('User.update', $object->id) !!}",
			headers: {
				'X-CSRF-TOKEN': CSRF_TOKEN
			},
			processData: false,
			contentType: false,
			data: data,
			success: function(response) {
				if (response.success) {
					toastr.success(response.message);
					window.location.href = "{{ route('User.index') }}";
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
