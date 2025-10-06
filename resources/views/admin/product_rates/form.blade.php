<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Người đánh giá</label>
                    <input class="form-control " type="text" ng-model="form.name" disabled>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên sản phẩm</label>
                    <input class="form-control " type="text" ng-model="form.product.name" disabled>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Số điện thoại</label>
                    <input class="form-control " type="text" ng-model="form.phone" disabled>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tiêu đề đánh giá</label>
                    <input class="form-control " type="text" ng-model="form.title" disabled>
                </div>
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Email</label>
                    <input class="form-control " type="text" ng-model="form.email" disabled>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Điểm đánh giá</label>
                    <input class="form-control " type="text" ng-model="form.rating" disabled>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Nội dung đánh giá</label>
                    <textarea class="form-control " type="text" ng-model="form.desc" disabled></textarea>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Hình ảnh đánh giá</label>
                    <div style="margin-top: 10px; gap: 10px;" class="d-flex flex-wrap">
                        <div class="image-item" ng-repeat="image in form.images" style="width: calc(25% - 10px);">
                            <img ng-src="<% image.path %>" class="img-fluid" style="display: block; width: 100%; height: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
