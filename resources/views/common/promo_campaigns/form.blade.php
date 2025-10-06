<div class="row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">
				<h5>Thông tin chung</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label class="form-label">Tên</label>
							<span class="text-danger">(*)</span>
							<input class="form-control" ng-model="form.name" type="text">
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.name[0] %></strong>
							</span>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="form-label">Loại KM</label>
							<span class="text-danger">(*)</span>
							<ui-select remove-selected="false" ng-model="form.type" ng-change="form.changeType()">
								<ui-select-match placeholder="Chọn loại khuyến mãi">
									<% $select.selected.name %>
								</ui-select-match>
								<ui-select-choices repeat="item.id as item in (form.all_types | filter: $select.search)">
									<span ng-bind="item.name"></span>
								</ui-select-choices>
							</ui-select>
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.type[0] %></strong>
							</span>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="form-label">Số lượng áp dụng</label>
							<span class="text-secondary">(Để trống nếu không giới hạn)</span>
							<input class="form-control" currency ng-model="form.limit" type="text">
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.limit[0] %></strong>
							</span>
						</div>
					</div>
					<div class="col-lg-12" ng-if="form.type == 3">
						<div class="form-group">
							<label class="form-label">Hàng hóa áp dụng</label>
							<span class="text-danger">(*)</span>
							<div class="input-group mb-0">
								<div class="input-group-prepend">
									<button class="btn btn-info" type="button" ng-click="searchCampaignProduct.open()">
										<i class="fa fa-search"></i>
									</button>
								</div>
								<input type="text" class="form-control" placeholder="Chọn hàng hóa áp dụng" value="<% form.product.name %>" disabled>
							</div>
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.product_id[0] %></strong>
							</span>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label class="form-label">Mô tả</label>
							<textarea class="form-control" rows="3" ng-model="form.note"></textarea>
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.note[0] %></strong>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h5>Chi tiết</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<table class="table table-bordered table-hover table-responsive" ng-if="form.type == 1">
						<thead>
							<tr>
								<th rowspan="2">STT</th>
								<th colspan="2">Giá trị</th>
								<th rowspan="2">Khuyến mãi</th>
								<th rowspan="2">
									<button class="btn btn-link text-success p-0" ng-click="form.addCheckpoint()">
										<i class="fa fa-plus"></i>
									</button>
								</th>
							</tr>
							<tr>
								<th>Từ</th>
								<th>Đến</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="c in form.checkpoints track by $index">
								<td class="text-center"><% $index + 1 %></td>
								<td>
									<input class="form-control" type="text" currency ng-model="c.from">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.from'][0] %></strong>
									</span>
								</td>
								<td>
									<input class="form-control" type="text" currency ng-model="c.to">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.to'][0] %></strong>
									</span>
								</td>
								<td>
									<div class="d-flex mb-1">
										<input class="form-control mr-1" currency ng-model="c.value">
										<ui-select remove-selected="false" ng-model="c.type">
											<ui-select-match placeholder="Chọn loại chiết khấu">
												<% $select.selected.name %>
											</ui-select-match>
											<ui-select-choices repeat="t.id as t in (c.types | filter: $select.search)" ng-selected="t.id == form.service_type_id">
												<span ng-bind="t.name"></span>
											</ui-select-choices>
										</ui-select>
									</div>
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.value'][0] || errors['checkpoints.' + $index + '.type'][0] %></strong>
									</span>
								</td>
								<td class="text-center">
									<button class="btn btn-link text-danger p-0" ng-click="form.removeCheckpoint($index)">
										<i class="fas fa-times"></i>
									</button>
								</td>
							</tr>
							<tr ng-if="!form.checkpoints.length">
								<td colspan="5">Chưa có dữ liệu</td>
							</tr>
						</tbody>
					</table>
					<table class="table table-bordered table-hover table-responsive" ng-if="form.type == 2">
						<thead>
							<tr>
								<th rowspan="2">STT</th>
								<th colspan="2">Giá trị</th>
								<th colspan="4">Khuyến mãi</th>
								<th rowspan="2">
									<button class="btn btn-link text-success p-0" ng-click="form.addCheckpoint()">
										<i class="fa fa-plus"></i>
									</button>
								</th>
							</tr>
							<tr>
								<th>Từ</th>
								<th>Đến</th>
								<th>Tên hàng</th>
								<th>Mã hàng</th>
								<th>Số lượng</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat-start="c in form.checkpoints track by $index">
								<td class="text-center" rowspan="<% c.rowspan %>"><% $index + 1 %></td>
								<td rowspan="<% c.rowspan %>">
									<input class="form-control" type="text" currency ng-model="c.from">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.from'][0] %></strong>
									</span>
								</td>
								<td rowspan="<% c.rowspan %>">
									<input class="form-control" type="text" currency ng-model="c.to">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.to'][0] %></strong>
									</span>
								</td>
								<td colspan="4" class="p-0 border-none"></td>
								<td class="text-center" rowspan="<% c.rowspan %>">
									<button class="btn btn-link text-danger p-0" ng-click="form.removeCheckpoint($index)">
										<i class="fas fa-times"></i>
									</button>
								</td>
							</tr>
							<tr ng-repeat="p in c.products">
								<td><% p.product.name %></td>
								<td><% p.product.code %></td>
								<td>
									<input class="form-control" type="text" currency ng-model="p.qty">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $parent.$index + '.products.' + $index + '.qty'][0] %></strong>
									</span>
								</td>
								<td class="text-center">
									<button class="btn btn-link text-danger p-0" ng-click="c.removeProduct($index)">
										<i class="fas fa-minus"></i>
									</button>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<button class="btn btn-link text-success" ng-click="searchProduct(c)">
										<i class="fa fa-plus"></i> Thêm hàng khuyến mãi
									</button>
								</td>
							</tr>
							<tr ng-repeat-end></tr>
							<tr ng-if="!form.checkpoints.length">
								<td colspan="8">Chưa có dữ liệu</td>
							</tr>
						</tbody>
					</table>
					<table class="table table-bordered table-hover table-responsive" ng-if="form.type == 3">
						<thead>
							<tr>
								<th rowspan="2">SL</th>
								<th colspan="4">Khuyến mãi</th>
							</tr>
							<tr>
								<th>Tên hàng</th>
								<th>Mã hàng</th>
								<th>Số lượng</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat-start="c in form.checkpoints track by $index">
								<td rowspan="<% c.rowspan %>">
									<input class="form-control" type="text" currency ng-model="c.from">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $index + '.from'][0] %></strong>
									</span>
								</td>
								<td colspan="4" class="p-0 border-none"></td>
							</tr>
							<tr ng-repeat="p in c.products">
								<td><% p.product.name %></td>
								<td><% p.product.code %></td>
								<td>
									<input class="form-control" type="text" currency ng-model="p.qty">
									<span class="invalid-feedback d-block" role="alert">
										<strong><% errors['checkpoints.' + $parent.$index + '.products.' + $index + '.qty'][0] %></strong>
									</span>
								</td>
								<td class="text-center">
									<button class="btn btn-link text-danger p-0" ng-click="c.removeProduct($index)">
										<i class="fas fa-minus"></i>
									</button>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<button class="btn btn-link text-success" ng-click="searchProduct(c)">
										<i class="fa fa-plus"></i> Thêm hàng khuyến mãi
									</button>
								</td>
							</tr>
							<tr ng-repeat-end></tr>
						</tbody>
					</table>
					<span class="invalid-feedback d-block" role="alert">
						<strong><% errors.checkpoints[0] %></strong>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h5>Điều kiện</h5>
			</div>
			<div class="card-body">
				<div class="form-group">
					<label class="form-label">Từ ngày</label>
					<input class="form-control" date-form ng-model="form.start_date" type="text">
					<span class="invalid-feedback d-block" role="alert">
						<strong><% errors.start_date[0] %></strong>
					</span>
				</div>
				<div class="form-group">
					<label class="form-label">Đến ngày</label>
					<input class="form-control" date-form ng-model="form.end_date" type="text">
					<span class="invalid-feedback d-block" role="alert">
						<strong><% errors.end_date[0] %></strong>
					</span>
				</div>
			</div>
		</div>
		@if (Auth::user()->type != App\Model\Common\User::G7)
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h5>Áp dụng</h5>
				<div class="text-danger ml-1">(*)</div>
			</div>
			<div class="card-body">
				<div class="form-group">
					<div class="form-check">
						<input id="for_all" class="form-check-input" type="checkbox" ng-model="form.for_all">
						<label for="for_all" class="form-check-label">Áp dụng tất cả điểm G7</label>
					</div>
				</div>
				<ui-select ng-model="form.g7_ids" remove-selected="false" multiple ng-disabled="form.for_all">
					<ui-select-match placeholder="Chọn điểm G7">
						<% $item.name %>
					</ui-select-match>
					<ui-select-choices repeat="item.id as item in (form.all_g7s | filter: $select.search)">
						<span ng-bind="item.name"></span>
					</ui-select-choices>
				</ui-select>
				<span class="invalid-feedback d-block" role="alert">
					<strong><% errors.g7_ids[0] %></strong>
				</span>
			</div>
		</div>
		@endIf
	</div>
</div>

<hr>
<div class="text-right">
	<button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
		<i ng-if="!loading.submit" class="fa fa-save"></i>
		<i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
		Lưu
	</button>
	<a href="{{ route('PromoCampaign.index') }}" class="btn btn-danger btn-cons">
		<i class="fa fa-remove"></i> Hủy
	</a>
</div>