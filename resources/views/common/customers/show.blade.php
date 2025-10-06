<div class="modal fade" id="show-customer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="max-width: 850px">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="semi-bold"><% form.name %></h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#chi_tiet">Chi tiết</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#giao_dich">Giao dịch</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#lich_su">Lịch sử</a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="chi_tiet" class="tab-pane fade show active pt-4">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group custom-group">
									<label class="form-label required-label">Tên khách</label>
									<input class="form-control" type="text" name="name" placeholder="Tên khách (*)" ng-model="form.name" disabled="disabled">
								</div>

								<div class="form-group custom-group">
									<label class="form-label required-label">Số điện thoại</label>
									<input class="form-control" type="text" name="mobile" placeholder="Điện thoại (*)" ng-model="form.mobile" disabled="disabled">
									<span class=" invalid-feedback d-block" role="alert" ng-if="errors && errors.mobile">
								</div>

								<div class="form-group custom-group">
									<label class="form-label">Email</label>
									<input class="form-control" type="text" name="email" placeholder="Email" ng-model="form.email" disabled="disabled">
								</div>

								<div class="form-group custom-group">
									<label class="form-label">Ngày sinh</label>
									<input class="form-control" date type="text" name="birth_day" ng-model="form.birth_day" disabled="disabled">
								</div>

								<div class="form-group custom-group">
									<label class="form-label required-label">Giới tính</label>
									<select class="form-control custom-select" name="gender" ng-model="form.gender" disabled="disabled">
										<option value="1">Nam</option>
										<option value="0">Nữ</option>
									</select>
								</div>

								<div class="form-group custom-group">
									<label class="form-label required-label">Trạng thái</label>
									<select class="form-control" name="status" ng-model="form.status" disabled="disabled">
										<option value="1">Hoạt động</option>
										<option value="0">Khóa</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group custom-group">
									<label class="form-label">Nhóm khách</label>
									<ui-select class="" remove-selected="false" ng-model="form.customer_group_id" id="edit_province_id" disabled="disabled">
										<ui-select-match placeholder="Chọn tỉnh">
											<% $select.selected.name %>
										</ui-select-match>
										<ui-select-choices repeat="g.id as g in (form.customer_groups | filter: $select.search)">
											<span ng-bind="g.name"></span>
										</ui-select-choices>
									</ui-select>
								</div>
								<div class="form-group custom-group">
									<label class="form-label">Tỉnh</label>
									<ui-select class="" remove-selected="false" ng-change="getDistricts(form.province_id, form, '#edit_district_id')" ng-model="form.province_id" id="edit_province_id" disabled="disabled">
										<ui-select-match placeholder="Chọn tỉnh">
											<% $select.selected.name_with_type %>
										</ui-select-match>
										<ui-select-choices repeat="p.id as p in (form.provinces | filter: $select.search)">
											<span ng-bind="p.name_with_type"></span>
										</ui-select-choices>
									</ui-select>
								</div>
								<div class="form-group custom-group">
									<label class="form-label">Huyện</label>
									<ui-select remove-selected="false" ng-change="getWards(form.district_id, form, '#edit_ward_id')" ng-model="form.district_id" id="edit_district_id" disabled="disabled">
										<ui-select-match placeholder="Chọn huyện">
											<% $select.selected.name_with_type %>
										</ui-select-match>
										<ui-select-choices repeat="d.id as d in (districts[form.province_id] | filter: $select.search)">
											<span ng-bind="d.name_with_type"></span>
										</ui-select-choices>
									</ui-select>
								</div>
								<div class="form-group custom-group">
									<label class="form-label">Xã</label>
									<ui-select remove-selected="false" ng-model="form.ward_id" id="edit_ward_id" disabled="disabled">
										<ui-select-match placeholder="Chọn xã">
											<% $select.selected.name_with_type %>
										</ui-select-match>
										<ui-select-choices repeat="w.id as w in (wards[form.district_id] | filter: $select.search)">
											<span ng-bind="w.name_with_type"></span>
										</ui-select-choices>
									</ui-select>
									</span>
								</div>

								<div class="form-group custom-group">
									<input class="form-control" type="text" name="adress" placeholder="Địa chỉ" ng-value="<% form.adress %>" ng-model="form.adress" disabled="disabled">
								</div>
							</div>
						</div>
					</div>
					<div id="giao_dich" class="tab-pane fade pt-4">
						<table class="table table-bordered table-hover table-stripped">
							<thead>
								<th>STT</th>
								<th>Số hóa đơn</th>
								<th>Ngày hóa đơn</th>
								<th>Giá trị</th>
								<th>Đã thanh toán</th>
								<th>Công nợ</th>
							</thead>
							<tbody>
								<tr ng-if="!form.bills.length">
									<td colspan="5">Chưa có giao dịch</td>
								</tr>
								<tr ng-repeat="b in form.bills track by $index">
									<td><% $index + 1 %></td>
									<td><% b.code %></td>
									<td><% b.bill_date | toDate | date:'dd/MM/yyyy HH:mm' %></td>
									<td style="text-align: right"><% b.cost_after_vat| number %></td>
									<td style="text-align: right"><% b.payed_value | number %></td>
									<td style="text-align: right"><% b.cost_after_vat - b.payed_value | number %></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="lich_su" class="tab-pane fade">
						<table class="table table-bordered table-hover table-stripped">
							<thead>
								<tr>
									<th>Người sửa / Thời gian</th>
									<th>Nội dung sửa</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-if="!form.versions.length">
									<td colspan="2">Chưa có dữ liệu</td>
								</tr>
								<tr ng-repeat="v in form.versions">
									<td>
										<div><b><% v.user.name %></b></div>
										<div><% v.time | toDate | date:'dd/MM/yyyy HH:mm' %></div>
									</td>
									<td>
										<table class="table table-bordered table-hover table-stripped">
											<thead>
												<tr>
													<th>Tên trường</th>
													<th>Giá trị cũ</th>
													<th>Giá trị mới</th>
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="h in v.histories">
													<td><% cols[h.column_name] || h.column_name %></td>
													<td ng-if="isNormal(h)"><% h.old_value %></td>
													<td ng-if="isDate(h)"><% h.old_value | date:'dd/MM/yyyy' %></td>
													<td ng-if="isNormal(h)"><% h.new_value %></td>
													<td ng-if="isDate(h)"><% h.new_value | date:'dd/MM/yyyy' %></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Quay lại</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>