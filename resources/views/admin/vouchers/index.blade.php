@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý mã giảm giá
@endsection

@section('title')
    Quản lý mã giảm giá
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-voucher" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Voucher">
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
        @include('admin.vouchers.create')
        @include('admin.vouchers.edit')


    </div>

</div>
@endsection

@section('script')
@include('admin.vouchers.Voucher')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '/admin/vouchers/searchData',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'code', title: 'Mã'},
            {data: 'name', title: 'Tên mã giảm giá'},
            {data: 'description', title: 'Mô tả'},
            {data: 'quantity', title: 'Số lượng còn lại', className: "text-center"},
            {data: 'from_date', title: 'Ngày áp dụng'},
            {data: 'to_date', title: 'Ngày hết hạn'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên mã giảm giá"},
            {data: 'code', search_type: "text", placeholder: "Mã giảm giá"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('Voucher', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();

            $.ajax({
                type: 'GET',
                url: "/admin/vouchers/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;

                        $rootScope.$emit("editVoucher", $scope.booking);
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
            $('#edit-voucher').modal('show');
        });
    })
</script>
@include('partial.confirm')
@endsection
