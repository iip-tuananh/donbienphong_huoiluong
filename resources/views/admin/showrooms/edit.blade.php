<div class="modal fade" id="edit-banner" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="EditBanner">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Sửa khối showroom</h4>
            </div>
            <div class="modal-body">
                @include('admin.showrooms.form')
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
    app.controller('EditBanner', function ($rootScope, $scope, $http) {
        $rootScope.$on("editBanner", function (event, data){
            $scope.form = new Showroom(data, {scope: $scope});

            $scope.$applyAsync();
            $scope.loading.submit = false;


            $('#edit-banner').modal('show');
        });
        $scope.loading = {};
        // Submit Form sửa
        $scope.submit = function () {

            let url = "/admin/showrooms/" + $scope.form.id + "/update";
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
                        $('#edit-banner').modal('hide');
                        toastr.success(response.message);
                        $scope.form.clearImage()
                        $scope.errors = null;
                        location.reload();

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
