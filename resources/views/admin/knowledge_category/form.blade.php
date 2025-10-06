<div class="row">
    <div class="col-sm-2"></div>

    <div class="col-sm-8">
        <div class="form-group custom-group mb-4">
            <label class="form-label">Danh mục cấp cha</label>
            <ui-select class="" remove-selected="true" ng-model="form.parent_id" theme="select2">
                <ui-select-match placeholder="Chọn danh mục">
                    <% $select.selected.name %>

                    <span class="span-right" ng-if="form.parent_id != 0">
                        <a class="del-button remove-category"><i class="fa fa-times"></i></a>
                   </span>
                </ui-select-match>

                <ui-select-choices repeat="t.id as t in (form.all_categories | filter: $select.search)">
                    <span ng-bind="t.name"></span>

                    <span class="span-right" ng-if="t.id == form.parent_id">
                         <a class="del-button remove-category"><i class="fa fa-times"></i></a>
                    </span>
                </ui-select-choices>

            </ui-select>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.parent_id[0] %></strong>
            </span>
        </div>

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tên danh mục</label>
            <input class="form-control " type="text" ng-model="form.name">
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.name[0] %></strong>
            </span>
        </div>

        <div class="form-group custom-group mb-4">
            <label class="form-label">Mô tả danh mục</label>
            <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.intro"></textarea>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.intro[0] %></strong>
            </span>
        </div>
    </div>

    <div class="col-sm-2"></div>
</div>
<hr>
<div class="text-right">
    <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
    <a href="{{ route('Category.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
