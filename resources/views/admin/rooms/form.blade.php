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
                        <label class="form-label required-label">Tên phòng</label>
                        <input class="form-control " type="text" ng-model="form.name">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.name[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Diện tích</label>
                        <input class="form-control " type="text" ng-model="form.area">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.area[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Số khách tối đa</label>
                        <input class="form-control " type="text" ng-model="form.maximum_occupancy">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.maximum_occupancy[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">View</label>
                        <input class="form-control " type="text" ng-model="form.view">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.view[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Số phòng ngủ</label>
                        <input class="form-control " type="text" ng-model="form.bedrooms">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.bedrooms[0] %>
                </strong>
            </span>
                    </div>

                    <div>
                        <!-- Danh sách Tab -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">
                                    Mô tả
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="highlight-tab" data-toggle="tab" href="#highlight" role="tab" aria-controls="highlight" aria-selected="true">
                                    Điểm nổi bật
                                </a>
                            </li>
                            {{--                <li class="nav-item">--}}
                            {{--                    <a class="nav-link" id="amenities-tab" data-toggle="tab" href="#amenities" role="tab" aria-controls="amenities" aria-selected="false">--}}
                            {{--                        Tiện nghi--}}
                            {{--                    </a>--}}
                            {{--                </li>--}}
                        </ul>
                        <!-- Nội dung Tab -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active p-3" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <h4>  Mô tả</h4>
                                <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.description"></textarea>
                                <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.description[0] %>
                </strong>
            </span>

                            </div>
                            <div class="tab-pane fade show p-3" id="highlight" role="tabpanel" aria-labelledby="highlight-tab">
                                <h4>Điểm nổi bật (Nhập mỗi điểm nổi bật trên 1 dòng, và nhấn Enter để xuống dòng)</h4>
                                <div class="form-group custom-group mb-4">
                                    <textarea class="form-control" rows="8" ng-model="form.highlight"></textarea>
                                    <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.highlight[0] %>
                </strong>
            </span>
                                </div>

                            </div>
                            {{--                <div class="tab-pane fade p-3" id="amenities" role="tabpanel" aria-labelledby="amenities-tab">--}}
                            {{--                    <h4>Tiện nghi</h4>--}}
                            {{--                    <div class="form-group custom-group mb-4">--}}
                            {{--                        <textarea class="form-control" rows="8" ng-model="form.amenities"></textarea>--}}
                            {{--                        <span class="invalid-feedback d-block" role="alert">--}}
                            {{--                            <strong>--}}
                            {{--                                <% errors.amenities[0] %>--}}
                            {{--                            </strong>--}}
                            {{--                    </span>--}}
                            {{--                    </div>--}}
                            {{--                </div>--}}
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade show p-3" id="en" role="tabpanel" aria-labelledby="en-tab">

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Tên phòng (Tiếng Anh)</label>
                        <input class="form-control " type="text" ng-model="form.name_eng">
                        <span class="invalid-feedback d-block" role="alert">
                        <strong>
                            <% errors.name_eng[0] %>
                        </strong>
                        </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Diện tích (Tiếng Anh)</label>
                        <input class="form-control " type="text" ng-model="form.area_eng">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.area_eng[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Số khách tối đa (Tiếng Anh)</label>
                        <input class="form-control " type="text" ng-model="form.maximum_occupancy_eng">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.maximum_occupancy_eng[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">View (Tiếng Anh)</label>
                        <input class="form-control " type="text" ng-model="form.view_eng">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.view_eng[0] %>
                </strong>
            </span>
                    </div>

                    <div class="form-group custom-group mb-4">
                        <label class="form-label required-label">Số phòng ngủ (Tiếng Anh)</label>
                        <input class="form-control " type="text" ng-model="form.bedrooms_eng">
                        <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.bedrooms_eng[0] %>
                </strong>
            </span>
                    </div>

                    <div>
                        <!-- Danh sách Tab -->
                        <ul class="nav nav-tabs" id="myTab_eng" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="description_eng-tab" data-toggle="tab" href="#description_eng" role="tab" aria-controls="description_eng" aria-selected="true">
                                    Mô tả
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="highlight_eng-tab" data-toggle="tab" href="#highlight_eng" role="tab" aria-controls="highlight_eng" aria-selected="true">
                                    Điểm nổi bật
                                </a>
                            </li>
                            {{--                <li class="nav-item">--}}
                            {{--                    <a class="nav-link" id="amenities-tab" data-toggle="tab" href="#amenities" role="tab" aria-controls="amenities" aria-selected="false">--}}
                            {{--                        Tiện nghi--}}
                            {{--                    </a>--}}
                            {{--                </li>--}}
                        </ul>
                        <!-- Nội dung Tab -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active p-3" id="description_eng" role="tabpanel" aria-labelledby="description_eng-tab">
                                <h4>  Mô tả</h4>
                                <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.description_eng"></textarea>
                                <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.description_eng[0] %>
                </strong>
            </span>

                            </div>
                            <div class="tab-pane fade show p-3" id="highlight_eng" role="tabpanel" aria-labelledby="highlight_eng-tab">
                                <h4>Điểm nổi bật (Nhập mỗi điểm nổi bật trên 1 dòng, và nhấn Enter để xuống dòng)</h4>
                                <div class="form-group custom-group mb-4">
                                    <textarea class="form-control" rows="8" ng-model="form.highlight_eng"></textarea>
                                    <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.highlight_eng[0] %>
                </strong>
            </span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>




    </div>
    <div class="col-sm-4">

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Trạng thái</label>
            <select id="my-select" class="form-control custom-select" ng-model="form.status">
                <option value="">Chọn trạng thái</option>
                <option value="2">Lưu nháp</option>
                <option value="1">Xuất bản</option>
            </select>
            <span class="invalid-feedback d-block" role="alert">
                <strong>
                    <% errors.status[0] %>
                </strong>
            </span>
        </div>

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
<hr>
<div class="text-right">
    <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
    <a href="{{ route('Category.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
