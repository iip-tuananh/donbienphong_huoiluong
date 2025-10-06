@extends('layouts.main')

@section('css')
<link rel="stylesheet" href="{{ asset('libs/pagination/pagination.css') }}">
@endsection

@section('title')
    Báo cáo khuyến mại
@endsection

@section('content')
<div ng-controller="Warehouse" ng-cloak>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <div class="row">
						<div class="col-md-3">
							<div class="form-group custom-group">
                                <label>CTKM:</label>
                                <ui-select remove-selected="false" ng-model="form.promo_id" theme="select2">
									<ui-select-match placeholder="Chọn CTKM">
										<% $select.selected.name %>
									</ui-select-match>
									<ui-select-choices repeat="item.id as item in (promo_campaigns | filter: $select.search)">
										<span ng-bind="item.name"></span>
									</ui-select-choices>
								</ui-select>
                            </div>
						</div>
						<div class="col-md-3">
                            <div class="form-group custom-group">
                                <label>Từ ngày:</label>
                                <input class="form-control" date-form ng-model="form.from_date" theme="select2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group custom-group">
                                <label>Đến ngày:</label>
                                <input class="form-control" date-form ng-model="form.to_date" theme="select2">
                            </div>
                        </div>
						<div class="col-md-3">
							<div class="form-group custom-group">
                                <label>Khách hàng:</label>
                                <ui-select remove-selected="false" ng-model="form.customer_id" theme="select2">
									<ui-select-match placeholder="Chọn Khách hàng">
										<% $select.selected.name %>
									</ui-select-match>
									<ui-select-choices repeat="item.id as item in (customers | filter: $select.search)">
										<span ng-bind="item.name"></span>
									</ui-select-choices>
								</ui-select>
                            </div>
						</div>
						@if (!Auth::user()->g7_id)
						<div class="col-md-3">
							<div class="form-group custom-group">
                                <label>Gara:</label>
                                <ui-select remove-selected="false" ng-model="form.g7_id" theme="select2">
									<ui-select-match placeholder="Chọn Gara">
										<% $select.selected.name %>
									</ui-select-match>
									<ui-select-choices repeat="item.id as item in (g7s | filter: $select.search)">
										<span ng-bind="item.name"></span>
									</ui-select-choices>
								</ui-select>
                            </div>
						</div>
						@endIf
                    </div>
                    <hr>
                    <div class="text-right">

                        <button class="btn btn-primary" ng-click="filter(1)" ng-disabled="loading.search">
                            <i ng-if="!loading.search" class="fa fa-filter"></i>
                            <i ng-if="loading.search" class="fa fa-spinner fa-spin"></i>
                            Lọc
                        </button>
						<a href="<% printURL() %>" target="_blank" class="btn btn-info text-light" ng-disabled="loading.search">
                            <i ng-if="!loading.search" class="fa fa-print"></i>
                            <i ng-if="loading.search" class="fa fa-spinner fa-spin"></i>
                            In
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết</h4>
                </div>
                <div class="card-body">
                    <table class="table table-condensed table-bordered table-head-border">
						<thead class="sticky-thead">
							<tr>
								<th>STT</th>
                                <th>Số hóa đơn</th>
								<th>Ngày hóa đơn</th>
                                <th>Giá trị hóa đơn</th>
								<th>Giá trị KM</th>
								<th>Khách hàng</th>
								<th>Xe</th>
								<th>Gara</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-if="loading.search">
								<td colspan="8"><i class="fa fa-spin fa-spinner"></i> Đang tải dữ liệu</td>
							</tr>
							<tr ng-if="!loading.search && details && !details.length">
								<td colspan="8">Chưa có dữ liệu</td>
							</tr>
							<tr ng-if="!loading.search && details && details.length" ng-repeat="d in details">
								<td class="text-center"><% $index + 1 + (current.page - 1) * per_page %></td>
                                <td><% d.code %></td>
								<td><% d.bill_date | toDate | date:'dd/MM/yyyy HH:mm' %></td>
								<td class="text-right"><% d.cost_after_sale | number %></td>
								<td class="text-right"><% d.promo_value | number %></td>
								<td><% d.customer_name %></td>
								<td><% d.license_plate %></td>
								<td><% d.g7 %></td>
							</tr>
							<tr ng-if="!loading.search && details && details.length">
								<td class="text-center" colspan="3"><b>Tổng cộng</b></td>
								<td class="text-right"><b><% summary.cost_after_sale | number %></b></td>
								<td class="text-right"><b><% summary.promo_value | number %></b></td>
								<td colspan="3"></td>
							</tr>
						</tbody>
					</table>
					<div class="text-right mt-2">
						<ul uib-pagination ng-change="pageChanged()" total-items="total_items" ng-model="current.page" max-size="10"
							class="pagination-sm" boundary-links="true" items-per-page="per_page" previous-text="‹" next-text="›" first-text="«" last-text="»">
						</ul>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('libs/pagination/ui-bootstrap.min.js') }}"></script>
<script>
    angular.module("App").requires.push('ui.bootstrap');
    app.controller('Warehouse', function ($scope) {
        $scope.form = {};
		$scope.details = [];
		$scope.promo_campaigns = @json(App\Model\Common\PromoCampaign::getForSelect());
		$scope.customers = @json(App\Model\Common\Customer::getForSelect());
		$scope.g7s = @json(App\Model\Uptek\G7Info::getForSelect());

        @include('g7.warehouse_reports.js')

		$scope.filter = function(page = 1) {
			draw++;
			$scope.current.page = page;
			$scope.loading.search = true;
			$.ajax({
                type: 'GET',
                url: "{{ route('Report.promoReportSearchData') }}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {
					...$scope.form,
					per_page: $scope.per_page,
					current_page: $scope.current.page,
					draw: draw
				},
                success: function(response) {
                    if (response.success && response.draw == draw) {
						$scope.details = response.data.data;
						$scope.total_items = response.data.total;
						$scope.current.page = response.data.current_page;
						$scope.summary = response.summary;
					}
				},
				error: function(err) {
					toastr.error('Đã có lỗi xảy ra');
				},
				complete: function() {
					$scope.loading.search = false;
					$scope.$applyAsync();
				}
            });
        }

		$scope.filter(1);

		$scope.pageChanged = function() {
			$scope.filter($scope.current.page);
		};

		function getFilterParams() {
			return $.param($scope.form);
		}

		$scope.printURL = function() {
            return `{{ route('Report.promoReportPrint') }}?${getFilterParams()}`;
        }
    })
</script>
@endsection
