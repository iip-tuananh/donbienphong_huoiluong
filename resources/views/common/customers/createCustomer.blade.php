<div class="modal fade" id="createCustomer" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createCustomer">
    <form method="POST" role="form" id="createCustomerForm">
        @csrf
        <div class="modal-dialog" style="max-width: 650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Thêm mới khách hàng</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group custom-group">
                                <label class="form-label required-label">Tên khách</label>
                                <input class="form-control" type="text" name="name" placeholder="Tên khách (*)" ng-model="form.name">
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>
                                        <% errors.name[0] %>
                                    </strong>
                                </span>
                            </div>

                            <div class="form-group custom-group">
                                <label class="form-label required-label">Số điện thoại</label>
                                <input class="form-control" type="text" name="mobile" placeholder="Điện thoại (*)" ng-model="form.mobile">
                                <span class=" invalid-feedback d-block" role="alert" ng-if="errors && errors.mobile">
                                    <strong>
                                        <% errors.mobile[0] %>
                                    </strong>
                                </span>
                            </div>

                            <div class="form-group custom-group">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="text" name="email" placeholder="Email" ng-model="form.email">
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.email">
                                    <strong>
                                        <% errors.email[0] %>
                                    </strong>
                                </span>
                            </div>

                            <div class="form-group custom-group">
                                <label class="form-label">Ngày sinh</label>
                                <input class="form-control" date type="text" name="birth_day" ng-model="form.birth_day">
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.birth_day">
                                    <strong>
                                        <% errors.birth_day[0] %>
                                    </strong>
                                </span>
                            </div>

                            <div class="form-group custom-group">
                                <label class="form-label">Giới tính</label>
                                <select class="form-control custom-select" name="gender" ng-model="form.gender">
                                    <option value="1">Nam</option>
                                    <option value="0">Nữ</option>
                                </select>
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.gender">
                                    <strong>
                                        <% errors.gender[0] %>
                                    </strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-group">
                                <label class="form-label">Nhóm khách</label>
                                <div class="input-group mb-3">
                                    <select class="form-control custom-select" name="customer_group_id" ng-model="form.customer_group_id">
                                        <option value="">Chọn nhóm khách hàng</option>
                                        <option ng-repeat="g in form.customer_groups" ng-value="g.id" ng-selected="g.id == form.customer_group_id">
                                            <% g.name %>
                                        </option>
                                    </select>
                                    @if(Auth::user()->type == 1)
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#createCustomerGroup">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.customer_group_id">
                                    <strong>
                                        <% errors.customer_group_id[0] %>
                                    </strong>
                                </span>
                            </div>
                            <div class="form-group custom-group">
                                <label class="form-label">Tỉnh</label>
                                <ui-select class="" remove-selected="false" ng-change="getDistricts(form.province_id, form, '#create_district_id')" ng-model="form.province_id" id="create_province_id">
                                    <ui-select-match placeholder="Chọn tỉnh">
                                        <% $select.selected.name_with_type %>
                                    </ui-select-match>
                                    <ui-select-choices repeat="p.id as p in (form.provinces | filter: $select.search)">
                                        <span ng-bind="p.name_with_type"></span>
                                    </ui-select-choices>
                                </ui-select>
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.province_id">
                                    <strong>
                                        <% errors.province_id[0] %>
                                    </strong>
                                </span>
                            </div>
                            <div class="form-group custom-group">
                                <label class="form-label">Huyện</label>
                                <ui-select remove-selected="false" ng-change="getWards(form.district_id, form, '#create_ward_id')" ng-model="form.district_id" id="create_district_id">
                                    <ui-select-match placeholder="Chọn huyện">
                                        <% $select.selected.name_with_type %>
                                    </ui-select-match>
                                    <ui-select-choices repeat="d.id as d in (districts[form.province_id] | filter: $select.search)">
                                        <span ng-bind="d.name_with_type"></span>
                                    </ui-select-choices>
                                </ui-select>
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.district_id">
                                    <strong>
                                        <% errors.district_id[0] %>
                                    </strong>
                                </span>
                            </div>
                            <div class="form-group custom-group">
                                <label class="form-label">Xã</label>
                                <ui-select remove-selected="false" ng-model="form.ward_id" id="create_ward_id">
                                    <ui-select-match placeholder="Chọn xã">
                                        <% $select.selected.name_with_type %>
                                    </ui-select-match>
                                    <ui-select-choices repeat="w.id as w in (wards[form.district_id] | filter: $select.search)">
                                        <span ng-bind="w.name_with_type"></span>
                                    </ui-select-choices>
                                </ui-select>
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.ward_id">
                                    <strong>
                                        <% errors.ward_id[0] %>
                                    </strong>
                                </span>
                            </div>

                            <div class="form-group custom-group">
                                <label class="form-label">Địa chỉ</label>
                                <input class="form-control" type="text" name="adress" placeholder="Địa chỉ" ng-model="form.adress">
                                <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.adress">
                                    <strong>
                                        <% errors.adress[0] %>
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                        <i ng-if="!loading.submit" class="fa fa-save"></i>
                        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                        Lưu
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>
{{-- @include('partial.classes.common.Customer') --}}
<script>
    let createCustomerCallback;
    let customerCarId;
    app.controller('createCustomer', function ($scope, $rootScope, $http) {
        $scope.form = new Customer({}, {scope: $scope});
        $scope.loading = {};

        $(document).on('shown.bs.modal', '#createCustomer', function() {
            document.getElementById("createCustomerForm").reset();
        })

        createCustomerGroupCallback = (response) => {
            $scope.form.customer_groups.push(response);
            $scope.form.customer_group_id = response.id;
            $rootScope.$emit("CreateCustomerGroup", response);
        }

        @include('common.customers.formJs')
         // Submit Form tạo mới
        $scope.submit = function () {
            let url = "{!! route('Customer.store') !!}";;
            $scope.loading.submit = true;
            let data = $scope.form.submit_data;
            data.car_id = customerCarId;
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $scope.form = new Customer({}, {scope: $scope});
                        toastr.success(response.message);
                        if (createCustomerCallback) createCustomerCallback(response.data);
                        $('#createCustomer').modal('hide');
                    } else {
                        $scope.errors = response.errors;
                        toastr.warning(response.message);
                    }
                },
                error: function () {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function () {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                },
            });
        }

    })
</script>