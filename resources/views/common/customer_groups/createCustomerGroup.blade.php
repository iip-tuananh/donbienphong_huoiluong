<div class="modal fade" id="createCustomerGroup" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createCustomerGroup">
    <form method="POST" role="form" id="createCustomerGroupForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới nhóm khách hàng</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tên nhóm</label>
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
    let createCustomerGroupCallback;
    app.controller('createCustomerGroup', function ($scope, $rootScope, $http) {
        $('#createCustomerGroupForm').submit(function(e) {
            e.preventDefault();
            var url = "{!! route('CustomerGroup.store') !!}";
            var data = $('#createCustomerGroupForm').serialize();
            $scope.loading = true;
            $scope.$apply();
            $.post(url, data, function(response) {
                if (response.success) {
                    $('#createCustomerGroup').modal('hide');
                    document.getElementById('createCustomerGroupForm').reset();
                    toastr.success(response.message);
                    if(createCustomerGroupCallback) createCustomerGroupCallback(response.data);
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