@extends('layouts.main')

@section('css')
@endsection

@section('title')
Danh sách chức vụ
@endsection

@section('buttons')
<a href="{{ route('Role.create') }}" class="btn btn-outline-success"><i class="fa fa-plus"></i> Thêm mới</a>
{{--<a href="{{ route('Role.create') }}" class="btn btn-primary"><i class="fas fa-file-excel"></i> Xuất file excel</a>--}}
{{--<a href="{{ route('Role.create') }}" class="btn btn-primary"><i class="far fa-file-pdf"></i> Xuất file pdf</a>--}}
@endsection

@section('content')
<div class="row">
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
@endsection
@section('script')
<script>
	    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('Role.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name', title: 'Chức vụ'},
			{data: 'updated_by', title: "Người sửa"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên chức vụ"},
		],
		search_by_time: false,
	})
</script>
@include('partial.confirm')
@endsection
