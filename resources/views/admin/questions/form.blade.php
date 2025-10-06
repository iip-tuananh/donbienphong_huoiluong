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
        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Danh mục</label>
            <ui-select class="" remove-selected="true" ng-model="form.topic_id" theme="select2">
                <ui-select-match placeholder="Chọn chủ đề">
                    <% $select.selected.name %>
                </ui-select-match>
                <ui-select-choices repeat="t.id as t in (topics | filter: $select.search)">
                    <span ng-bind="t.name"></span>
                </ui-select-choices>
            </ui-select>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.topic_id[0] %></strong>
            </span>
        </div>

        <div>
            <ul class="nav nav-tabs" id="myTab1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="vi-tab" data-toggle="tab" href="#vi" role="tab" aria-controls="vi" aria-selected="true">
                        Tiếng Việt
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="en-tab" data-toggle="tab" href="#en" role="tab" aria-controls="en" aria-selected="true">
                        Tiếng Anh
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent1">
                <div class="tab-pane fade show active p-3" id="vi" role="tabpanel" aria-labelledby="vi-tab">
                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Tên tiêu đề</label>
                        <input class="form-control " type="text" ng-model="form.title">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.title[0] %>
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

                <div class="tab-pane fade show p-3" id="en" role="tabpanel" aria-labelledby="en-tab">
                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Tên tiêu đề</label>
                        <input class="form-control " type="text" ng-model="form.title_eng">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.title_eng[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Nội dung</label>
                        <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.content_eng"></textarea>
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.content_eng[0] %>
                </strong>
            </span>
                    </div>
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
    <a href="{{ route('questions.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
