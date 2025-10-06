@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý danh mục đối tác
@endsection

@section('title')
    Quản lý danh mục đối tác
@endsection

@section('buttons')
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Partner">
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
    @include('admin.config_static.create')
    @include('admin.config_static.edit')
</div>
@endsection

@section('script')
@include('admin.config_static.ConfigStatic')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('configStatistic.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'title', title: 'Tiêu đề'},
            {data: 'des', title: 'Nội dung'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'title', search_type: "text", placeholder: "Tiêu đề"},
        ],
        create_modal_2: true
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }
    app.controller('Partner', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $(document).on('click', '.create-modal', function () {
            $scope.errors = null;
            $rootScope.$emit("createPartner", $scope.errors, $scope.form);
        })

        $('#table-list').on('click', '.edit', function () {
            $scope.errors = null;

            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/configStatistic/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        $scope.booking = response.data;
                        $rootScope.$emit("editPartner", $scope.booking);
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
            $('#edit-partner').modal('show');
        });
    })
</script>
@include('partial.confirm')
@endsection
