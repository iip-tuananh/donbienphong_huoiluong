<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6>Chính sách</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" ng-model="form.title">

                            <span class="invalid-feedback d-block" role="alert">
				        <strong><% errors.title[0] %></strong>
			        </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control" ng-model="form.status">
                                <option ng-value="1" ng-selected="1 == form.status">Xuất bản</option>
                                <option ng-value="0" ng-selected="0 == form.status">Nháp</option>

                            </select>
                            <span class="invalid-feedback d-block" role="alert">
				        <strong><% errors.status[0] %></strong>
			        </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control ck-editor" ck-editor rows="5" ng-model="form.content"></textarea>
                            <span class="invalid-feedback d-block" role="alert">
				                <strong><% errors.content[0] %></strong>
                            </span>
                        </div>
                    </div>

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
    <a href="{{ route('policies.index') }}" class="btn btn-danger btn-cons">
        <i class="fa fa-remove"></i> Hủy
    </a>
</div>
