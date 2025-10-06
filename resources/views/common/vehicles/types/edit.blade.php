<div class="modal fade" id="editVehicleType" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="#" method="POST" role="form" id="editVehicleTypeForm">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Sửa loại xe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group custom-group">
                        <label class="form-label required-label">Dòng xe</label>
                        <div class="input-group mb-3">
                            <ui-select remove-selected="false" ng-model="form.vehicle_category_id" ng-disabled="form.disabled_manufact" theme="select2">
                                <ui-select-match placeholder="Dòng xe">
                                    <% $select.selected.name %>
                                </ui-select-match>
                                <ui-select-choices repeat="item.id as item in (form.categories | filter: $select.search)" ng-selected="form.vehicle_category_id == item.id">
                                    <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.vehicle_category_id[0] %></strong>
                        </span>
                    </div>

                    <div class="form-group custom-group">
                        <label class="form-label required-label">Tên hãng</label>
                        <div class="input-group mb-3">
                            <ui-select remove-selected="false" ng-model="form.vehicle_manufact_id" ng-disabled="form.disabled_manufact" theme="select2">
                                <ui-select-match placeholder="Hãng xe">
                                    <% $select.selected.name %>
                                </ui-select-match>
                                <ui-select-choices repeat="item.id as item in (form.manufacts | filter: $select.search)" ng-selected="form.vehicle_manufact_id == item.id">
                                    <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.vehicle_manufact_id[0] %></strong>
                        </span>
                    </div>

                    <div class="form-group custom-group">
                        <label class="form-labe required-label">Tên loại</label>
                        <input class="form-control" type="text" name="name" ng-model="form.name">
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.name[0] %></strong>
                        </span>
                    </div>

                    <div class="form-group custom-group">
                        <label class="form-label required-label">Trạng thái</label>
                        <select class="form-control custom-select" name="status" ng-model="form.status">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.status[0] %></strong>
                        </span>
                    </div>
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
    </form>
</div>