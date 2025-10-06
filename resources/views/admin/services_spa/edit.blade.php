<div class="modal fade" id="edit-service" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="EditService">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Cập nhật dịch vụ</h4>
            </div>
            <div class="modal-body">
                @include('admin.services_spa.form')
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
    <!-- /.modal-dialog -->
</div>
<script>
    app.controller('EditService', function ($rootScope, $scope, $http) {
        $rootScope.$on("editServiceSpa", function (event, data){
            $scope.form = new ServiceSpa(data, {scope: $scope});

            $scope.$applyAsync();
            $scope.loading.submit = false;

            $('#edit-service').modal('show');
        });
        $scope.loading = {};
        // Submit Form sửa
        $scope.submit = function () {

            let url = "/admin/services-spa/" + $scope.form.id + "/update";
            $scope.loading.submit = true;

            $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.form.submit_data,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#edit-service').modal('hide');
                        toastr.success(response.message);
                        datatable.ajax.reload();
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
                    $scope.loading.submit = false;
                    $scope.form.clearImage();
                    $scope.$applyAsync();
                },
            });
        }
    })
</script>
