@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý loại dịch vụ
@endsection

@section('buttons')
<a href="{{ route('ServiceType.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#createServiceType" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('ServiceType.exportExcel') }}" class="btn btn-primary"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('ServiceType.create') }}" class="btn btn-primary"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="ServiceType">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
            {{-- Form sửa --}}
            <div class="modal fade" id="editServiceType" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="POST" role="form" id="editServiceTypeForm">
                    @csrf
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="semi-bold">Sửa nhóm dịch vụ</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Tên</label>
                                    <span class="text-danger">(*)</span>
                                    <input class="form-control" type="text" name="name" ng-model="formEdit.name">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.name[0] %></strong>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-control" name="status" ng-model="formEdit.status">
                                        <option value="1">Hoạt động</option>
                                        <option value="0">Khóa</option>
                                    </select>
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.status[0] %></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                                    <i ng-if="!loading.submit" class="fa fa-save"></i>
                                    <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                                    Lưu
                                </button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Form tạo mới --}}
    @include('common.service_types.createServiceType')
</div>
@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('ServiceType.searchData') !!}',
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
			{data: 'updated_at', title: "Ngày cập nhật"},
			{data: 'updated_by', title: "Người cập nhật"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên loại dịch vụ"},
            {
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}]
			}
		],
		search_by_time: false,
		// create_link: "{{ route('VehicleCategory.create') }}"
	}).datatable;

    createServiceTypeCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('ServiceType', function ($scope, $http) {
        $scope.formEdit = {};
        $scope.loading = {};
        $('#table-list').on('click', '.edit', function () {
            $scope.formEdit = datatable.datatable.row($(this).parents('tr')).data();
            $scope.$apply();
            $scope.errors = null;
            $scope.$apply();
            $('#editServiceType').modal('show');
        });
        // Submit mode mới
        $scope.submit = function() {
            $scope.loading.submit = true;
            $.ajax({
                type: 'POST',
                url: "/common/service-types/" + $scope.formEdit.id + "/update",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.formEdit,
                success: function(response) {
                    if (response.success) {
                        $('#editServiceType').modal('hide');
                        datatable.datatable.ajax.reload();
                        toastr.success(response.message);
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
        }
    })

</script>
@include('partial.confirm')
@endsection