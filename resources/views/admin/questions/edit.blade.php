@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa câu hỏi
@endsection

@section('page_title')
Chỉnh sửa câu hỏi
@endsection

@section('content')
<div ng-controller="Question" ng-cloak>
  @include('admin.questions.form')
</div>
@endsection
@section('script')
@include('admin.questions.Question')
<script>
    app.controller('Question', function ($scope, $http) {
        $scope.form = new Question(@json($object), {scope: $scope});

        $scope.topics = @json(\App\Model\Admin\Topic::getForSelect());

        $scope.loading = {};

        $scope.submit = function(publish = 0) {
            $scope.loading.submit = true;
            let data = $scope.form.submit_data;
            $.ajax({
                type: 'POST',
                url: "/admin/questions/" + "{{ $object->id }}" + "/update",
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
