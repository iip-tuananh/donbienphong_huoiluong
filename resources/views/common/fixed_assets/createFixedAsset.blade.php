<div class="modal fade" id="create-fixed-asset" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createFixedAsset">
    <div class="modal-dialog" style="max-width: 850px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Thêm mới tài sản cố định</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="required-label">Tên tài sản</label>
                                    <input class="form-control" type="text" ng-model="form.name">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.name[0] %></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group custom-group">
                                    <label class="required-label">Thời gian khấu hao</label>
                                    <input class="form-control" type="text" ng-model="form.depreciation_period">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.depreciation_period[0] %></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group custom-group">
                                    <label class="required-label">Đơn vị tính</label>
                                    <select class="form-control" select2 style="width: 100%;" ng-model="form.unit_id">
                                        <option value="">Chọn đơn vị tính</option>
                                        <option ng-repeat="u in units" ng-value="u.id"><% u.name %></option>
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong><% errors.unit_id[0] %></strong>
                                        </span>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group custom-group">
                                    <label class="required-label">Giá nhập định mức</label>
                                    <input class="form-control" type="text" ng-model="form.import_price_quota">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.import_price_quota[0] %></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group custom-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-control" style="width: 100%;" ng-model="form.status">
                                        <option value="">Trạng thái</option>
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
                                    <textarea id="my-textarea" class="form-control" ng-model="form.note" rows="3"></textarea>
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong><% errors.note[0] %></strong>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group text-center">
                            <div class="main-img-preview">
                                <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 2MB.</p>
                                <img class="thumbnail img-preview" ng-src="<% form.image.path %>">
                            </div>
                            <div class="input-group" style="width: 100%; text-align: center">
                                <div class="input-group-btn" style="margin: 0 auto">
                                    <div class="fileUpload fake-shadow cursor-pointer">
                                        <label class="mb-0" for="<% form.image.element_id %>">
                                            <i class="glyphicon glyphicon-upload"></i> Chọn ảnh
                                        </label>
                                        <input class="d-none" id="<% form.image.element_id %>" type="file" class="attachment_upload" accept=".jpg,.jpeg,.png">
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
                <button type="button" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                    <i ng-if="!loading.submit" class="fa fa-save"></i>
                    <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                    Lưu
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    app.controller('createFixedAsset', function ($scope, $http) {
        $scope.form = new FixedAsset({}, {scope: $scope});
        $scope.units = @json(App\Model\Common\Unit::all());
        $scope.loading = {};

        // Submit Form tạo mới
        $scope.submit = function () {
            let url = "{!! route('FixedAsset.store') !!}";;
            $scope.loading.submit = true;
            $.ajax({
                type: "POST",
                url: url,
                data: $scope.form.submit_data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $('#create-fixed-asset').modal('hide');
                        $scope.form = new FixedAsset({}, {scope: $scope});
                        toastr.success(response.message);
                        if (datatable.datatable) datatable.datatable.ajax.reload();
                        $scope.errors = null;
                    } else {
                        $scope.errors = response.errors;
                        toastr.warning(response.message);
                    }
                },
                error: function () {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function () {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                },
            });
        }
    })
</script>