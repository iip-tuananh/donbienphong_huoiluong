@extends('layouts.main')

@section('title')
    Chỉnh sửa chính sách
@endsection

@section('page_title')
    Chỉnh sửa chính sách
@endsection

@section('title')
    Chỉnh sửa chính sách
@endsection
@section('content')
    <div ng-controller="EditPolicy" ng-cloak>
        @include('admin.policies.form')
    </div>
@endsection
@section('script')
    @include('admin.policies.Policy')
    <script>
        app.controller('EditPolicy', function ($rootScope, $scope, $http) {
            $scope.form = new Policy(@json($object), {scope: $scope});
            $scope.loading = {};
            $scope.loading.submit = false;

            // Submit Form sửa
            $scope.submit = function () {
                let url = "/admin/policies/" + $scope.form.id + "/update";
                $scope.loading.submit = true;

                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: $scope.form.submit_data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $scope.errors = null;
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                        window.location.href = "{{ route('policies.index') }}";
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    },
                });
            }


        })

    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7DU37dL6MMP6g0LDFoU109Ps3YQbeH00&callback=initAutocomplete"
            async defer></script>
@endsection
