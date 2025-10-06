<div class="row">
    <div class="col-md-12 col-sm-8 col-xs-12">
        <div class="card-block">
            <div class="tab-content tab-custom">
                <div class="tab-pane p-0 active" id="vi">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Thông tin chung</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Tiêu đề</label>
                                                <span class="text-danger">(*)</span>
                                                <input class="form-control" type="text" ng-model="form.title">
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong><% errors['title'][0] %></strong>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Địa điểm</label>
                                                <span class="text-danger">(*)</span>
                                                <input class="form-control" type="text" ng-model="form.address">
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong><% errors['address'][0] %></strong>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Mức lương</label>
                                                <span class="text-danger">(*)</span>
                                                <input class="form-control" type="text" ng-model="form.salary">
                                            </div>
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong><% errors['salary'][0] %></strong>
                                            </span>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Ngày hết hạn</label>
                                                <span class="text-danger">(*)</span>
                                                <input class="form-control" date type="text" name="expiration_date" ng-model="form.expiration_date">
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong><% errors['expiration_date'][0] %></strong>
                                                </span>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Chi tiết</label>
                                                <span class="text-danger">(*)</span>
                                                <textarea id="editor" class="form-control" ck-editor
                                                          ng-model="form.description" rows="4"></textarea>
                                                <span class="invalid-feedback d-block" role="alert">
                                                 <strong><% errors['description'][0] %></strong>
			                                    </span>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

{{--    <div class="col-md-3 col-sm-4 col-xs-12">--}}
{{--        --}}{{--		<div class="form-group custom-group mb-4">--}}
{{--        --}}{{--			<label class="form-label required-label">Trạng thái</label>--}}
{{--        --}}{{--			<select select2 class="form-control" ng-model="form.status">--}}
{{--        --}}{{--				<option value="">Chọn trạng thái</option>--}}
{{--        --}}{{--				<option ng-repeat="s in form.statuses" ng-value="s.id" ng-selected="form.status == s.id"><% s.name %></option>--}}
{{--        --}}{{--			</select>--}}
{{--        --}}{{--		</div>--}}
{{--        <div class="form-group text-center mb-4">--}}
{{--            <div class="main-img-preview">--}}
{{--                <p class="help-block-img">* Ảnh định dạng: jpg, png không quá 2MB.</p>--}}
{{--                <img class="thumbnail img-preview" ng-src="<% form.image.path %>">--}}
{{--            </div>--}}
{{--            <div class="input-group" style="width: 100%; text-align: center">--}}
{{--                <div class="input-group-btn" style="margin: 0 auto">--}}
{{--                    <div class="fileUpload fake-shadow cursor-pointer">--}}
{{--                        <label class="mb-0" for="<% form.image.element_id %>">--}}
{{--                            <i class="glyphicon glyphicon-upload"></i> Chọn ảnh--}}
{{--                        </label>--}}
{{--                        <input class="d-none" id="<% form.image.element_id %>" type="file" class="attachment_upload"--}}
{{--                               accept=".jpg,.jpeg,.png">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <span class="invalid-feedback d-block" role="alert">--}}
{{--				<strong><% errors.image[0] %></strong>--}}
{{--			</span>--}}
{{--        </div>--}}

{{--    </div>--}}
</div>

<hr>
<div class="text-right">
    <button type="submit" class="btn btn-success btn-cons" ng-click="submit(0)" ng-disabled="loading.submit">
        <i ng-if="!loading.submit" class="fa fa-save"></i>
        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
        Lưu
    </button>
    <a href="{{ route('Project.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
