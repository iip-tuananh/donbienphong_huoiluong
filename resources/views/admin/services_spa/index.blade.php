@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý dịch vụ
@endsection

@section('title')
    Quản lý dịch vụ
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-service" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="ServiceSpa">
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
        @include('admin.services_spa.create')
        @include('admin.services_spa.edit')

    </div>

</div>
@endsection

@section('script')
@include('admin.services_spa.ServiceSpa')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '/admin/services-spa/searchData',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Dịch vụ'},
            {data: 'image', title: 'Ảnh'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "nhập tên dịch vụ"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('ServiceSpa', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.errors = null;

            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/services-spa/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;

                        $rootScope.$emit("editServiceSpa", $scope.booking);
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
            $('#edit-service').modal('show');
        });


    })

</script>
@include('partial.confirm')
@endsection
