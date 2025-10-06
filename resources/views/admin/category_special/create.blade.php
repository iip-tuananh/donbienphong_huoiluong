<div class="modal fade" id="create-category-special" tabindex="-1" role="dialog" aria-hidden="true"
     ng-controller="CreateCategorySpecial">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Thêm danh mục đặc biệt</h4>
            </div>
            <div class="modal-body">
                @include('admin.category_special.form')
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
    app.controller('CreateCategorySpecial', function ($scope, $http) {
        $scope.form = new CategorySpecial({}, {scope: $scope});
        $scope.loading = {};
        $scope.types = @json(\App\Model\Admin\CategorySpecial::TYPES);
        $scope.show_home_page = [
            {'name': 'hiển thị', 'value': '1'},
            {'name': 'không', 'value': '0'},
        ];

        // Submit Form tạo mới
        $scope.submit = function () {
            let url = "{!! route('category_special.store') !!}";;
            $scope.loading.submit = true;
            // return 0;
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
                        $('#create-category-special').modal('hide');
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
