<div class="modal fade" id="edit-product-rate" tabindex="-1" role="dialog" aria-hidden="true"
     ng-controller="EditProductRate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Xem chi tiết đánh giá</h4>
            </div>
            <div class="modal-body">
                @include('admin.product_rates.form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-cons" ng-click="submit(0)" ng-disabled="loading.submit">
                    <i ng-if="!loading.submit" class="fa fa-times"></i>
                    <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                    Không duyệt
                </button>
                <button type="button" class="btn btn-success btn-cons" ng-click="submit(2)" ng-disabled="loading.submit">
                    <i ng-if="!loading.submit" class="fa fa-check"></i>
                    <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                    Duyệt và Xuất bản
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    app.controller('EditProductRate', function ($rootScope, $scope, $http) {
        $rootScope.$on("editProductRate", function (event, data){
            $scope.form = data;
            $scope.$applyAsync();
            $scope.loading.submit = false;
            $scope.statues = [
                {'name': 'hiển thị', 'value': '1'},
                {'name': 'không', 'value': '0'},
            ];

            $('#edit-product-rate').modal('show');
        });
        $scope.loading = {};
        // Submit Form sửa
        $scope.submit = function (status) {
            let url = "{{route('product_rates.update.status')}}";
            $scope.loading.submit = true;
            $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {
                    product_rate_id: $scope.form.id,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        $('#edit-product-rate').modal('hide');
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
