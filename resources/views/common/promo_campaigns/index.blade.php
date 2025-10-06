@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý chương trình khuyến mãi
@endsection

@section('content')
<div ng-cloak>
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
</div>
@endsection
@section('script')
<script>
	let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('PromoCampaign.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT"},
			{data: 'name', title: 'Tên'},
			{data: 'code', title: 'Mã'},
			{data: 'start_date', title: 'Từ ngày'},
			{data: 'end_date', title: 'Đến ngày'},
			{data: 'status',title: "Trạng thái"},
			{data: 'updated_at', title: "Ngày cập nhật"},
			{data: 'updated_by', title: "Người cập nhật"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tên"},
			{data: 'code', search_type: "text", placeholder: "Mã"},
			{
				data: 'status', search_type: "select", placeholder: "Trạng thái",
				column_data: @json(App\Model\Common\PromoCampaign::STATUSES)
			}
		],
		search_by_time: false,
		create_link: "{{ route('PromoCampaign.create') }}"
	})
</script>
@include('partial.confirm')
@endsection
