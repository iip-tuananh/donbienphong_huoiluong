<style>
	.group-name {
		font-weight: bold;
		margin-bottom: 10px;
		border-bottom: 1px solid #aaa;
		cursor: pointer;
		display: flex;
	}
	.permission-amount {
		padding: 1px 5px;
		background-color: #bbb;
		color: #fff;
		border-radius: 50%;
	}
	[data-toggle="collapse"] .fa:before {
		content: "\f106";
	}

	[data-toggle="collapse"].collapsed .fa:before {
		content: "\f107";
	}
</style>
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
							<label class="form-label">Tên chức vụ</label>
							<span class="text-danger">(*)</span>
							<input class="form-control" ng-model="form.display_name" type="text">
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.display_name[0] || errors.name[0]%></strong>
							</span>
						</div>
					</div>
					<!-- <div class="col-md-6">
						<div class="form-group">
							<label class="form-label">Loại chức vụ</label>
                            <span class="text-danger">(*)</span>
							<select class="form-control" select2 ng-model="form.type" ng-change="changeType()" ng-disabled="form.id">
                                <option value="">Chọn loại chức vụ</option>
                                <option ng-repeat="t in types" value="<% t.id %>" ng-selected="t.id == form.type">
                                    <% t.name %>
                                </option>
                            </select>
							<span class="invalid-feedback d-block" role="alert">
								<strong><% errors.type[0] %></strong>
							</span>
						</div>
					</div> -->
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h6>Chi tiết</h6>
				<div class="text-danger ml-1">(*)</div>
				<button type="button" class="btn btn-sm btn-secondary ml-auto cursor-pointer" ng-click="toggleCollapse()">
					<% toggle ? "Ẩn tất cả" : "Hiện tất cả" %>
				</button>
			</div>
			<div class="card-body">
				<span class="invalid-feedback d-block" role="alert" ng-if="errors && errors.permissions">
					<strong><% errors.permissions[0] %></strong>
				</span>
                <div ng-if="!getGroups.length">
                    Không có quyền phù hợp
                </div>
				<div class="row d-flex flex-wrap">
					<div class="col-md-<% col %>" ng-repeat="group in groups track by $index">
						<div ng-repeat="item in group track by $index">
							<div class="group-name" data-toggle="collapse" ng-class="{'text-success': getActive(groupRoles[item]) > 0}"
								data-target="#group<% $parent.$index %>-<% $index %>" aria-expanded="true" aria-controls="group<% $index %>">
								<span>
									<% item %>
									<span ng-if="getActive(groupRoles[item]) > 0" class="permission-amount"><% getActive(groupRoles[item]) %></span>
								</span>
								<i class="fa ml-auto" aria-hidden="true"></i>
							</div>
							<div id="group<% $parent.$index %>-<% $index %>" class="collapse show">
								<div class="group-item" ng-repeat="permission in groupRoles[item] track by $index">
									<div class="border-checkbox-section">
										<div class="border-checkbox-group border-checkbox-group-primary">
											<input class="border-checkbox" id="checkbox<% $parent.$parent.$index %>-<% $parent.$index %>-<% $index %>"
												type="checkbox" ng-value="permission.id" name="permissions[]" ng-checked="form.permissions.includes(permission.id)" ng-click="toggleSelected(permission.id)">
											<label style="font-weight:400" class="border-checkbox-label" for="checkbox<% $parent.$parent.$index %>-<% $parent.$index %>-<% $index %>">
												<% permission.display_name %>
											</label>
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
<hr>
<div class="text-right">
	<button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
		<i ng-if="!loading.submit" class="fa fa-save"></i>
		<i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
		Lưu
	</button>
	<a href="{{ route('Role.index') }}" class="btn btn-danger btn-cons">
		<i class="fa fa-remove"></i> Hủy
	</a>
</div>
