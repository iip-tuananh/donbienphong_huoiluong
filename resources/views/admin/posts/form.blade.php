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


    /* Scoped: chỉ áp dụng trong khối .hot-post-field */
    .hot-post-field .hot-card{
        display:flex; align-items:center; justify-content:space-between;
        gap:16px; padding:14px 16px;
        border:1px solid #eceff3; border-radius:12px;
        background:linear-gradient(180deg,#fff, #f9fafb);
        box-shadow:0 1px 0 rgba(16,24,40,.02);
        transition:transform .18s ease, box-shadow .18s ease;
    }
    .hot-post-field .hot-card:hover{
        transform:translateY(-1px);
        box-shadow:0 8px 24px rgba(16,24,40,.08);
    }

    .hot-post-field .hot-left{ display:flex; align-items:center; gap:12px; min-width:0; }
    .hot-post-field .hot-icon{ font-size:20px; line-height:1; }

    .hot-post-field .hot-text{ min-width:0; }
    .hot-post-field .hot-title{
        font-weight:600; font-size:14px; color:#111827; margin-bottom:2px;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .hot-post-field .hot-help{
        font-size:12px; color:#6b7280;
        max-width:520px; line-height:1.3;
    }

    /* Làm đẹp công tắc Bootstrap switch */
    .hot-post-field .hot-switch .form-check-input{
        width:3rem; height:1.6rem; cursor:pointer;
        border-color:#d1d5db;
    }
    .hot-post-field .hot-switch .form-check-input:focus{
        box-shadow:0 0 0 .2rem rgba(255,107,107,.15);
    }
    .hot-post-field .hot-switch .form-check-input:checked{
        background-color:#ff6b6b; border-color:#ff6b6b;
    }
    .hot-post-field .hot-switch .form-check-label{
        margin-left:1.4rem; font-weight:500; color:#374151;
        min-width:70px; text-align:left;
    }

    /* Responsive: xuống hàng trên màn nhỏ */
    @media (max-width:576px){
        .hot-post-field .hot-card{ flex-direction:column; align-items:flex-start; }
        .hot-post-field .hot-switch{ width:100%; display:flex; align-items:center; justify-content:space-between; }
    }

    .video-row{
        display:grid;
        grid-template-columns: minmax(160px,1fr) minmax(220px,2fr) auto;
        gap:8px;
        align-items:center;
        margin-bottom:10px;
    }
    .video-actions{ display:flex; gap:6px; }

    /* Mobile: xếp dọc gọn gàng */
    @media (max-width: 575.98px){
        .video-row{
            grid-template-columns: 1fr;
        }
        .video-actions{
            justify-content:flex-start;
        }
    }

</style>
<div class="row">
	<div class="col-md-8 col-sm-8 col-xs-12">

        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Danh mục</label>

            <ui-select remove-selected="true" ng-model="form.cate_id" theme="select2">
                <ui-select-match placeholder="Chọn danh mục">
                    <% $select.selected.name %>
                    <small class="text-muted ms-1" ng-if="$select.selected._label_suffix">
                        <% $select.selected._label_suffix %>
                    </small>
                </ui-select-match>

                <!-- Hiển thị tất cả, nhưng khoá các cha có con -->
                <ui-select-choices
                    repeat="t.id as t in (form.all_categories_marked | filter:$select.search) track by t.id"
                    ui-disable-choice="t._disabled">
                    <div class="d-flex align-items-center">
                        <span ng-bind="t.name"></span>
                        <small class="text-muted ms-2" ng-if="t._label_suffix"><% t._label_suffix %></small>
                    </div>
                </ui-select-choices>
            </ui-select>

            <span class="invalid-feedback d-block" role="alert">
  <strong><% errors.cate_id[0] %></strong>
</span>


        </div>




        <div class="form-group custom-group mb-4">
            <label class="form-label required-label">Tiêu đề bài viết</label>
            <input class="form-control" ng-model="form.name" type="text">
            <span class="invalid-feedback d-block" role="alert">
				<strong><% errors.name[0] %></strong>
			</span>
        </div>
        <div class="form-group">
            <label>Mô tả ngắn</label>
            <textarea name="form.intro"  class="form-control"
                     ng-model="form.intro" rows="5"></textarea>

        </div>
        <div class="form-group">
            <label>Nội dung</label>
            <textarea name="form.body"  class="form-control"
                      ck-editor ng-model="form.body" rows="7"></textarea>

        </div>

        <style>
            /* UI nho nhỏ cho uploader */
            .upload-box{
                border:2px dashed #d7dbe0; border-radius:10px; padding:16px 20px;
                display:inline-flex; gap:10px; align-items:center; cursor:pointer; user-select:none;
            }
            .upload-box:hover{ background:#f7f9fc; }
            .upload-box .icon{ font-size:18px; }
            .file-list{ margin:10px 0 0; padding:0; list-style:none; }
            .file-list li{ display:flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid #eee; border-radius:8px; margin-bottom:6px; }
            .file-list .muted{ color:#6b7280; font-size:.9rem; }
            .btn-remove{ margin-left:auto; border:0; background:#ffe5e5; color:#c0392b; border-radius:6px; padding:2px 8px; cursor:pointer; }
            .btn-remove:hover{ background:#ffd6d6; }

        </style>
        <div class="form-group">
            <label class="d-block">Tài liệu đính kèm (PDF, DOC/DOCX)</label>

            <input id="file-input" type="file" accept=".pdf,.doc,.docx" multiple hidden>

            <label class="upload-box" for="file-input">
                <span class="icon">⬆</span>
                <span>Chọn hoặc kéo–thả file vào đây</span>
                <small class="text-muted d-block mt-1">Tối đa 20MB/ file, có thể chọn nhiều</small>
            </label>

            <ul class="file-list" ng-if="form.files && form.files.length">
                <li ng-repeat="f in form.files track by $index">
                    <strong><% f.name %></strong>
                    <span class="muted">(<% (f.size/1024/1024).toFixed(2) %> MB)</span>
                    <button type="button" class="btn-remove" ng-click="removeFile($index)">✕</button>
                </li>
            </ul>

        </div>


        <div class="card">
            <div class="card-header text-center">Thêm Video</div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Video YouTube</label>

                    <div class="video-row" ng-repeat="(idx, item) in form.videos track by idx">
                        <input type="text"
                               class="form-control"
                               placeholder="Nhập tiêu đề"
                               ng-model="item.title">

                        <input type="text"
                               class="form-control"
                               placeholder="Dán link YouTube"
                               ng-model="item.link">

                        <div class="video-actions">
                            <button type="button"
                                    class="btn btn-success"
                                    ng-if="idx === form.videos.length - 1"
                                    ng-click="form.addVideo()">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-danger"
                                    ng-if="form.videos.length > 1"
                                    ng-click="form.removeVideo(idx)">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="invalid-feedback d-block"><% errors.videos %></div>
                </div>
            </div>
        </div>


    </div>

	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="form-group custom-group mb-4">
			<label class="form-label required-label">Trạng thái</label>
			<select id="my-select" class="form-control custom-select" ng-model="form.status">
				<option value="">Chọn trạng thái</option>
				<option ng-repeat="s in form.statuses" ng-value="s.id" ng-selected="form.status == s.id"><% s.name %></option>
			</select>
		</div>

        <div class="form-group custom-group mb-4">
            <label class="form-label d-flex align-items-center justify-content-between">
                <span>Thẻ tag</span>
            </label>

            <ui-select multiple
                       ng-model="form.tag_ids"
                       theme="select2"
                       close-on-select="false"
                       remove-selected="true"
                       reset-search-input="true"
                       title="Chọn một hoặc nhiều thẻ">
                <ui-select-match placeholder="Chọn thẻ tag...">
      <span class="cate-chip">
       <% $item.name %>
      </span>
                </ui-select-match>

                <ui-select-choices
                    repeat="t as t in (form.all_tags | filter: $select.search) track by t.id"
                >
                    <div class="d-flex align-items-center">
                        <span ng-bind="t.name"></span>
                    </div>
                </ui-select-choices>
            </ui-select>

            <div class="mt-2 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        ng-click="form.tag_ids=[]"
                        ng-disabled="!form.tag_ids.length">
                    Xoá hết
                </button>
            </div>

            <span class="invalid-feedback d-block" role="alert" ng-if="errors.tag_ids && errors.tag_ids[0]">
    <strong><% errors.tag_ids[0] %></strong>
  </span>
        </div>


        <div class="card mb-4">
            <div class="card-header text-center ">
                <h5>Ảnh đại diện</h5>
            </div>
            <div class="card-body">
                <div class="form-group text-center mb-4">
                    <div class="main-img-preview">
                        <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 10MB.</p>
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
	<a href="{{ route('Post.index') }}" class="btn btn-danger btn-cons">
		<i class="fa fa-remove"></i> Hủy
	</a>
</div>
