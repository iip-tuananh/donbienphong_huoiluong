@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Danh mục thẻ tag
@endsection

@section('title')
    Danh mục thẻ tag
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
    @include('admin.tags.create')
    @include('admin.tags.edit')
    <style>
        .square-badge {
            display: inline-block;
            width: 2rem;           /* điều chỉnh kích thước ô vuông */
            height: 2rem;
            line-height: 2rem;     /* canh chữ đứng giữa */
            text-align: center;    /* canh chữ ngang giữa */
            border-radius: .25rem; /* bo góc */
        }
    </style>
</div>
@endsection

@section('script')
@include('admin.tags.Tag')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('tags.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'code', title: 'Mã'},
            {data: 'name', title: 'Tên'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên thẻ"},
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
                url: "/admin/tags/" + $scope.data.id + "/getDataForEdit",
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

    })

    </script>
@include('partial.confirm')
@endsection
