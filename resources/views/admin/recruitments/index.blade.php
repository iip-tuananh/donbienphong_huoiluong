@extends('layouts.main')

@section('css')
@endsection

@section('title')
    Quản lý tin tuyển dụng
@endsection

@section('page_title')
    Quản lý tin tuyển dụng
@endsection

@section('content')
    <div  ng-cloak>
        <div class="row" ng-controller="Recruitment">
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
        let datatable = new DATATABLE('table-list', {
            ajax: {
                url: '{!! route('recruitments.searchData') !!}',
                data: function (d, context) {
                    DATATABLE.mergeSearch(d, context);
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, title: "STT"},
                {data: 'title',title: 'Tiêu đề'},
                {data: 'salary',title: 'Mức lương'},
                {data: 'address', title: 'Địa điểm'},
                {data: 'expiration_date', title: 'Ngày hết hạn'},
                {data: 'created_at', title: "Ngày cập nhật"},
                {data: 'updated_by', title: "Người cập nhật"},
                {data: 'action', orderable: false, title: "Hành động"}
            ],
            search_columns: [
                {data: 'title', search_type: "text", placeholder: "Tiêu đề tuyển dụng"},
            ],
            search_by_time: false,
            @if(Auth::user()->type == App\Model\Common\User::SUPER_ADMIN || Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN)
            create_link: "{{ route('recruitments.create') }}"
            @endif
        }).datatable;

        app.controller('Recruitment', function ($scope, $rootScope, $http) {

            $scope.categorieSpeicals = @json(\App\Model\Admin\CategorySpecial::getForSelectForPost());
            $scope.arrayInclude = arrayInclude;
            $scope.loading = {};
        })
    </script>
    @include('partial.confirm')
@endsection
