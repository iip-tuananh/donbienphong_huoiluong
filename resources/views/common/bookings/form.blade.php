<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Khách hàng</label>
                    <ui-select class="" remove-selected="false" ng-model="form.customer_id">
                        <ui-select-match placeholder="Chọn khách hàng">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="t.id as t in (form.customers | filter: $select.search)">
                            <span ng-bind="t.name"></span>
                        </ui-select-choices>
                    </ui-select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.customer_id[0] %></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Giờ hẹn</label>
                    <input class="form-control " datetime type="text" ng-model="form.booking_time">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.booking_time[0] %></strong>
                    </span>
                </div>
            </div>
            @if(Auth::user()->type == 1)
            <div class="col-sm-12 col-md-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Gara nhận lịch</label>
                    <ui-select class="" remove-selected="false" ng-model="form.g7_id">
                        <ui-select-match placeholder="Chọn gara">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="p.id as p in (form.g7 | filter: $select.search)">
                            <span ng-bind="p.name"></span>
                        </ui-select-choices>
                    </ui-select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.g7_id[0] %></strong>
                    </span>
                </div>
            </div>
            @endif
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label">Ghi chú</label>
                    <input class="form-control" type="text" ng-model="form.note">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.note[0] %></strong>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Trạng thái</label>
                    <ui-select class="" remove-selected="false" ng-model="form.status">
                        <ui-select-match placeholder="Trạng thái">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="t.id as t in (form.statuses | filter: $select.search)">
                            <span ng-bind="t.name"></span>
                        </ui-select-choices>
                    </ui-select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.status[0] %></strong>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>