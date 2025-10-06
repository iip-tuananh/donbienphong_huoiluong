<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên loại</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên loại (Tiếng Anh)</label>
                    <input class="form-control " type="text" ng-model="form.name_eng">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name_eng[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Trạng thái</label>
                    <select id="my-select" class="form-control custom-select" ng-model="form.status">
                        <option ng-repeat="s in status" ng-value="s.value" ng-selected="form.status == s.value"><% s.name %></option>

                    </select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.show_home_page[0] %></strong>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
