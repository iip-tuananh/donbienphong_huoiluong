<style>
    /* Card wrapper cho mỗi section để nổi khối */
    .custom-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .custom-card .form-label {
        font-weight: 600;
    }

    .custom-card .required-label::after {
        content: "*";
        color: #e74c3c;
        margin-left: 4px;
    }

    /* Kết quả đạt được */
    .result-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    .result-item input {
        flex: 1;
        margin-right: 0.5rem;
    }
    .result-item .btn {
        min-width: 36px;
    }

    /* Thumbnail preview */
    .thumb-wrapper {
        text-align: center;
    }
    .thumb-wrapper img {
        max-width: 100%;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        border: 1px solid #ccc;
    }
    .thumb-wrapper .btn-upload {
        width: 100%;
    }

    .result-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem; /* Khoảng cách giữa các phần tử */
    }

    .field-wrapper {
        width: 100%;
    }

    .action-buttons {
        /* Đẩy các nút về bên phải */
    }

    .action-buttons .btn {
        min-width: 40px;
    }

</style>

<div class="row">
{{--    <div class="col-lg-8">--}}
{{--        <div class="custom-card">--}}
{{--            <h5 class="mb-4">Thông tin chung</h5>--}}
{{--            <div class="form-group">--}}
{{--                <label class="form-label">Nội dung</label>--}}
{{--                <div ng-repeat="(idx, item) in form.detail" class="result-item">--}}
{{--                    <input type="text"--}}
{{--                           class="form-control"--}}
{{--                           placeholder="Nhập kết quả"--}}
{{--                           ng-model="item.title" />--}}


{{--                    <textarea id="editor" class="form-control" ck-editor ng-model="item.intro" rows="5"></textarea>--}}



{{--                    <button type="button"--}}
{{--                            class="btn btn-success"--}}
{{--                            ng-click="form.addResult()">--}}
{{--                        <i class="fa fa-plus"></i>--}}
{{--                    </button> &nbsp;--}}
{{--                    <button type="button" ng-if="idx"--}}
{{--                            class="btn btn-danger"--}}
{{--                            ng-click="form.removeResult(idx)">--}}
{{--                        <i class="fa fa-times"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="invalid-feedback d-block"><% errors.results %></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="col-lg-8">
        <div class="custom-card">
            <h5 class="mb-4">Thông tin chung</h5>
            <div class="form-group">
                <label class="form-label">Nội dung</label>
                <div
                    ng-repeat="(idx, item) in form.detail"
                    class="result-item p-3 mb-4 border rounded"
                >
                    <!-- Wrapper cho input -->
                    <div class="field-wrapper mb-3">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Nhập tiêu đề"
                            ng-model="item.title"
                        />
                    </div>

                    <!-- Wrapper cho textarea -->
                    <div class="field-wrapper mb-3">
          <textarea
              id="editor"
              class="form-control"
              ck-editor
              ng-model="item.intro"
              rows="5"
          ></textarea>
                    </div>

                    <!-- Nút hành động -->
                    <div class="action-buttons text-end">
                        <button
                            type="button"
                            class="btn btn-success me-2"
                            ng-click="form.addResult()"
                        >
                            <i class="fa fa-plus"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            ng-if="idx"
                            ng-click="form.removeResult(idx)"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="invalid-feedback d-block"><% errors.results %></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header text-center ">
                <h5>Ảnh mô tả</h5>
            </div>
            <div class="card-body">
                <!-- Ảnh đại diện -->
                <div class="form-group text-center">
                    <div class="main-img-preview">
                        <label class="required-label">Ảnh mô tả</label>
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
            </div>
        </div>

    </div>
</div>

<div class="text-right mt-3">
    <button type="submit" class="btn btn-success px-4" ng-click="submit(0)" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
</div>
