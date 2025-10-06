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
<style>
    /* Style cho header của mỗi card để click mở/thu gọn */
    .card-header {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .toggle-arrow {
        font-size: 1.2em;
    }
</style>
<div class="row">
    <div class="col-sm-8">
        <button id="add-block" class="btn btn-primary mb-3" ng-click="form.addDetail()">Thêm</button>

        <div class="row mb-3" ng-repeat="d in form.details track by $index">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" data-toggle="collapse" data-target="#block-<% $index + 1 %>" aria-expanded="true">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <span>Điều khoản <% $index + 1 %></span>
                            <div>
                                <span class="toggle-arrow">▲</span>
                                <span class="remove-block ml-2"
                                      ng-click="form.removeDetail()"
                                      style="cursor: pointer;">&minus;</span>
                            </div>
                        </div>


                    </div>
                    <div id="block-<% $index + 1 %>" class="collapse show">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="tabBlock<% $index + 1 %>" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="vi-tab-<% $index + 1 %>" data-toggle="tab" href="#vi-<% $index + 1 %>" role="tab" aria-controls="vi-<% $index + 1 %>" aria-selected="true">Tiếng Việt</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="en-tab-<% $index + 1 %>" data-toggle="tab" href="#en-<% $index + 1 %>" role="tab" aria-controls="en-<% $index + 1 %>" aria-selected="false">Tiếng Anh</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="myTabContent<% $index + 1 %>">
                                <div class="tab-pane fade show active p-3" id="vi-<% $index + 1 %>" role="tabpanel" aria-labelledby="vi-tab-<% $index + 1 %>">
                                    <div class="form-group">
                                        <label class="form-label required-label">Tên tiêu đề</label>
                                        <input class="form-control" type="text" ng-model="d.title" placeholder="Nhập tên tiêu đề">
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>
                                                <% errors['details.' + $index + '.title' ][0] %>
                                            </strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required-label">Nội dung</label>
                                        <textarea class="form-control" rows="5" ng-model="d.content" placeholder="Nhập nội dung"></textarea>
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>
                                                <% errors['details.' + $index + '.content' ][0] %>
                                            </strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="tab-pane fade p-3" id="en-<% $index + 1 %>" role="tabpanel" aria-labelledby="en-tab-<% $index + 1 %>">
                                    <div class="form-group">
                                        <label class="form-label required-label">Tên tiêu đề</label>
                                        <input class="form-control" type="text" ng-model="d.title_eng" placeholder="Enter title">
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>
                                                <% errors['details.' + $index + '.title_eng' ][0] %>
                                            </strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required-label">Nội dung</label>
                                        <textarea class="form-control" rows="5" ng-model="d.content_eng" placeholder="Enter content"></textarea>
                                        <span class="invalid-feedback d-block" role="alert" >
                                            <strong>
                                                <% errors['details.' + $index + '.content_eng' ][0] %>
                                            </strong>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div><!-- /.collapse -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group custom-group">
            <label class="form-label required-label">Nội dung</label>
            <textarea id="editor" class="form-control" ck-editor ng-model="form.content" rows="3"></textarea>
        </div>

        <div class="form-group custom-group required-label">
            <label class="form-label">Nội dung(Tiếng Anh)</label>
            <textarea id="editor" class="form-control" ck-editor ng-model="form.content_eng" rows="3"></textarea>
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
<script>

</script>
