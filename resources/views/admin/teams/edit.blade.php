<div class="modal fade" id="edit-review" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="EditReview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Sửa đội ngũ sáng lập</h4>
            </div>
            <div class="modal-body">
                @include('admin.teams.form')
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
    app.controller('EditReview', function ($rootScope, $scope, $http) {
        $rootScope.$on("editReview", function (event, data){
           $scope.form = new Team(data, {scope: $scope});
           $scope.$applyAsync();
           $scope.loading.submit = false;
           $('#edit-review').modal('show');
        });

        @include('admin.teams.formJs');

        // Submit Form sửa
        $scope.submit = function () {
            let url = "/admin/teams/" + $scope.form.id + "/update";
            $scope.loading.submit = true;
            $.ajax({
                type: "POST",
                url: url,
                data: $scope.form.submit_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#edit-review').modal('hide');
                        $scope.form = new Team({}, {scope: $scope});
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
