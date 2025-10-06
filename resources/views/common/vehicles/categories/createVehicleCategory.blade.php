<div class="modal fade" id="createVehicleCategory" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createVehicleCategory">
    <form method="POST" role="form" id="createVehicleCategoryForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới dòng xe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tên dòng xe</label>
                        <span class="text-danger">(*)</span>
                        <input class="form-control" type="text" name="name">
                        <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.name">
                            <strong><% errors.name[0] %></strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" ng-disabled="loading"><i class="fa fa-save"></i> Thêm mới</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" ng-disabled="loading"><i class="fas fa-window-close"></i> Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>
<script>
    let createVehicleCategory;
    app.controller('createVehicleCategory', function ($scope, $http) {
        $('#createVehicleCategoryForm').submit(function(e) {
            e.preventDefault();
            var url = "{!! route('VehicleCategory.store') !!}";
            var data = $('#createVehicleCategoryForm').serialize();
            $scope.loading = true;
            $scope.$apply();
            $.post(url, data, function(response) {
                if (response.success) {
                    $('#createVehicleCategory').modal('hide');
                    document.getElementById('createVehicleCategoryForm').reset();
                    toastr.success(response.message);
                    if(createVehicleCategory) createVehicleCategory(response.data);
                    $scope.errors = null;
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