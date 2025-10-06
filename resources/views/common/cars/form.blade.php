<div class="row">
    <div class="col-md-6">
        <div class="form-group custom-group">
            <label class="form-label required-label">Biển số xe</label>
            <input class="form-control" type="text" ng-model="form.license_plate">
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.license_plate[0] %></strong>
            </span>
        </div>

        <div class="form-group custom-group">
            <label class="form-label required-label">Chủ xe</label>
            <ui-select remove-selected="false" multiple ng-model="form.customer_ids">
                <ui-select-match placeholder="Chọn chủ xe">
                    <% $item.name %>
                </ui-select-match>
                <ui-select-choices repeat="item.id as item in (form.all_customers | filter: $select.search)">
                    <span ng-bind="item.name"></span>
                </ui-select-choices>
            </ui-select>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#createCustomer">
                <small><i class="fa fa-plus"></i> Thêm chủ xe</small>
            </a>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.customer_ids[0] %></strong>
            </span>
        </div>

    </div>

    <div class="col-md-6">
        {{-- <div class="form-group custom-group">
            <label class="form-label required-label">Dòng xe</label>
            <div class="input-group mb-3">
                <div class="flex-grow-1">
                    <ui-select remove-selected="false" ng-model="form.category_id">
                        <ui-select-match placeholder="Chọn dòng xe">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="item.id as item in (form.categories | filter: $select.search)">
                            <span ng-bind="item.name"></span>
                        </ui-select-choices>
                    </ui-select>
                </div>
                @if(Auth::user()->typpe == 1)
                <div class="input-group-append">
                    <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#createVehicleCategory"><i class="fa fa-plus"></i></button>
                </div>
                @endif
            </div>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.category[0] %></strong>
            </span>

        </div> --}}
        <div class="form-group custom-group">
            <label class="form-label required-label">Hãng xe</label>
            <div class="input-group mb-3">
                <div class="flex-grow-1">
                    <ui-select remove-selected="false" ng-change="getVehicleTypes(form.manufact_id, form, '#create_type_id')" ng-model="form.manufact_id" id="create_manufact_id">
                        <ui-select-match placeholder="Chọn hãng xe">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="item.id as item in (form.manufacts | filter: $select.search)">
                            <span ng-bind="item.name"></span>
                        </ui-select-choices>
                    </ui-select>
                </div>
                @if(Auth::user()->typpe == 1)
                <div class="input-group-append">
                    <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#createVehicleManufact"><i class="fa fa-plus"></i></button>
                </div>
                @endif
            </div>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.manufact_id[0] %></strong>
            </span>
        </div>
        <div class="form-group custom-group">
            <label class="form-label required-label">Loại xe</label>
            <div class="input-group mb-3">
                <div class="flex-grow-1">
                    <ui-select remove-selected="false" id="create_type_id" ng-model="form.type_id">
                        <ui-select-match placeholder="Chọn loại xe">
                            <% $select.selected.name %>
                        </ui-select-match>
                        <ui-select-choices repeat="item.id as item in (form.types[form.manufact_id] | filter: $select.search)">
                            <span ng-bind="item.name"></span>
                        </ui-select-choices>
                    </ui-select>
                </div>
                @if(Auth::user()->typpe == 1)
                <div class="input-group-append">
                    <button class="btn btn-outline-success" id="quick-type" type="button" ng-disabled="!form.manufact_id"><i class="fa fa-plus"></i></button>
                </div>
                @endif
            </div>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.type_id[0] %></strong>
            </span>
        </div>
        {{-- <div class="form-group custom-group">
            <label class="form-label required-label">Trạng thái</label>
            <select class="form-control custom-select" ng-model="form.status">
                <option value="">Chọn Trạng thái</option>
                <option ng-repeat="s in form.statuses" value="<% s.id %>" ng-selected="form.status == s.id"><% s.name %></option>
            </select>
            <span class="invalid-feedback d-block" role="alert">
                <strong><% errors.status[0] %></strong>
            </span>
        </div> --}}
    </div>

    <div class="col-md-12 col-xs-12">
        <div class="row ">
            <div class="col-md-12 mb-4">
                <label><input type="checkbox" ng-model="form.reminder_info"> Thông tin nhắc lịch</label>
            </div>
        </div>
        <div class="row" ng-show="form.reminder_info">
            <div class="col-md-6 col-xs-6">
                <div class="form-group custom-group">
                    <label class="form-label">Thời hạn đăng kiểm</label>
                    <div class="input-group">
                        <input type="text" date class="form-control" ng-model="form.registration_deadline">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.type_id[0] %></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="form-group custom-group">
                    <label class="form-label">Thời hạn bảo hiểm thân vỏ</label>
                    <div class="input-group">
                        <input type="text" date class="form-control" ng-model="form.hull_insurance_deadline">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.type_id[0] %></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="form-group custom-group">
                    <label class="form-label">Ngày bảo dưỡng tiếp theo</label>
                    <div class="input-group">
                        <input type="text" date class="form-control" ng-model="form.maintenance_dateline">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.maintenance_dateline[0] %></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="form-group custom-group">
                    <label class="form-label">Thời hạn bảo hiểm bắt buộc</label>
                    <div class="input-group">
                        <input type="text" date class="form-control" ng-model="form.insurance_deadline">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <span class="invalid-feedback d-block" role="alert">
                            <strong><% errors.insurance_deadline[0] %></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>