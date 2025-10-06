<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Mã</label>
                    <input class="form-control " type="text" ng-model="form.code">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.code[0] %></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên hãng</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>

{{--            <div class="col-md-12 col-sm-12 col-xs-12">--}}
{{--                <div class="form-group custom-group">--}}
{{--                    <label class="form-label">Danh mục</label>--}}
{{--                    <select class="form-control" ng-model="form.category_id">--}}
{{--                        <option value="">Chọn danh mục</option>--}}
{{--                        <option ng-repeat="c in categories" ng-value="c.id" ng-selected="c.id == form.category_id">--}}
{{--                            <% c.name %>--}}
{{--                        </option>--}}
{{--                    </select>--}}
{{--                    <span class="invalid-feedback d-block" role="alert">--}}
{{--                <strong><% errors.category_id[0] %></strong>--}}
{{--                         </span>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="col-md-12 col-sm-12 col-xs-12">
                <label class="form-label">Ảnh đại diện</label>
                <div class="main-img-preview">
                    <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 2MB.</p>
                    <img class="thumbnail img-preview" ng-src="<% form.image.path %>">
                </div>
                <div class="input-group" style="width: 100%; text-align: center">
                    <div class="input-group-btn" style="margin: 0 auto">
                        <div class="fileUpload fake-shadow cursor-pointer">
                            <label class="mb-0" for="<% form.image.element_id %>">
                                <i class="glyphicon glyphicon-upload"></i> Chọn ảnh
                            </label>
                            <input class="d-none" id="<% form.image.element_id %>" type="file" class="attachment_upload" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <span class="invalid-feedback d-block" role="alert">

            </span>
            </div>

        </div>
    </div>
</div>
