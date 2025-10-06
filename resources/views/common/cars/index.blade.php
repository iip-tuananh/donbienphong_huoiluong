@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý xe
@endsection

@section('page_title')
Quản lý xe
@endsection

@section('buttons')
<a href="{{ route('Car.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#createCar" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('Car.exportExcel') }}" class="btn btn-info"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('Car.exportPDF') }}" class="btn btn-warning"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Cars">
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
        @include('common.cars.edit')
        {{-- Form show --}}
        @include('common.cars.show')
    </div>
</div>
{{-- Form tạo mới --}}
@include('common.cars.createCar')
@include('common.customers.createCustomer')
@include('common.license_plates.create_license_plate')
@include('common.customer_groups.createCustomerGroup')
@endsection
@section('script')
@include('partial.classes.common.Customer');
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('Car.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'license_plate', title: 'Biển số'},
            {data: 'customers', title: 'Chủ xe'},
            // {
            //     data: 'customers', orderable: false, title: 'Chủ xe',
            //     render: function(data) {
            //         return data ? data.name : '';
            //     }
            // },
            {data: 'manufact', title: 'Hãng sx'},
            {data: 'type', title: 'Loại xe'},
            {data: 'category', title: 'Dòng xe'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					return getStatus(data, @json(App\Model\Common\Car::STATUSES));
				}
			},
            {data: 'category_id', visible: false},
            {data: 'manufact_id', visible: false},
            {data: 'type_id', visible: false},
			// {data: 'updated_at', title: "Ngày cập nhật"},
			// {data: 'updated_by', title: "Người cập nhật"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'license_plate', search_type: "text", placeholder: "Biển số"},
            @if(Auth::user()->type == \App\Model\Common\User::SUPER_ADMIN || Auth::user()->type == \App\Model\Common\User::QUAN_TRI_VIEN)
            {
				data: 'g7_id', search_type: "select", placeholder: "Gara quản lý",
				column_data:  @json(App\Model\Uptek\G7Info::getForSelect()),
			},
            @endif
            {
				data: 'customer_mobile', search_type: "text", placeholder: "SĐT hoặc tên khách"
			},
			{
				data: 'manufact', search_type: "select", placeholder: "Hãng xe",
				column_data:  @json(App\Model\Common\VehicleManufact::where('status',1)->get()),
			},
            {
				data: 'type', search_type: "select", placeholder: "Loại xe",
				column_data:  @json(App\Model\Common\VehicleType::where('status',1)->get()),
			},
            {
				data: 'category', search_type: "select", placeholder: "Dòng xe",
				column_data:  @json(App\Model\Common\VehicleCategory::where('status',1)->get()),
			},
            {
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}]
			}
		],
		search_by_time: false,
		// create_link: "{{ route('VehicleCategory.create') }}"
	}).datatable;

    createCarCallback = (response) => {
        datatable.ajax.reload();
    }

    createLicensePlateCallback = (response) => {
        $('select[name="license_plate_id"]').append('<option value="' + response.data.id + '">' + response.data.license_plate + '</option>');
        $('select[name="license_plate_id"]').val(response.data.id).trigger('change');
    }

    app.controller('Cars', function ($scope, $http) {
        $scope.form = {};
        // $scope.arrayInclude = arrayInclude;
        // $scope.customers = @json(\App\Model\Common\Customer::select('id','name')->get());
        // $scope.categories = @json(\App\Model\Common\VehicleCategory::select('id','name')->get());
        // $scope.manufacts = @json(\App\Model\Common\VehicleManufact::select('id','name')->get());
        // $scope.license_plates = @json(\App\Model\Common\LicensePlate::getForSelect());
        // $scope.statuses = @json(\App\Model\Common\Car::STATUSES);

        createCustomerCallback = (response) => {
            $scope.$emit("addCustomer", response);
        }

        @include('common.cars.formJs')
        // Show
        $('#table-list').on('click', '.show', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            sendRequest({
                type: "GET",
                url: "/common/cars/" + $scope.data.id + "/getDataForShow",
                success: function(response) {
                    if (response.success) {
                        $scope.form = new Car(response.data, {scope: $scope});
                        $scope.getCarTypes($scope.form.manufact_id);
                        $scope.errors = null;
                        $scope.$apply();
                        $('#show-modal').modal('show');
                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
                    }
                }
            }, $scope);
        });

        // Sửa
        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $scope.$apply();

            sendRequest({
                type: "GET",
                url: "/common/cars/" + $scope.data.id + "/getDataForShow",
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('show');
                        $scope.form = new Car(response.data, {scope: $scope});
                        $scope.getCarTypes($scope.form.manufact_id);
                        $scope.errors = null;
                        $scope.$apply();

                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
                    }
                }
            }, $scope);
        });
        // Submit mode mới
        $scope.submit = function() {
            $scope.loading.submit = true;
            $.ajax({
                type: 'POST',
                url: "/common/cars/" + $scope.form.id + "/update",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.form.submit_data,
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
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
        @include('partial.modal.vehicleJs')
    })

</script>
@include('partial.confirm')
@endsection