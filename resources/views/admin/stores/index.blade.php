@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý danh sách đơn vị thành viên
@endsection

@section('title')
    Quản lý danh sách đơn vị thành viên
@endsection

@section('buttons')
<a href="{{route('stores.create')}}" class="btn btn-outline-success"  class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Stores">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

        {{-- Form tạo mới --}}
{{--        @include('admin.stores.create')--}}
{{--        @include('admin.stores.edit')--}}

    </div>

</div>
@endsection

@section('script')
@include('admin.stores.Store')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('stores.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tên'},
            {data: 'address', title: 'Địa chỉ'},
            {data: 'phone', title: 'SĐT'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "nhập tên đơn vị"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

</script>


@include('partial.confirm')
@endsection
