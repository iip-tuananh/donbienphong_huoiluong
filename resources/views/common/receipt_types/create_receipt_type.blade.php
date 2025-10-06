<div class="modal fade" id="create-license-plate" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="CreateLicensePlate">
    <form method="POST" role="form" id="license-plate-form">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới biển số xe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Biển số</label>
                        <span class="text-danger">(*)</span>
                        <input class="form-control" type="text" ng-model="form.license_plate">
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.license_plate[0] %></strong>
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
    <!-- /.modal-dialog -->
</div>
<script>
    app.controller('CreateLicensePlate', function ($scope, $http) {
        $scope.form = {};
        $scope.data_loading = {};
        // Submit Form tạo mới
        $scope.submit = function() {
            var url = "{!! route('LicensePlate.store') !!}";
            var data = $scope.form;
            $scope.loading = true;
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function(response) {
                    if (response.success) {
                        $('#create-license-plate').modal('hide');
                        $('select[name="license_plate_id"]').append('<option value="' + response.data.id + '">' + response.data.license_plate + '</option>');
                        $scope.form = {};
                        toastr.success(response.message);
                        if (datatable.datatable) datatable.datatable.ajax.reload();
                        $scope.errors = null;
                        document.getElementById("license-plate-form").reset();
                        $('select[name="license_plate_id"]').val(response.data.id).trigger('change');
                    } else {
                        $scope.errors = response.errors;
                        toastr.warning(response.message);
                    }
                },
                error: function() {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.loading = false;
                    $scope.$applyAsync();
                },
            });
        }
    })
</script>