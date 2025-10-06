<div class="modal fade" id="createServiceType" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createServiceType">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Thêm mới loại dịch vụ</h4>
            </div>
            <div class="modal-body">
                <form action="" id="create-service-type-form">
                    <div class="form-group">
                        <label class="form-label">Tên</label>
                        <span class="text-danger">(*)</span>
                        <input class="form-control" type="text" name="name" ng-model="form.name">
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.name[0] %></strong>
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" ng-disabled="loading" ng-click="submit()">
                    <i ng-if="!loading" class="fa fa-save"></i>
                    <i ng-if="loading" class="fa fa-spin fa-spinner"></i>
                    Lưu
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" ng-disabled="loading"><i class="fas fa-window-close"></i> Hủy</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    let createServiceTypeCallback;
    app.controller('createServiceType', function ($scope, $http) {
        $scope.form = {};
        // Submit Form tạo mới
        $(document).on('shown.bs.modal', '#createServiceType', function() {
            document.getElementById("create-service-type-form").reset();
        })
        $scope.submit = function() {
            var url = "{!! route('ServiceType.store') !!}";
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
                        $('#createServiceType').modal('hide');
                        $scope.form = {};
                        toastr.success(response.message);
                        if (createServiceTypeCallback) createServiceTypeCallback(response.data);
                        $scope.errors = null;
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