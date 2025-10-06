<div class="row">
    <div class="col-sm-5">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Mã</label>
                    <input class="form-control " type="text" ng-model="form.code" ng-change="form.code = form.code.toUpperCase()">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.code[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên mã giảm giá</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Mô tả ngắn gọn</label>
                    <textarea class="form-control " ng-model="form.description"></textarea>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.description[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Số lượng mã</label>
                    <input class="form-control " type="number" ng-model="form.quantity">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.quantity[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Giá trị giảm áp dụng trên đơn hàng</label>
                    <input class="form-control " type="number" ng-model="form.value">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.value[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label">Số lượng hàng tối thiểu trên đơn hàng</label>
                    <input class="form-control " type="number" ng-model="form.limit_product_qty">
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label">Giá trị đơn hàng tối thiểu</label>
                    <input class="form-control " type="number" ng-model="form.limit_bill_value">
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group custom-group">
                            <label class="form-label required-label">Ngày áp dụng</label>
                            <input class="form-control " type="date" ng-model="form.from_date">
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><% errors.from_date[0] %></strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group custom-group">
                            <label class="form-label required-label">Ngày hết hạn</label>
                            <input class="form-control " type="date" ng-model="form.to_date">
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><% errors.to_date[0] %></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Trạng thái</label>
                    <select id="my-select" class="form-control custom-select" ng-model="form.status">
                        <option ng-repeat="s in status" ng-value="s.value" ng-selected="form.status == s.value"><% s.name %></option>

                    </select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.status[0] %></strong>
                    </span>
                </div>
            </div>

        </div>
    </div>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Mô tả chi tiết</label>
                    <textarea class="form-control ck-editor" ck-editor rows="1" ng-model="form.content"></textarea>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.content[0] %></strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
