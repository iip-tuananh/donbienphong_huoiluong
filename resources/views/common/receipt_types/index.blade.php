@extends('layouts.main')

@section('css')
@endsection

@section('title')
Danh mục biển số xe
@endsection

@section('buttons')
<a href="{{ route('LicensePlate.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#create-license-plate" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('LicensePlate.exportExcel') }}" class="btn btn-primary"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('LicensePlate.create') }}" class="btn btn-primary"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="LicensePlate">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
            {{-- Form sửa --}}
            <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="POST" role="form" id="edit-modal-form">
                    @csrf
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="semi-bold">Sửa biển số</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Biển số</label>
                                    <span class="text-danger">(*)</span>
                                    <input class="form-control" type="text" ng-model="formEdit.license_plate">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.license_plate[0] %></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-control" ng-model="formEdit.status">
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
    @include('common.license_plates.create_license_plate')
</div>
@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('LicensePlate.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'license_plate', title: 'Biển số xe'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					return getStatus(data, @json(App\Model\Common\LicensePlate::STATUSES));
				}
			},
			{data: 'updated_by', title: "Người sửa"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'license_plate', search_type: "text", placeholder: "Biển số xe"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}]
			}
		],
		search_by_time: false,
	})
    app.controller('LicensePlate', function ($scope, $http) {
        $scope.formEdit = {};
        $scope.loading = {};

        $('#table-list').on('click', '.edit', function () {
            $scope.formEdit = datatable.datatable.row($(this).parents('tr')).data();
            $scope.errors = null;
            $scope.$apply();
            $('#edit-modal').modal('show');
        });
        // Submit mode mới
        $scope.submit = function() {
            $scope.loading.submit = true;
            let url = "/common/license-plates/" + $scope.formEdit.id + "/update";
            let data = $scope.formEdit;
            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#edit-modal').modal('hide');
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
