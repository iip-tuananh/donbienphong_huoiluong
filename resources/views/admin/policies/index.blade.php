@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý chính sách
@endsection

@section('title')
    Quản lý chính sách
@endsection

@section('buttons')
<a href="{{route('policies.create')}}" class="btn btn-outline-success"  class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Policy">
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
@include('admin.policies.Policy')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('policies.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'title', title: 'Tiêu đề'},
            {data: 'status', title: 'Trạng thái'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "nhập tiêu đề"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

</script>


@include('partial.confirm')
@endsection
