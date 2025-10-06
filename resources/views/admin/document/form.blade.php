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
            <label class="form-label required-label">Tên tư liệu</label>
            <input class="form-control " type="text" ng-model="form.title">
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.title[0] %>
                </strong>
            </span>
        </div>

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tên tư liệu (Tiếng Anh)</label>
            <input class="form-control " type="text" ng-model="form.title_eng">
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.title_eng[0] %>
                </strong>
            </span>
        </div>

        <div class="card mb-4">
            <div class="card-header text-center ">
                <h5>Thư viện ảnh</h5>
            </div>
            <div class="card-body">
                <div class="form-group text-center">
                    <label for="">Thư viện ảnh</label>
                    <div class="row gallery-area border">
                        <div class="col-md-4 p-2" ng-repeat="g in form.galleries">
                            <div class="gallery-item">
                                <button class="btn btn-sm btn-danger remove" ng-click="form.removeGallery($index)">
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
                            <label class="gallery-item d-flex align-items-center justify-content-center cursor-pointer" for="gallery-chooser">
                                <i class="fa fa-plus fa-2x text-secondary"></i>
                            </label>
                            <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="gallery-chooser" multiple>
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
    <div class="col-sm-4">
        <div class="card mb-4">
            <div class="card-header text-center">
                <h5>Video Links</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Link Video</label>
                    <div class="video-links">
                        <div class="input-group mb-2" ng-repeat="video in form.videos track by $index">
                            <input type="text" class="form-control" ng-model="video.link" placeholder="Nhập link video">
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="button" ng-click="form.removeVideo()">&minus;</button>
                            </div>
                            <span class="invalid-feedback d-block" role="alert">
                            <strong>
                                <% errors['videos.' + $index + '.link' ][0] %>
                            </strong>
                         </span>
                        </div>

                    </div>
                    <!-- Nút Thêm Mới, khi click gọi hàm thêm mới vào mảng -->
                    <button class="btn btn-primary" type="button" ng-click="form.addVideo()">Thêm mới</button>
                </div>
            </div>
    </div>


    </div>

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="text-right">
            <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                <i ng-if="!loading.submit" class="fa fa-save"></i>
                <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                Lưu
            </button>
            <a href="{{ route('document.index') }}" class="btn btn-danger btn-cons">
                <i class="fa fa-remove"></i> Hủy
            </a>
        </div>
    </div>

</div>

