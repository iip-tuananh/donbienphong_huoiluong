<div class="modal fade" id="createVehicleType" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createVehicleType">
    <form method="POST" role="form" id="createVehicleTypeForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới loại xe</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group custom-group">
                        <label class="form-label required-label">Dòng xe</label>
                        <div class="input-group mb-3">
                            <ui-select remove-selected="false" ng-model="form.vehicle_category_id" ng-disabled="form.disabled_manufact" theme="select2">
                                <ui-select-match placeholder="Dòng xe">
                                    <% $select.selected.name %>
                                </ui-select-match>
                                <ui-select-choices repeat="item.id as item in (form.categories | filter: $select.search)" ng-selected="form.vehicle_category_id == item.id">
                                    <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.vehicle_category_id[0] %></strong>
                        </span>
                    </div>

                    <div class="form-group custom-group">
                        <label class="form-label required-label">Hãng xe</label>
                        <div class="input-group mb-3">
                            <ui-select remove-selected="false" ng-model="form.vehicle_manufact_id" ng-disabled="form.disabled_manufact" theme="select2">
                                <ui-select-match placeholder="Hãng xe">
                                    <% $select.selected.name %>
                                </ui-select-match>
                                <ui-select-choices repeat="item.id as item in (form.manufacts | filter: $select.search)" ng-selected="form.vehicle_manufact_id == item.id">
                                    <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.vehicle_manufact_id[0] %></strong>
                        </span>
                    </div>
                    <div class="form-group custom-group">
                        <label class="form-label required-label">Tên loại xe</label>
                        <input class="form-control" type="text" name="name" ng-model="form.name">
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.name[0] %></strong>
                        </span>
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
    </form>
    <!-- /.modal-dialog -->
</div>
@include('partial.classes.common.VehicleType')
<script>
    let createVehicleType;
    app.controller('createVehicleType', function ($scope, $rootScope, $http) {
        $scope.form = new VehicleType({}, {scope: $scope});
        $scope.loading = {};

        $rootScope.$on("quickVehicleType", function (event, data){
           $scope.form.manufact_id = data;
           $scope.form.disabled_manufact = 1;
           $scope.$applyAsync();
        });

        $(document).on('shown.bs.modal', '#createVehicleType', function() {
            document.getElementById("createVehicleTypeForm").reset();
        })

        $scope.submit = function () {
            var url = "{!! route('VehicleType.store') !!}";
            let data = $scope.form.submit_data;
            $scope.loading.submit = true;
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $scope.form = new VehicleType({}, {scope: $scope});
                        toastr.success(response.message);
                        if (createVehicleType) createVehicleType(response.data);
                        $('#createVehicleType').modal('hide');
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