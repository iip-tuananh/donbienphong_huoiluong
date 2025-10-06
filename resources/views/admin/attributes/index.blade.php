@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý danh mục thuộc tính
@endsection

@section('title')
    Quản lý danh mục thuộc tính
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-attribute" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Attribute">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

    </div>
    {{-- Form tạo mới --}}
    @include('admin.attributes.create')
    @include('admin.attributes.edit')
</div>
@endsection

@section('script')
@include('admin.attributes.Attribute')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('attributes.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tên thuộc tính'},
            {data: 'products', title: 'Sản phẩm đã áp dụng'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên thuộc tính"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }
    app.controller('Attribute', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/attributes/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        $scope.booking = response.data;
                        $rootScope.$emit("editAttribute", $scope.booking);
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
            $scope.errors = null;
            $scope.$apply();
            $('#edit-attribute').modal('show');
        });

        $('#table-list').on('click', '.remove-att', function () {
            var self = this;
            event.preventDefault();
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/attributes/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        if(response.data.products.length > 0) {
                            swal({
                                title: "Xác nhận!",
                                text: `Thuộc tính này đã được gán cho sản phẩm. Đồng ý xóa?`,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Xác nhận",
                                cancelButtonText: "Hủy",
                                closeOnConfirm: true
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    window.location.href = $(self).attr("href");
                                }
                            })
                        } else {
                            swal({
                                title: "Xác nhận xóa!",
                                text: "Bạn chắc chắn muốn xóa thuộc tính này?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Xác nhận",
                                cancelButtonText: "Hủy",
                                closeOnConfirm: false
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    window.location.href = $(self).attr("href");
                                }
                            })
                        }
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

        });
    })

    </script>
@include('partial.confirm')
@endsection
