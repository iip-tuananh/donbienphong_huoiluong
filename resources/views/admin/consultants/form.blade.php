<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên nhân viên</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Giới tính</label>
                    <select class="form-control"  ng-model="form.gender">
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                    </select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.gender[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Chức vụ</label>
                    <input class="form-control " type="text" ng-model="form.role">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.role[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Số điện thoại</label>
                    <input class="form-control " type="text" ng-model="form.phone">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.phone[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Email</label>
                    <input class="form-control " type="text" ng-model="form.email">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.email[0] %></strong>
                    </span>
                </div>
            </div>

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
