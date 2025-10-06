if(!$scope.form.manufacts) $scope.form.manufacts = {};
if(!$scope.form.types) $scope.form.types = {};
$scope.getVehicleTypes = function(manufact_id, object, selector = null) {
    object.type_id = "";
    if (manufact_id) {
        var url = "/common/vehicle-manufacts/" + manufact_id + "/vehicle-types";
        $scope.data_loading.types = true;
        $.get(url, function(response) {
            if (response.success) {
                $scope.form.types[manufact_id] = response.data;
                console.log($scope.form.types[manufact_id]);
                $scope.data_loading.types = false;
                $scope.$apply();
            }
        });
    }
}
