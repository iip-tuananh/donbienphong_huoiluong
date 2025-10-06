@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý đặt lịch
@endsection

@section('title')
Quản lý đặt lịch
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal"  data-target="#create-booking" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="javascript:void(0)"  data-href="{{ route('Booking.exportExcel') }}" class="btn btn-info export-button"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="javascript:void(0)"  data-href="{{ route('Booking.exportPDF') }}" class="btn btn-warning export-button"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Bookings">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

    </div>
    {{-- Form tạo mới --}}
    @include('common.bookings.create_booking')
    @include('common.bookings.edit_booking')
</div>
@endsection

@section('script')
@include('partial.classes.common.Booking')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('Booking.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'customer', title: 'Khách hàng'},
            {data: 'mobile', title: 'Số điện thoại'},
            {data: 'booking_time', title: 'Giờ hẹn'},
            {data: 'note', title: 'Ghi chú'},
            @if(Auth::user()->type == 1)
            {data: 'g7', title: 'Gara nhận'},

            {
				data: 'status',
				title: "Trạng thái",
				render: function (data) {
					return getStatus(data, @json(App\Model\Common\Booking::STATUSES));
				}
			},
            @endif
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'customer', search_type: "text", placeholder: "Khách hàng"},
            {data: 'mobile', search_type: "text", placeholder: "Điện thoại khách"},
            @if(Auth::user()->type == 1)
            {
                data: 'g7_id', search_type: "select", placeholder: "Gara",
                column_data: @json(App\Model\Uptek\G7Info::getForSelect())
            },
            @endif
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: @json(App\Model\Common\Booking::STATUSES)
			}
		],
		search_by_time: true,
	}).datatable;

    createBookingCallback = (response) => {
        datatable.ajax.reload();
    }
    app.controller('Bookings', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}


        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/common/bookings/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                if (response.success) {
                    $scope.booking = response.data;
                    $rootScope.$emit("editBooking", $scope.booking);
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

        // show phiếu chi
        $('#table-list').on('click', '.show', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
			$.ajax({
                type: 'GET',
                url: "/g7/funds/receipt-vouchers/" + $scope.data.id + "/show",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,
                success: function(response) {
                if (response.success) {
					$scope.form.data = response.data;
                    $scope.form.payer_type_name = response.payer_type_name;
                    $('#show-modal').modal('show');
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
        });
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
