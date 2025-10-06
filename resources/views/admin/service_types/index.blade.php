@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý loại {{$title}}
@endsection

@section('page_title')
Quản lý loại {{$title}}
@endsection

@section('buttons')
{{-- <a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-service-type" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a> --}}
@endsection

@section('content')
<div  ng-cloak>
	<div class="row" ng-controller="ServiceTypes">
		<div class="col-12">
			<div class="card">
				<!-- /.card-header -->
				<div class="card-body">
					<table id="table-list">
					</table>
				</div>
			</div>
		</div>

        @include('admin.service_types.create')
        @include('admin.service_types.edit')
	</div>
</div>
@endsection
@section('script')
@include('admin.service_types.ServiceType')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{{route('service_types.searchData')}}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
                d.type = '{{$type}}';
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name',title: 'Tên loại'},
            {
                data: 'status',
                title: "Trạng thái",
                render: function (data) {
                    return getStatus(data, @json(App\Model\Admin\ServiceType::STATUSES));
                },
                className: 'text-center'
            },
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên loại"},
            {
                data: 'status', search_type: "select", placeholder: "Trạng thái",
                column_data: @json(App\Model\Admin\ServiceType::STATUSES)
            },
        ],
        create_modal: 'create-service-type',
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('ServiceTypes', function ($scope, $rootScope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();

            $.ajax({
                type: 'GET',
                url: "/admin/service-types/" + $scope.data.id + "/edit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.form = response.data;
                        console.log($scope.form );

                        $rootScope.$emit("editServiceType", $scope.form);
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
            $('#edit-service-type').modal('show');
        });

        $('#table-list').on('click', '.remove', function () {
            var self = this;
            event.preventDefault();
            $scope.data = datatable.row($(this).parents('tr')).data();
            swal({
                title: "Xác nhận xóa!",
                text: "Bạn chắc chắn muốn xóa loại này?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Xác nhận",
                cancelButtonText: "Hủy",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'GET',
                        url: "/admin/service-types/" + $scope.data.id + "/delete",
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        data: $scope.data.id,

                        success: function(response) {
                            if (response.success) {
                                datatable.ajax.reload();
                                swal.close();
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

        });
    })
</script>
@include('partial.confirm')
@endsection
