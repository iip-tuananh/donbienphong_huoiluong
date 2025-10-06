@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý tài sản cố định
@endsection

@section('buttons')
<a class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#create-fixed-asset" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="javascript:void(0)" target="_blank" data-href="{{ route('FixedAsset.exportExcel') }}" class="btn btn-info export-button"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="javascript:void(0)" target="_blank" data-href="{{ route('FixedAsset.exportPDF') }}" class="btn btn-warning export-button"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="FixedAssets">
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
                <form method="POST" role="form" id="editModalForm">
                    @csrf
                    <div class="modal-dialog" style="max-width: 850px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="semi-bold">Sửa tài sản cố định</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-8 col-sm-12">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="required-label">Tên tài sản</label>
                                                    <input class="form-control" type="text" ng-model="formEdit.name" placeholder="Tên hàng hóa (*)">
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.name[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="required-label">Mã tài sản</label>
                                                    <input class="form-control" type="text" ng-model="formEdit.code">
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.code[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="required-label">Thời gian khấu hao</label>
                                                    <input class="form-control" type="text" ng-model="formEdit.depreciation_period">
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.depreciation_period[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group custom-group">
                                                    <label class="required-label">Đơn vị tính</label>
                                                    <select class="form-control select2-in-modal" style="width: 100%;" ng-model="formEdit.unit_id">
                                                        <option value="">Chọn đơn vị tính</option>
                                                        <option ng-repeat="u in units" ng-value="u.id" ng-selected="formEdit.unit_id == u.id"><% u.name %></option>
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong><% errors.unit_id[0] %></strong>
                                                        </span>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="required-label">Giá nhập định mức</label>
                                                    <input class="form-control" type="text" ng-model="formEdit.import_price_quota">
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.import_price_quota[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="form-label">Trạng thái</label>
                                                    <select class="form-control" select2 style="width: 100%;" name="status" ng-model="formEdit.status">
                                                        <option value="1">Hoạt động</option>
                                                        <option value="0">Khóa</option>
                                                    </select>
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.status[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group custom-group">
                                                    <label class="form-label">Ghi chú</label>
                                                    <textarea id="my-textarea" class="form-control" ng-model="formEdit.note" rows="3"></textarea>
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong><% errors.price[0] %></strong>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group text-center">
                                            <div class="main-img-preview">
                                                <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 2MB.</p>
                                                <img class="thumbnail img-preview" ng-src="<% formEdit.image.path %>">
                                            </div>
                                            <div class="input-group" style="width: 100%; text-align: center">
                                                <div class="input-group-btn" style="margin: 0 auto">
                                                    <div class="fileUpload fake-shadow cursor-pointer">
                                                        <label class="mb-0" for="<% formEdit.image.element_id %>">
                                                            <i class="glyphicon glyphicon-upload"></i> Chọn ảnh
                                                        </label>
                                                        <input class="d-none" id="<% formEdit.image.element_id %>" type="file" class="attachment_upload" accept=".jpg,.jpeg,.png">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong><% errors.image[0] %></strong>
                                            </span>
                                        </div>
                                    </div>
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
    @include('common.fixed_assets.createFixedAsset')
</div>
@endsection

@section('script')
@include('partial.classes.uptek.FixedAsset')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('FixedAsset.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {
                data: 'image', title: "Hình ảnh", orderable: false, className: "text-center",
                render: function (data) {
					return `<img src="${data.path}" width="100">`;
				}
            },
            {data: 'name', title: 'Tên'},
            {data: 'unit', title: 'Đơn vị tính'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					return getStatus(data, @json(App\Model\Uptek\FixedAsset::STATUSES));
				}
			},
			{data: 'depreciation_period', title: "Thời gian khấu hao (tháng)"},
			{data: 'updated_by', title: "Người sửa"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên tài sản"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: @json(App\Model\Uptek\FixedAsset::STATUSES)
			}
		],
		search_by_time: false,
	}).datatable
    app.controller('FixedAssets', function ($scope, $http) {
        $scope.units = @json(App\Model\Common\Unit::all());
        $scope.loading = {};
        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $scope.formEdit = new FixedAsset($scope.data, {scope: $scope});
            $scope.errors = null;
            $scope.$apply();
            $('#edit-modal').modal('show');
        });
        // Submit mode mới
        $scope.submit = function() {
            $scope.loading.submit = false;
            // return 0;
            $.ajax({
                type: 'POST',
                url: "/uptek/fixed-assets/" + $scope.formEdit.id + "/update",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                processData: false,
                contentType: false,
                data: $scope.formEdit.submit_data,
                success: function(response) {
                if (response.success) {
                    $('#edit-modal').modal('hide');
                    toastr.success(response.message);
                    if (datatable) datatable.ajax.reload();
                    $scope.errors = null;
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
    $(document).on('click', '.export-button', function(event) {
        event.preventDefault();
        let data = {};
        mergeSearch(data, datatable.context[0]);
        window.location.href = $(this).data('href') + "?" + $.param(data);
    })
</script>
@include('partial.confirm')
@endsection
