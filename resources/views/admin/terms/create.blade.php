@extends('layouts.main')

@section('title')
Thêm mới điều khoản
@endsection

@section('page_title')
Thêm mới điều khoản
@endsection

@section('content')
<div ng-controller="Question" ng-cloak>
@include('admin.terms.form')
</div>
@endsection
@section('script')
@include('admin.terms.Polivicy')
@include('admin.terms.PolivicyDetail')
<script>
app.controller('Question', function ($scope, $http) {
    $scope.form = new Polivicy({}, {scope: $scope});

    $scope.loading = {}

    $scope.submit = function(publish = 0) {
        $scope.loading.submit = true;
        let data = $scope.form.submit_data;
        $.ajax({
            type: 'POST',
            url: "{{route('terms.store')}}",
            headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: data,
            success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                window.location.href = "{{ route('questions.index') }}";
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

    @include('admin.questions.formJs');
});
</script>
@endsection
