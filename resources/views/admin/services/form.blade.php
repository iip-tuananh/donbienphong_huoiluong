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
</style>
<div class="row">
    <div class="col-sm-8">
{{--        <div class="form-group mb-3">--}}
{{--            <label class="form-label required-label">Danh mục</label>--}}
{{--            <ui-select remove-selected="true" ng-model="form.cate_id" theme="select2" ng-change="changeCategory(form.cate_id)">--}}
{{--                <ui-select-match placeholder="Chọn danh mục"><% $select.selected.name %></ui-select-match>--}}
{{--                <ui-select-choices repeat="t.id as t in (form.all_categories | filter: $select.search)">--}}
{{--                    <span ng-bind="t.name"></span>--}}
{{--                </ui-select-choices>--}}
{{--            </ui-select>--}}
{{--            <span class="invalid-feedback d-block" role="alert">--}}
{{--                <strong>--}}
{{--                    <% errors.cate_id[0] %>--}}
{{--                </strong>--}}
{{--            </span>--}}
{{--        </div>--}}

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tên tiêu đề</label>
            <input class="form-control " type="text" ng-model="form.name">
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.name[0] %>
                </strong>
            </span>
        </div>

        <div class="form-group custom-group mb-4">
            <label class="form-label">Mô tả ngắn</label>
            <textarea id="my-textarea" class="form-control" rows="5" ng-model="form.description"></textarea>
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.description[0] %>
                </strong>
            </span>
        </div>

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Nội dung</label>
            <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.content"></textarea>
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.content[0] %>
                </strong>
            </span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Trạng thái</label>
            <select id="my-select" class="form-control custom-select" ng-model="form.status">
                <option value="">Chọn trạng thái</option>
                <option value="0">Lưu nháp</option>
                <option value="1">Xuất bản</option>
            </select>
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.status[0] %>
                </strong>
            </span>
        </div>




        <div class="card mb-4">
            <div class="card-header text-center ">
                <h5>Ảnh mô tả</h5>
            </div>
            <div class="card-body">
                <!-- Ảnh đại diện -->
                <div class="form-group text-center">
                    <div class="main-img-preview">
                        <label class="required-label">Ảnh đại diện</label>
                        <p class="help-block-img">* Ảnh định dạng: jpg, png</p>
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

                <hr>

                <div class="form-group text-center">
                    <div class="main-img-preview">
                        <label for="">Label (64x64)</label>
                        <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 2MB.</p>
                        <img class="thumbnail img-preview" ng-src="<% form.image_label.path %>">
                    </div>
                    <div class="input-group" style="width: 100%; text-align: center">
                        <div class="input-group-btn" style="margin: 0 auto">
                            <div class="fileUpload fake-shadow cursor-pointer">
                                <label class="mb-0" for="<% form.image_label.element_id %>">
                                    <i class="glyphicon glyphicon-upload"></i> Chọn ảnh
                                </label>
                                <input class="d-none" id="<% form.image_label.element_id %>" type="file" class="attachment_upload" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.image_label[0] %>
                </strong>
            </span>
                </div>

            </div>
        </div>
    </div>
</div>
<hr>
<div class="text-right">
    <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
    <a href="{{ route('services.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
