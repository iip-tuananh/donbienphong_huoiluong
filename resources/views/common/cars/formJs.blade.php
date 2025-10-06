$scope.loading = {};

$scope.getCarTypes = function(manufact_id) {
    var url = "/common/vehicle-manufacts/" + manufact_id + "/vehicle-types"
    $scope.loading.types = true;
    $.get(url, function(response) {
        if (response.success) {
            $scope.form.types[manufact_id] = response.data;
            $scope.loading.types = false;
            $scope.$apply();
        }
    });
};