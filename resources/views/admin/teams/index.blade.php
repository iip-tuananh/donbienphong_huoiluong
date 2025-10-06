@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý đội ngũ sáng lập
@endsection

@section('title')
Quản lý đội ngũ sáng lập
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-review" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Reviews">
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
    @include('admin.teams.create')
    @include('admin.teams.edit')
</div>
@endsection

@section('script')
@include('admin.teams.Team')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '/admin/teams/searchData',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'image', title: 'Ảnh', className: "text-center", orderable: false},
            {data: 'name', title: 'Tên nhân viên'},
            {data: 'position', title: 'Chức vụ'},
            {data: 'phone_number', title: 'Số điện thoại'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Khách hàng"},
			{data: 'phone_number', search_type: "text", placeholder: "Số điện thoại"},
		],
	}).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('Reviews', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}


        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/teams/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                if (response.success) {
                    $scope.booking = response.data;
                    $rootScope.$emit("editReview", $scope.booking);
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
            $('#edit-review').modal('show');
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
