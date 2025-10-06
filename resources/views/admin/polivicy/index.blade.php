@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý câu hỏi Q&A
@endsection

@section('page_title')
    Quản lý câu hỏi Q&A
@endsection

@section('content')
<div  ng-cloak>
	<div class="row" >
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
        {data: 'title',title: 'Tiêu đề'},
        {data: 'topic', title: 'Topic'},
        {data: 'updated_at', title: 'Ngày cập nhật'},
        {data: 'updated_by', title: 'Người cập nhật'},
        {data: 'action', orderable: false, title: "Hành động"}
    ];

    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{{route('questions.searchData')}}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: columns,
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tiêu đề"},
            {
                data: 'topic_id', search_type: "select", placeholder: "Chủ dề",
                column_data: @json(\App\Model\Admin\Topic::getForSelect())
            },
        ],
        search_by_time: false,
        @if(Auth::user()->type == App\Model\Common\User::SUPER_ADMIN || Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN)
        create_link: "{{ route('questions.create') }}"
        @endif
    }).datatable;

</script>
@include('partial.confirm')
@endsection
