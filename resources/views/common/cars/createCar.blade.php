<div class="modal fade" id="createCar" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createCar">
    <form action="" id="create-car-form">
        <div class="modal-dialog" style="max-width: 850px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới xe</h4>
                </div>
                <div class="modal-body">
                    @include('common.cars.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" ng-disabled="loading.car" ng-click="submit()">
                        <i ng-if="!loading.car" class="fa fa-save"></i>
                        <i ng-if="loading.car" class="fa fa-spin fa-spinner"></i>
                        Lưu
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" ng-disabled="loading.car"><i class="fas fa-window-close"></i> Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </form>
</div>
@include('common.vehicles.categories.createVehicleCategory')
@include('common.vehicles.manufacts.createVehicleManufact')
@include('common.vehicles.types.createVehicleType')
@include('partial.classes.common.Car')
<script>

    let createCarCallback;
    app.controller('createCar', function ($scope, $rootScope, $http) {
        $scope.form = new Car({}, {scope: $scope});
        $scope.data_loading = {};
        $scope.loading = {};

        $(document).on('shown.bs.modal', '#createCar', function() {
            document.getElementById("create-car-form").reset();
        })

        createVehicleCategory = function(response) {
            $scope.form.categories.push(response);
            $scope.form.category = response.id;
        }

        createVehicleManufact = function(response) {
            $scope.form.manufacts.push(response);
            $scope.form.manufact_id = response.id;
        }

        createVehicleType = function(response) {
            $scope.form.types[$scope.form.manufact_id].push(response);
            $scope.form.type_id = response.id;
        }

        createLicensePlateCallback = function(response) {
            $scope.form.license_plates.push(response);
            $scope.form.license_plate_id = response.id;
            $scope.$applyAsync();
        }

        $rootScope.$on("addCustomer", function (event, data){
           $scope.form.customers.push(data);
           if (!$scope.form.customer_ids) $scope.form.customer_ids = [];
           $scope.form.customer_ids.push(data.id);
           $scope.$applyAsync();
        });

        $rootScope.$on("addLicensePlate", function (event, data) {
           $scope.form.license_plates.push(data);
           $scope.form.license_plate_id = data.id;
           $scope.$applyAsync();
        });

        // Thêm nhanh loại xe
        $('#quick-type').on('click', function () {
            let data = $scope.form.manufact_id;
            $rootScope.$emit("quickVehicleType",  data);
            $("#createVehicleType").modal('show');
        });

        // Xử lý Hãng >> loại xe
        @include('partial.modal.vehicleJs')

        $(document).on('shown.bs.modal', '#createCar', function() {
            $scope.form = new Car({}, {scope: $scope});
            $scope.form.license_plate_id = "";
            $(this).find('select').trigger('change');
        })

        // Submit Form tạo mới
        $scope.submit = function() {
            var url = "{!! route('Car.store') !!}";
            var data = $scope.form.submit_data;
            $scope.loading.car = true;
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function(response) {
                    if (response.success) {
                        $('#createCar').modal('hide');
                        $scope.form = new Car({}, {scope: $scope});
                        toastr.success(response.message);
                        if (createCarCallback) createCarCallback(response.data);
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
                    $scope.loading.car = false;
                    $scope.$applyAsync();
                },
            });
        }
    })
</script>