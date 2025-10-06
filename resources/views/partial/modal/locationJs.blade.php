// Xử lý tỉnh >> Huyện >> Xã
if(!$scope.provinces) $scope.provinces = {};
if(!$scope.districts) $scope.districts = {};
if(!$scope.wards) $scope.wards = {};
$scope.data_loading = {};
$scope.getDistricts = function(province, object, selector = null) {
    object.district_id = "";
    object.ward_id = "";

    if (province && !$scope.districts[province]) {

        var url = "/locations/" + province + "/districts";
        object.url = url;
        $scope.data_loading.districts = true;
        $.get(url, function(response) {
            if (response.success) {
                $scope.districts[province] = response.data;
                $scope.data_loading.districts = false;
                $scope.$apply();
                reinitSelect2(selector);
            }
        });
    }
}

$scope.getWards = function(district, object, selector = null) {
    object.ward_id = "";
    if (district && !$scope.wards[district]) {
        var url = "/locations/" + district + "/wards";
        $scope.data_loading.wards = true;
        $.get(url, function(response) {
            if (response.success) {
                $scope.wards[district] = response.data;
                $scope.data_loading.wards = false;
                $scope.$apply();
                reinitSelect2(selector);
            }
        });
    }
}