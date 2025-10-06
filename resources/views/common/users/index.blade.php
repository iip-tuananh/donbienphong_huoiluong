@extends('layouts.main')

@section('css')
@endsection

@section('title')
Danh sách người dùng
@endsection


@section('buttons')
{{--<a href="{{ route('User.exportExcel') }}" class="btn btn-primary"><i class="fas fa-file-excel"></i> Xuất file excel</a>--}}
{{--<a href="{{ route('User.exportPDF') }}" class="btn btn-warning"><i class="far fa-file-pdf"></i> Xuất file pdf</a>--}}
@endsection


@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<table id="table-list">
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script>
	new DATATABLE('table-list', {
		ajax: {
			url: '/admin/common/users/searchData',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
			{data: 'name', title: "Tên"},
			{data: 'email', title: "Email"},
			{
				data: 'status',
				title: "Trạng thái",
			},
			{data: 'created_by', title: "Người tạo"},
			{data: 'created_at', title: "Ngày tạo"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: "name", search_type: "text", placeholder: "Tên"},
			{data: "email", search_type: "text", placeholder: "Email"},
			{
				data: "status", search_type: "select", placeholder: "Trạng thái",
				column_data: [{id: 1, name: "Hoạt động"}, {id: 0, name: "Khóa"}],
			},
			// {
			// 	data: "type", search_type: "select", placeholder: "Loại",
			// 	column_data: USER_TYPES,
			// },
		],
        {{--create_link: "{{ route('User.create') }}"--}}
	});

</script>
@include('partial.confirm')
@endsection
