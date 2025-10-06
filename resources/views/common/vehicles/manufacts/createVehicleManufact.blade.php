<div class="modal fade" id="createVehicleManufact" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createVehicleManufact">
    <form method="POST" role="form" id="createVehicleManufactForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới hãng xe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tên hãng xe</label>
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
    let createVehicleManufact;
    app.controller('createVehicleManufact', function ($scope, $http) {
        $('#createVehicleManufactForm').submit(function(e) {
            e.preventDefault();
            var url = "{!! route('VehicleManufact.store') !!}";
            var data = $('#createVehicleManufactForm').serialize();
            $scope.loading = true;
            $scope.$apply();
            $.post(url, data, function(response) {
                if (response.success) {
                    $('#createVehicleManufact').modal('hide');
                    document.getElementById('createVehicleManufactForm').reset();
                    toastr.success(response.message);
                    if (createVehicleManufact) createVehicleManufact(response.data);
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