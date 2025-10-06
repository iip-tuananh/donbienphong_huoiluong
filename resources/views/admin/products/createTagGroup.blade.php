<div class="modal fade" id="createTag" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createCustomerGroup">
    <form method="POST" role="form" id="createCustomerGroupForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm thẻ tag</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Mã thẻ</label>
                        <span class="text-danger">(*)</span>
                        <input class="form-control" type="text" name="code" ng-class="{'is-invalid': errors && errors.code}">
                        <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.code">
                                <strong><% errors.code[0] %></strong>
                            </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên thẻ</label>
                        <span class="text-danger">(*)</span>
                        <input class="form-control" type="text" name="name" ng-class="{'is-invalid': errors && errors.name}">
                        <input type="hidden" name="type" value="10">
                        <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.name">
                                <strong><% errors.name[0] %></strong>
                            </span>
                    </div>


					<div class="border-checkbox-section">

					</div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" ng-disabled="loading"><i class="fa fa-save"></i> Lưu</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" ng-disabled="loading"><i class="fa fa-remove"></i>   Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>
<script>
    app.controller('createCustomerGroup', function ($scope, $http) {
        $('#createCustomerGroupForm').submit(function(e) {
            e.preventDefault();
            var url = "{!! route('tags.store') !!}";
            var data = $('#createCustomerGroupForm').serialize();
            $scope.loading = true;
            $scope.$apply();
            $.post(url, data, function(response) {
                if (response.success) {
                    $('#createTag').modal('hide');
                    toastr.success(response.message);
                    $scope.errors = null;
                    $('select[name="tag_group_id[]"]').append('<option value="' + response.data.id + '">' + response.data.name + '</option>');
                    document.getElementById("createCustomerGroupForm").reset();
                    $('select[name="tag_group_id[]"] option:last').attr("selected", "selected");
                    $('select[name="tag_group_id[]"]').trigger('change');
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
