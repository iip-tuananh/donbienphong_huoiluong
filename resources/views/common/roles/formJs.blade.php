let permissions = @json(\App\Model\Common\Permission::getAll());
$scope.loading = {};
$scope.toggle = true;
$scope.types = USER_TYPES;

$scope.getActive = function(group) {
    return group.filter((val) => $scope.form.permissions.includes(val.id)).length;
}

$scope.toggleSelected = function(id) {
    var idx = $scope.form.permissions.indexOf(id);
    if (idx > -1) $scope.form.permissions.splice(idx, 1);
    else $scope.form.permissions.push(id);
}

$scope.toggleCollapse = function() {
    if ($scope.toggle) $('.collapse').collapse('hide');
    else $('.collapse').collapse('show');
    $scope.toggle = !$scope.toggle;
}

function calculateLayout() {
    if (window.innerWidth >= 992) $scope.col = 4;
    else if (window.innerWidth >= 768) $scope.col = 6;
    else $scope.col = 12;

    var numCol = 12 / $scope.col;
    var amount = Math.ceil($scope.getGroups.length / numCol);
    var result = [];
    for (var i = 0; i < numCol; i++) {
        result.push($scope.getGroups.slice(amount * i, amount * (i + 1)));
    }
    $scope.groups = result;

    $scope.$applyAsync();
}

$scope.changeType = function() {
    $scope.form.permissions = [];
    $scope.groupRoles = permissions.reduce(function(acc, cur) {
        if (!acc[cur.group]) acc[cur.group] = [];
        acc[cur.group].push(cur);

        return acc;
    }, {})

    $scope.getGroups = Object.keys($scope.groupRoles);

    calculateLayout();
}

$scope.changeType();

window.onresize = function() {
    calculateLayout();
}
