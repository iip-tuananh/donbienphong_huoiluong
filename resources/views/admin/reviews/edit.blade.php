<div class="modal fade" id="edit-review" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="EditReview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Sửa đánh giá</h4>
            </div>
            <div class="modal-body">
                @include('admin.reviews.form')
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
           $scope.form = new Review(data, {scope: $scope});
           $scope.$applyAsync();
           $scope.loading.submit = false;
           $('#edit-review').modal('show');
        });

        @include('admin.reviews.formJs');

        // Submit Form sửa
        $scope.submit = function () {
            let url = "/admin/reviews/" + $scope.form.id + "/update";
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
                        $scope.form = new Review({}, {scope: $scope});
                        toastr.success(response.message);
                        if (createReviewCallback) createReviewCallback(response.data);
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
