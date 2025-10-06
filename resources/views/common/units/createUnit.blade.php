<div class="modal fade" id="createUnit" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createUnit">
    <form method="POST" role="form" id="createUnitForm">
        @csrf
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Thêm mới đơn vị</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tên</label>
                    <span class="text-danger">(*)</span>
                    <input class="form-control" type="text" name="name" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
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
    </form>
</div>
<script>
    let createUnitCallback;
    app.controller('createUnit', function ($scope, $http) {
        $scope.form = {};
        $scope.loading = false
        $(document).on('shown.bs.modal', '#createUnitForm', function() {
            document.getElementById("createUnitForm").reset();
        })
        // Submit Form tạo mới
        $scope.submit = function () {
            var url = "{!! route('Unit.store') !!}";;
            var data = $scope.form;
            $scope.loading = true;
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $('#createUnit').modal('hide');
                        $scope.form = {};
                        toastr.success(response.message);
                        if (createUnitCallback) createUnitCallback(response.data);
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
                    $scope.loading = false;
                    $scope.$applyAsync();
                },
            });
        }
    })

</script>