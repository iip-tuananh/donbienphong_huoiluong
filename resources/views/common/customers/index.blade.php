@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý khách hàng
@endsection

@section('buttons')
<a class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#createCustomer" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="javascript:void(0)" target="_blank" data-href="{{ route('Customer.exportExcel') }}" class="btn btn-info export-button"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="javascript:void(0)" target="_blank" data-href="{{ route('Customer.exportPDF') }}" class="btn btn-warning export-button"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="customer">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>
        {{-- Form sửa --}}
        @include('common.customers.edit_customer')
        @include('common.customers.show')

    </div>
</div>
{{-- Form tạo mới --}}
@include('common.customers.createCustomer')
{{-- Form tạo mới nhóm khách --}}
@include('common.customer_groups.createCustomerGroup')
@endsection

@section('script')
@include('partial.classes.common.Customer')
<script>

    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('Customer.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name', title: 'Tên'},
            // {data: 'birth_day', title: 'Sinh nhật'},
            {data: 'mobile', title: 'Điện thoại'},
            {data: 'level', title: 'Hạng khách'},
            // {data: 'email', title: 'Email'},
            {data: 'full_adress', title: 'Địa chỉ'},
            {data: 'g7_id', title: 'Gara quản lý'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					if (data == 0) {
						return `<span class="badge badge-danger">Khóa</span>`;
					} else {
						return `<span class="badge badge-success">Hoạt động</span>`;
					}
				}
			},
			// {data: 'updated_at', title: "Ngày cập nhật"},
			// {data: 'updated_by', title: "Người cập nhật"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name_mobile', search_type: "text", placeholder: "Tên hoặc SĐT khách"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}]
			}
		],
		search_by_time: false,
		// create_link: "{{ route('VehicleCategory.create') }}"
	}).datatable;

    $(document).on('shown.bs.modal', '#createCustomer', function() {
        document.getElementById("createCustomerForm").reset();
    })

    createCustomerCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('customer', function ($scope, $rootScope, $http) {
        $scope.form = {};
        $scope.loading = {};
        $scope.customer_groups = @json(\App\Model\Common\CustomerGroup::getForSelect());

        $rootScope.$on("CreateCustomerGroup", function (event, data){
            $scope.customer_groups.push(data);
            $scope.form.customer_group_id = data.id;
        });

        @include('common.customers.formJs')

        // Show Khách hàng
        $('#table-list').on('click', '.show', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            sendRequest({
                type: "GET",
                url: "/common/customers/" + $scope.data.id + "/getDataForShow",
                success: function(response) {
                    if (response.success) {
                        $scope.form = new Customer(response.data, {scope: $scope});
                        console.log($scope.form.bills);
                        $('#show-customer').modal('show');
                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
                    }
                }
            }, $scope);
        });
        // Sửa khách hàng
        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/common/customers/" + $scope.data.id + "/edit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                if (response.success) {
                    $scope.customer = response.data;
                    $scope.form = new Customer($scope.customer, {scope: $scope})
                    $scope.getDistricts($scope.form.province_id, $scope, "#edit_district_id");
                    $scope.getWards($scope.form.district_id, $scope, "#edit_ward_id");

                    $('#editCustomer').modal('show');
                } else {
                    toastr.warning(response.message);
                    $scope.errors = response.errors;
                }
                },
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                }
            });
            $scope.errors = null;
            $scope.$apply();
            $('#editCustomer').modal('show');
        });

        $scope.submit = function() {
            $scope.loading.submit = true;
            let url = "/common/customers/" + $scope.form.id + "/update";
            let data = $scope.form.submit_data;
            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#editCustomer').modal('hide');
                        datatable.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
                    }
                },
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                }
            });
        }

		$scope.cols = {
			name: "Tên",
			mobile: "Số điện thoại",
			email: "Email",
			gender: "Giới tính",
			birth_day: "Ngày sinh",
			adress: "Địa chỉ",
			status: "Trạng thái",
			customer_group_id: "Nhóm",
			province_id: "Tỉnh",
			district_id: "Huyện",
			ward_id: "Xã"
		};

		$scope.isDate = function(history) {
			return ['birth_day'].includes(history.column_name);
		}

		$scope.isNormal = function(history) {
			return !$scope.isDate(history);
		}

    })
    $(document).on('click', '.export-button', function(event) {
        event.preventDefault();
        let data = {};
        mergeSearch(data, datatable.context[0]);
        window.location.href = $(this).data('href') + "?" + $.param(data);
    })
</script>
@include('partial.confirm')
@endsection
