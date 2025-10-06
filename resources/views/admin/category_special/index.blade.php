@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý danh mục đặc biệt
@endsection

@section('title')
    Quản lý danh mục đặc biệt
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-category-special" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="CategorySpecial">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

        {{-- Form tạo mới --}}
        @include('admin.category_special.create')
        @include('admin.category_special.edit')


    </div>

</div>
@endsection

@section('script')
@include('admin.category_special.CategorySpecial')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '/admin/category-special/searchData',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tên danh mục'},
            // {data: 'order_number', title: 'Thứ tự hiển thị', className: "text-center"},
            {data: 'show_home_page', title: 'Hiển thị ngoài trang chủ', className: "text-center"},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên danh mục"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('CategorySpecial', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();

            $.ajax({
                type: 'GET',
                url: "/admin/category-special/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;
                        console.log($scope.booking );

                        $rootScope.$emit("editCategorySpecial", $scope.booking);
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
            $('#edit-category-special').modal('show');
        });

        $('#table-list').on('click', '.remove', function () {
            var self = this;
            event.preventDefault();
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/category-special/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        if(response.data.posts.length > 0) {
                            swal({
                                title: "Xác nhận!",
                                text: `Danh mục này đã được gán cho bài viết. Đồng ý xóa?`,
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
                                text: "Bạn chắc chắn muốn xóa danh mục này?",
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


    $(document).on('click', '.export-button', function(event) {
        event.preventDefault();
        let data = {};
        mergeSearch(data, datatable.context[0]);
        window.location.href = $(this).data('href') + "?" + $.param(data);
    })
</script>
@include('partial.confirm')
@endsection
