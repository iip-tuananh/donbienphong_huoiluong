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
        <ul class="nav nav-tabs" id="blockTabs" role="tablist">
            <li class="nav-item" ng-repeat="block in form.blocks track by $index">
                <a class="nav-link"
                   ng-class="{'active': $index === 0}"
                   data-toggle="tab"
                   href="#block<% $index + 1 %>"
                   role="tab"
                   aria-controls="block<% $index + 1 %>"
                   aria-selected="<%$index === 0 ? 'true' : 'false'%>">
                    Khối <% $index + 1 %>
                </a>
            </li>

        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade"
                 ng-class="{'show active': $index === 0}"
                 ng-repeat="block in form.blocks track by $index"
                 id="block<% $index + 1 %>"
                 role="tabpanel"
                 aria-labelledby="block-tab-<% $index + 1 %>">

                <!-- Input tiêu đề -->
                <div class="form-group">
                    <label for="title<% $index + 1 %>">Tiêu đề:</label>
                    <input type="text"
                           name="blocks[<% $index + 1 %>][title]"
                           id="title<% $index + 1 %>"
                           class="form-control"
                           placeholder="Nhập tiêu đề"
                           ng-model="block.title">
                </div>

                <div class="form-group">
                    <label for="title<% $index + 1 %>">Tiêu đề (Tiếng Anh):</label>
                    <input type="text"
                           name="blocks[<% $index + 1 %>][title_eng]"
                           id="title<% $index + 1 %>"
                           class="form-control"
                           placeholder="Nhập tiêu đề tiếng anh"
                           ng-model="block.title_eng">
                </div>

                <!-- Input nội dung -->
                <div class="form-group">
                    <label for="content<% $index + 1 %>">Nội dung:</label>
                    <textarea name="blocks[<% $index + 1 %>][body]"  id="content<% $index + 1 %>" class="form-control"
                              ck-editor ng-model="block.body" rows="7"></textarea>

                </div>
                <div class="form-group">
                    <label for="content<% $index + 1 %>">Nội dung (Tiếng Anh):</label>
                    <textarea name="blocks[<% $index + 1 %>][body_eng]"  id="content<% $index + 1 %>" class="form-control"
                              ck-editor ng-model="block.body_eng" rows="7"></textarea>

                </div>
                <!-- Thư viện ảnh -->
                <div class="form-group text-center">
                    <label>Thư viện ảnh</label>
                    <div class="row gallery-area border">
                        <div class="col-md-4 p-2" ng-repeat="g in block.galleries">
                            <div class="gallery-item">
                                <button class="btn btn-sm btn-danger remove" ng-click="block.removeGallery($index)">
                                    <i class="fa fa-times mr-0"></i>
                                </button>
                                <div class="form-group">
                                    <div class="img-chooser" title="Chọn ảnh">
                                        <label for="<% g.image.element_id %>">
                                            <img ng-src="<% g.image.path %>">
                                            <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="<% g.image.element_id %>">
                                        </label>
                                    </div>
                                    <span class="invalid-feedback d-block" role="alert" ng-if="!errors['galleries.' + $index + '.image_obj']">
                                <strong>
                                    <% errors['galleries.' + $index + '.image' ][0] %>
                                </strong>
                            </span>
                                    <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries.' + $index + '.image_obj']">
                                <strong>
                                    <% errors['galleries.' + $index + '.image_obj' ][0] %>
                                </strong>
                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 p-2">
                            <label class="gallery-item d-flex align-items-center justify-content-center cursor-pointer" for="gallery-chooser-<% $index %>">
                                <i class="fa fa-plus fa-2x text-secondary"></i>
                            </label>
                            <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="gallery-chooser-<% $index %>" multiple>
                        </div>
                    </div>
                    <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries']">
                <strong>
                    <% errors.galleries[0] %>
                </strong>
            </span>
                </div>


            </div>
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
