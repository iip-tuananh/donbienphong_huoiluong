<div class="modal fade" id="edit-origin" tabindex="-1" role="dialog" aria-hidden="true"
     ng-controller="EditOrigin">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Cập nhật xuất xứ</h4>
            </div>
            <div class="modal-body">
                @include('admin.origins.form')
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
    app.controller('EditOrigin', function ($rootScope, $scope, $http) {
        $rootScope.$on("editOrigin", function (event, data){
            $scope.form = new Origin(data, {scope: $scope});
            $scope.$applyAsync();
            $scope.loading.submit = false;
            $('#edit-origin').modal('show');
        });
        $scope.loading = {};
        // Submit Form sửa
        $scope.submit = function () {
            let url = "/admin/origins/" + $scope.form.id + "/update";
            $scope.loading.submit = true;
            $.ajax({
                type: "POST",
                url: url,
                data: $scope.form.submit_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $('#edit-origin').modal('hide');
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
                    $scope.$applyAsync();
                },
            });
        }
    })
</script>
