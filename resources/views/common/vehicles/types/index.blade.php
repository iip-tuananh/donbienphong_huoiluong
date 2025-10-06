@extends('layouts.main')

@section('css')
@endsection

@section('title')
Danh sách loại xe
@endsection

@section('page_title')
Danh sách loại xe
@endsection

@section('buttons')
<a href="{{ route('VehicleType.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#createVehicleType" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('VehicleType.exportExcel') }}" class="btn btn-info"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('VehicleType.exportPDF') }}" class="btn btn-warning"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="VehicleType">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
            {{-- Form sửa --}}
            @include('common.vehicles.types.edit')
        </div>
    </div>
    {{-- Form tạo mới --}}
    @include('common.vehicles.types.createVehicleType')
</div>

@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('VehicleType.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name', title: 'Tên'},
            {data: 'manufacts', title: 'Hãng xe'},
            {data: 'category', title: 'Dòng xe'},
            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					return getStatus(data, @json(App\Model\Common\VehicleType::STATUSES));
				}
			},
			{data: 'created_by', title: "Người tạo"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: @json(\App\Model\Common\VehicleType::STATUSES)
			}
		],
		search_by_time: false,
		// create_link: "{{ route('VehicleCategory.create') }}"
	}).datatable;

    createVehicleType = (response) => {
        datatable.ajax.reload();
    }

    app.controller('VehicleType', function ($scope, $http) {
        $scope.form = {};
        $scope.loading = {};

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/vehicle-types/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,
                success: function(response) {
                    if (response.success) {
                        $scope.form = new VehicleType(response.data, {scope: $scope})
                        $('#editVehicleType').modal('show');
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
            $('#editVehicleType').modal('show');
        });

        $scope.submit = function() {
            $scope.loading.submit = true;
            let url = "/vehicle-types/" + $scope.form.id + "/update";
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
                        $('#editVehicleType').modal('hide');
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

    })


</script>
@include('partial.confirm')
@endsection
