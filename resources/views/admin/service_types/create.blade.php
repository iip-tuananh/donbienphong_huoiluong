<div class="modal fade" id="create-service-type" tabindex="-1" role="dialog" aria-hidden="true"
     ng-controller="CreateServiceType">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="semi-bold">Thêm loại</h4>
            </div>
            <div class="modal-body">
                @include('admin.service_types.form')
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
    app.controller('CreateServiceType', function ($scope, $http) {
        $scope.form = new ServiceType({}, {scope: $scope});
        $scope.type = @json($type);
        $scope.loading = {};
        $scope.status = [
            {'name': 'hoạt động', 'value': '1'},
            {'name': 'hủy', 'value': '0'},
        ];

        // Submit Form tạo mới
        $scope.submit = function () {
            let url = "{!! route('service_types.store') !!}";;
            $scope.loading.submit = true;
            let data = $scope.form.submit_data;
            data.append('type', $scope.type);

            $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#create-service-type').modal('hide');
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
