@extends('layouts.main')

@section('css')

@endsection

@section('title')
    Chỉnh sửa hàng hóa
@endsection

@section('page_title')
    Chỉnh sửa hàng hóa
@endsection

@section('content')
    <div ng-controller="EditProduct" ng-cloak>
        @include('admin.products.form')
    </div>
@endsection

@section('script')
    @include('admin.products.Product')
    @include('admin.products.createTagGroup')

    <script>
        app.controller('EditProduct', function ($scope, $http) {
            $scope.manufacturers = @json(\App\Model\Admin\Manufacturer::getForSelect());
            $scope.origins = @json(\App\Model\Admin\Origin::getForSelect());
            $scope.attributes = @json(\App\Model\Admin\Attribute::getForSelect());
            $scope.tags = @json($tags);
            $scope.arrayInclude = arrayInclude;

            $scope.form = new Product(@json($object), {scope: $scope});
            console.log($scope.form.documents);
            $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;
                $scope.addition_attachments.forEach((val, index) => {
                    data.append('attachments[]', $('#document' + index).get(0).files[0]);
                });
                $.ajax({
                    type: 'POST',
                    url: "/admin/products/" + "{{ $object->id }}" + "/update",
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

            $scope.deleteFile = function(file) {
                swal({
                    title: "Xác nhận!",
                    text: `Bạn chắc chắn muốn xóa file này?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Xác nhận",
                    cancelButtonText: "Hủy",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        sendRequest({
                            type: 'POST',
                            url: "{{ route('products.deleteFile', $object->id) }}",
                            data: {
                                file: file
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    $scope.form.attachments = response.data.attachments;
                                } else {
                                    toastr.warning(response.message);
                                }
                            },
                        }, $scope);
                    }
                })
            }

            @include('admin.products.formJs');

        });
    </script>
@endsection
