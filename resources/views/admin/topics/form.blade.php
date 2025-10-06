<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tiêu đề</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tiêu đề (Tiếng Anh)</label>
                    <input class="form-control " type="text" ng-model="form.name_eng">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name_eng[0] %></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label">Nội dung</label>
                    <textarea id="editor" class="form-control" ck-editor ng-model="form.description" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label">Nội dung (Tiếng Anh)</label>
                    <textarea id="editor" class="form-control" ck-editor ng-model="form.description_eng" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
