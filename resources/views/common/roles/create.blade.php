@extends('layouts.main')

@section('css')

@endsection

@section('title')
Thêm mới chức vụ
@endsection

@section('content')
<div ng-controller="Common" ng-cloak>
	@include('common.roles.form')
</div>

@endsection

@section('script')
<script>
	app.controller('Common', function ($scope, $http) {
	$scope.form = {};
	$scope.form.status = 1;

	// @if (Auth::user()->type == App\Model\Common\User::SUPER_ADMIN)
	// 	$scope.form.type = {{ App\Model\Common\User::QUAN_TRI_VIEN }};
	// @elseif (Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN)
	// 	$scope.form.type = {{ App\Model\Common\User::G7 }};
	// @else
	// 	$scope.form.type = {{ App\Model\Common\User::NHAN_VIEN_G7 }};
	// @endIf

	$scope.form.permissions = [];

	@include('common.roles.formJs')

	$scope.submit = function() {
		$scope.loading.submit = true;
		$.ajax({
			type: 'POST',
			url: "{!! route('Role.store') !!}",
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