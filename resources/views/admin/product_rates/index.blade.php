@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Đánh giá sản phẩm
@endsection

@section('title')
    Đánh giá sản phẩm
@endsection

@section('buttons')
{{-- <a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-category-special" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a> --}}
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="ProductRate">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

        @include('admin.product_rates.edit')

        <div class="modal fade" id="update-status" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="semi-bold">Đổi trạng thái</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group custom-group">
                                            <label class="form-label">Trạng thái</label>
                                            <select class="form-control" ng-model="form.status">
                                                <option value="">Chọn trạng thái</option>
                                                <option ng-repeat="s in statues" ng-value="s.id" ng-selected="s.id == form.status">
                                                    <% s.name %>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-cons" ng-click="submitUpdateStatus()" ng-disabled="loading.submit">
                            <i ng-if="!loading.submit" class="fa fa-save"></i>
                            <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                            Lưu
                        </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </div>

</div>
@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{{ route('product_rates.searchData') }}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'product_name', title: 'Tên sản phẩm'},
            {data: 'user_name', title: 'Tên người đánh giá'},
            {data: 'rating', title: 'Điểm đánh giá', className: "text-center"},
            {data: 'title', title: 'Tiêu đề'},
            {data: 'desc', title: 'Nội dung'},
            {data: 'status', title: 'Trạng thái', className: "text-center"},
            {data: 'created_at', title: 'Ngày gửi'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'product_name', search_type: "text", placeholder: "Tên sản phẩm"},
            {data: 'user_name', search_type: "text", placeholder: "Tên người đánh giá"},
            {data: 'title', search_type: "aj", placeholder: "Tiêu đề"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('ProductRate', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {};
        $scope.statues = [
            {id: 2, name: 'Duyệt và xuất bản'},
            {id: 0, name: 'Không duyệt'},
        ];

        $('#table-list').on('click', '.update-status', function () {
            event.preventDefault();
            $scope.data = datatable.row($(this).parents('tr')).data();
            $scope.form.status = $scope.data.status;
            $scope.$apply();
            $('#update-status').modal('show');
        });

        $scope.submitUpdateStatus = function () {
            $scope.loading.submitUpdateStatus = true;
            $.ajax({
                type: 'POST',
                url: "{{route('product_rates.update.status')}}",
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                data: {
                    product_rate_id: $scope.data.id,
                    status:  $scope.form.status
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                },
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $('#update-status').modal('hide');
                    datatable.ajax.reload();
                    $scope.loading.submitUpdateStatus = false;
                    $scope.$applyAsync();
                }
            });
        }

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();

            $.ajax({
                type: 'GET',
                url: "/admin/product-rates/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;
                        console.log($scope.booking );

                        $rootScope.$emit("editProductRate", $scope.booking);
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
            $('#edit-product-rate').modal('show');
        });

        // $('#table-list').on('click', '.remove', function () {
        //     var self = this;
        //     event.preventDefault();
        //     $scope.data = datatable.row($(this).parents('tr')).data();
        //     $.ajax({
        //         type: 'GET',
        //         url: "/admin/category-special/" + $scope.data.id + "/getDataForEdit",
        //         headers: {
        //             'X-CSRF-TOKEN': CSRF_TOKEN
        //         },
        //         data: $scope.data.id,

        //         success: function(response) {
        //             if (response.success) {
        //                 if(response.data.products.length > 0 || response.data.posts.length > 0) {
        //                     swal({
        //                         title: "Xác nhận!",
        //                         text: `Danh mục này đã được gán cho sản phẩm hoặc bài viết. Đồng ý xóa?`,
        //                         type: "warning",
        //                         showCancelButton: true,
        //                         confirmButtonClass: "btn-danger",
        //                         confirmButtonText: "Xác nhận",
        //                         cancelButtonText: "Hủy",
        //                         closeOnConfirm: true
        //                     }, function(isConfirm) {
        //                         if (isConfirm) {
        //                             window.location.href = $(self).attr("href");
        //                         }
        //                     })
        //                 } else {
        //                     swal({
        //                         title: "Xác nhận xóa!",
        //                         text: "Bạn chắc chắn muốn xóa danh mục này?",
        //                         type: "warning",
        //                         showCancelButton: true,
        //                         confirmButtonClass: "btn-danger",
        //                         confirmButtonText: "Xác nhận",
        //                         cancelButtonText: "Hủy",
        //                         closeOnConfirm: false
        //                     }, function(isConfirm) {
        //                         if (isConfirm) {
        //                             window.location.href = $(self).attr("href");
        //                         }
        //                     })
        //                 }
        //             } else {
        //                 toastr.warning(response.message);
        //                 $scope.errors = response.errors;
        //             }
        //         },
        //         error: function(e) {
        //             toastr.error('Đã có lỗi xảy ra');
        //         },
        //         complete: function() {
        //             $scope.loading.submit = false;
        //             $scope.$applyAsync();
        //         }
        //     });
        // });
    })

</script>
@include('partial.confirm')
@endsection
