@extends('layouts.main')

@section('page_title')
    Thêm mới hàng hóa
@endsection

@section('title')
    Thêm mới hàng hóa
@endsection

@section('title')
    Thêm mới hàng hóa
@endsection
@section('content')
    <div ng-controller="CreateProduct" ng-cloak>
        @include('admin.products.form')
    </div>
@endsection
@section('script')
    @include('admin.products.Product')
    @include('admin.products.createTagGroup')

    <script>
        app.controller('CreateProduct', function ($scope, $http) {
            $scope.origins = @json(\App\Model\Admin\Origin::getForSelect());
            $scope.attributes = @json(\App\Model\Admin\Attribute::getForSelect());
            $scope.tags = @json($tags);
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};
            $scope.manufacturers = @json(\App\Model\Admin\Manufacturer::getForSelect());

            $scope.form = new Product({}, {scope: $scope});

            @include('admin.products.formJs')
                $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;
                $scope.addition_attachments.forEach((val, index) => {
                    data.append('attachments[]', $('#document' + index).get(0).files[0]);
                });
                $.ajax({
                    type: 'POST',
                    url: "/admin/products",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('Product.index') }}";
                        } else {
                            toastr.warning(response.message);
                            $scope.errors = response.errors;
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });
            }

        });
    </script>
@endsection
