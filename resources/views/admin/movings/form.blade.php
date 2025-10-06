<style>
    .gallery-item {
        padding: 5px;
        padding-bottom: 0;
        border-radius: 2px;
        border: 1px solid #bbb;
        min-height: 100px;
        height: 100%;
        position: relative;
    }

    .gallery-item .remove {
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .gallery-item .drag-handle {
        position: absolute;
        top: 5px;
        left: 5px;
        cursor: move;
    }

    .gallery-item:hover {
        background-color: #eee;
    }

    .gallery-item .image-chooser img {
        max-height: 150px;
    }

    .gallery-item .image-chooser:hover {
        border: 1px dashed green;
    }

    .gallery-area {
    }
</style>
<div class="row">
    <div class="col-md-8 col-sm-8 col-xs-12">
        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tiêu đề</label>
            <input class="form-control" ng-model="form.title" type="text">
            <span class="invalid-feedback d-block" role="alert">
				<strong><% errors.title[0] %></strong>
			</span>
        </div>
        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tiêu đề (Tiếng Anh)</label>
            <input class="form-control" ng-model="form.title_eng" type="text">
            <span class="invalid-feedback d-block" role="alert">
				<strong><% errors.title_eng[0] %></strong>
			</span>
        </div>
        <div class="form-group custom-group mb-4">
            <label class="form-label">Nội dung</label>
            <textarea class="form-control ck-editor" ck-editor rows="2" ng-model="form.body"></textarea>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.body[0] %></strong>
            </span>
        </div>
        <div class="form-group">
            <label for="content<% $index + 1 %>">Nội dung (Tiếng Anh)</label>
            <textarea class="form-control ck-editor" ck-editor rows="2" ng-model="form.body_eng"></textarea>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.body_eng[0] %></strong>
            </span>

        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="card mb-4">
            <div class="card-header text-center ">
                <h5>Banner</h5>
            </div>
            <div class="card-body">
                <div class="form-group text-center mb-4">
                    <div class="main-img-preview">
                        <p class="help-block-img">* Ảnh banner định dạng: jpg, png không quá 10MB.</p>
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
				<strong><% errors.image[0] %></strong>
			</span>
                </div>
            </div>
        </div>


    </div>
</div>

<hr>
<div class="text-right">
    <button type="submit" class="btn btn-success btn-cons" ng-click="submit(0)" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
</div>
