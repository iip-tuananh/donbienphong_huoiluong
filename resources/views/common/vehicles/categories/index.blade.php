@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý dòng xe
@endsection

@section('buttons')
<a href="{{ route('VehicleCategory.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#createVehicleCategory" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('VehicleCategory.exportExcel') }}" class="btn btn-info"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('VehicleCategory.exportPDF') }}" class="btn btn-warning"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="VehicleCategory">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
            {{-- Form sửa --}}
            <div class="modal fade" id="editVehicleCategory" tabindex="-1" role="dialog" aria-hidden="true">
                <form action="#" method="POST" role="form" id="editVehicleCategoryForm">
                    @csrf
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="semi-bold">Sửa dòng xe</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Tên dòng xe</label>
                                    <span class="text-danger">(*)</span>
                                    <input class="form-control" type="text" name="name" ng-model="editing.name">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.name[0] %></strong>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-control" name="status" ng-model="editing.status" ng-class="{'is-invalid': errors && errors.status}">
                                        <option value="1">Hoạt động</option>
                                        <option value="0">Khóa</option>
                                    </select>
                                    <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.status">
                                        <strong><% errors.status[0] %></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" ng-disabled="loading"><i class="fa fa-save"></i> Lưu</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" ng-disabled="loading"><i class="fa fa-remove"></i> Hủy
                                </button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Form tạo mới --}}
    @include('common.vehicles.categories.createVehicleCategory')
</div>
@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('VehicleCategory.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name', title: 'Tên'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					if (data == 0) {
						return `<span class="badge badge-danger">Khóa</span>`;
					} else {
						return `<span class="badge badge-success">Hoạt động</span>`;
					}
				}
			},
			{data: 'created_at', title: "Ngày tạo"},
			{data: 'created_by', title: "Người tạo"},
			{data: 'updated_by', title: "Người sửa"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên dòng xe"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}]
			}
		],
		search_by_time: false,
		// create_link: "{{ route('VehicleCategory.create') }}"
	}).datatable;

    createVehicleCategory = (response) => {
        datatable.ajax.reload();
    }

    app.controller('VehicleCategory', function ($scope, $http) {
            $scope.editing = {};
            $('#table-list').on('click', '.edit', function () {
                $scope.editing = datatable.row($(this).parents('tr')).data();
                $scope.errors = null;
                $scope.$apply();
                $('#editVehicleCategory').modal('show');
            });
            $('#editVehicleCategoryForm').submit(function (e) {
                e.preventDefault();
                $scope.loading = true;
                $scope.$apply();
                var url = "/vehicle-categories/" + $scope.editing.id + "/update";
                var data = $('#editVehicleCategoryForm').serialize();
                $.post(url, data, function (response) {
                    if (response.success) {
                        $('#editVehicleCategory').modal('hide');
                        datatable.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        $scope.errors = response.errors;
                        toastr.warning(response.message);
                    }
                    $scope.loading = false;
                    $scope.$apply();
                });
            })
        })

</script>
@include('partial.confirm')
@endsection