@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý dự án
@endsection

@section('page_title')
Quản lý dự án
@endsection

@section('content')
<div ng-cloak>
	<div class="row" ng-controller="Project">
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
</div>
@endsection
@section('script')
<script>
    let columns = [
        {data: 'DT_RowIndex', orderable: false, title: "STT"},
        {
            data: 'image', title: "Hình ảnh", orderable: false, className: "text-center",
        },
        {data: 'name',title: 'Tiêu đề'},
        {data: 'created_by',title: 'Người tạo'},
        {data: 'updated_by',title: 'Người cập nhật'},
        {data: 'updated_at',title: 'Ngày cập nhật'},
        {
            data: 'status',
            title: "Trạng thái",
            render: function (data) {
                return getStatus(data, @json(App\Model\Admin\Service::STATUSES));
            },
            className: 'text-center'
        },
        {data: 'action', orderable: false, title: "Hành động"}
    ];

    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{{route('projects.searchData')}}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: columns,
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tiêu đề"},
            {
                data: 'status', search_type: "select", placeholder: "Trạng thái",
                column_data: @json(App\Model\Admin\Post::STATUSES)
            },
        ],
        search_by_time: false,
        @if(Auth::user()->type == App\Model\Common\User::SUPER_ADMIN || Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN)
        create_link: "{{ route('projects.create') }}"
        @endif
    }).datatable;

    app.controller('Project', function ($scope, $rootScope, $http) {

    })
</script>
@include('partial.confirm')
@endsection
