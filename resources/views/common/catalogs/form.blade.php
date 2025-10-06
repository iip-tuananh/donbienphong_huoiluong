<div class="modal-content">
    <div class="modal-header">
        <h4 class="semi-bold">Thêm mới loại vật tư</h4>
    </div>
    <div class="modal-body">
        <div class="form-group custom-group">
            <label class="form-label required-label">Danh mục cấp cha</label>
            <ui-select remove-selected="false" ng-model="form.parent_id" theme="select2">
                <ui-select-match placeholder="Chọn danh mục cha">
                    <% $select.selected.name %>
                </ui-select-match>
                <ui-select-choices repeat="item.id as item in (form.categories | filter: $select.search)">
                    <div>
                        <span>--</span>
                        <span ng-bind="item.name"></span>
                    </div>
                </ui-select-choices>
            </ui-select>
        </div>
        <div class="form-group">
            <label class="form-label">Tên</label>
            <span class="text-danger">(*)</span>
            <input class="form-control" type="text" ng-model="form.name">
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.name[0] %></strong>
            </span>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
            <i ng-if="!loading.submit" class="fa fa-save"></i>
            <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
            Lưu
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
    </div>
</div>