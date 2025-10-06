@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý nhân viên liên hệ
@endsection

@section('title')
    Quản lý nhân viên liên hệ
@endsection

@section('buttons')
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Consultants">
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
    @include('admin.consultants.create')
    @include('admin.consultants.edit')
</div>
@endsection

@section('script')
@include('admin.consultants.Consultant')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('consultants.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tên nhân viên'},
            {data: 'phone', title: 'Số điện thoại'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên nhân viên"},
            {data: 'phone', search_type: "text", placeholder: "Số điện thoại"},
        ],
        create_modal_2: true
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }
    app.controller('Consultants', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $(document).on('click', '.create-modal', function () {
            $scope.errors = null;
            $rootScope.$emit("createConsultant", $scope.errors, $scope.form);
        })

        $('#table-list').on('click', '.edit', function () {
            $scope.errors = null;

            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/consultants/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        $scope.booking = response.data;
                        $rootScope.$emit("editConsultant", $scope.booking);
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
            $('#edit-consultant').modal('show');
        });
    })
</script>
@include('partial.confirm')
@endsection
