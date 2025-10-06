<div class="modal fade" id="show-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="POST" role="form" id="show-modal-form">
        @csrf
        <div class="modal-dialog" style="max-width: 650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Chi tiết xe</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group custom-group add-btn">
                                <label class="form-label required-label">Biển số xe</label>
                                <div class="input-group mb-3">
                                    <select class="form-control" ng-model="form.license_plate_id" name="license_plate_id" disabled="disabled">
                                        <option ng-repeat="l in form.license_plates" ng-value="l.id" ng-selected="form.license_plate_id == l.id"><% l.license_plate %></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group custom-group">
                                <label class="form-label required-label">Chủ xe</label>
                                <ui-select ng-model="form.customer_ids" remove-selected="false" multiple disabled="disabled">
                                    <ui-select-match placeholder="Chọn chủ xe">
                                        <% $item.name %>
                                    </ui-select-match>
                                    <ui-select-choices repeat="item.id as item in (form.all_customers | filter: $select.search)">
                                        <span ng-bind="item.name"></span>
                                    </ui-select-choices>
                                </ui-select>
                            </div>
                        </div>
                        <div class=" col-md-6">
                            <div class="form-group custom-group">
                                <label class="form-label required-label">Hãng xe</label>
                                <select class="form-control" select2 name="manufact_id" ng-change="getVehicleTypes(form.manufact_id, form, '#edit_type_id')" ng-model="form.manufact_id" id="edit_manufact_id" disabled="disabled">
                                    <option ng-repeat="m in form.manufacts" ng-value="m.id" ng-selected="form.manufact_id == m.id"><% m.name %></option>
                                </select>
                            </div>
                            <div class="form-group custom-group">
                                <label class="form-label required-label">Loại xe</label>
                                <select class="form-control" select2 name="type_id" id="edit_type_id" ng-model="form.type_id" disabled="disabled">
                                    <option value="">Chọn kiểu xe</option>
                                    <option ng-repeat="t in form.types[form.manufact_id]" ng-value="t.id" ng-selected="form.type_id == t.id"><% t.name %></option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group custom-group">
                                <label class="form-label">Thời hạn đăng kiểm</label>
                                <div class="input-group">
                                    <input type="text" date class="form-control" ng-model="form.registration_deadline" disabled="disabled">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group custom-group">
                                <label class="form-label">Thời hạn bảo hiểm thân vỏ</label>
                                <div class="input-group">
                                    <input type="text" date class="form-control" ng-model="form.hull_insurance_deadline"  disabled="disabled">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group custom-group">
                                <label class="form-label">Ngày bảo dưỡng tiếp theo</label>
                                <div class="input-group">
                                    <input type="text" date class="form-control" ng-model="form.maintenance_dateline"  disabled="disabled">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="form-group custom-group">
                                <label class="form-label">Thời hạn bảo hiểm bắt buộc</label>
                                <div class="input-group">
                                    <input type="text" date class="form-control" ng-model="form.insurance_deadline" disabled="disabled">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>