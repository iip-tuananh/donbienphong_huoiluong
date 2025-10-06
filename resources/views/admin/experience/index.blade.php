@extends('layouts.main')

@section('css')
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
          rel="stylesheet"/>
@endsection

@section('page_title')
    Danh sách hạng mục trải nghiệm
@endsection

@section('title')
    Danh sách hạng mục trải nghiệm
@endsection

@section('buttons')
    @if(Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN || Auth::user()->type == App\Model\Common\User::SUPER_ADMIN)
        <a href="{{ route('experience.create') }}" class="btn btn-outline-success btn-sm" class="btn btn-info"><i
                class="fa fa-plus"></i> Thêm mới</a>
        {{-- <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportExcel') }}" class="btn btn-info export-button btn-sm"><i class="fas fa-file-excel"></i> Xuất file excel</a>
        <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportPDF') }}" class="btn btn-warning export-button btn-sm"><i class="far fa-file-pdf"></i> Xuất file pdf</a> --}}
    @endif
@endsection

@section('content')
    <div>
        <div class="row" ng-controller="Experience">
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


    @include('common.units.createUnit')
@endsection

@section('script')
    <script type="text/javascript"
            src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

    @include('admin.experience.Experience')
    <script>
        let datatable = new DATATABLE('table-list', {
            ajax: {
                url: '/admin/experience/searchData',
                data: function (d, context) {
                    DATATABLE.mergeSearch(d, context);
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
                {data: 'name', title: 'Hạng phòng'},
                {data: 'updated_at', title: 'Ngày cập nhật'},
                {data: 'updated_by', title: 'Người cập nhật'},
                {data: 'action', orderable: false, title: "Hành động"}
            ],
            search_columns: [
                {data: 'name', search_type: "text", placeholder: "Trải nghiệm"},
            ],
            act: true,
        }).datatable;

        app.controller('Experience', function ($scope, $rootScope, $http) {
            $scope.loading = {};
            $scope.arrayInclude = arrayInclude;


        })

        function removeProductArr() {
            var product_remove_ids = [];
            var rows_selected = datatable.column(0).checkboxes.selected();

            $.each(rows_selected, function (index, rowId) {
                product_remove_ids.push(rowId);
            });

            if(product_remove_ids.length == 0) {
                toastr.warning("Chưa có sản phẩm nào được chọn");
                return;
            }

            var product_ids = product_remove_ids.join(',');
            swal({
                title: "Xác nhận xóa!",
                text: "Bạn chắc chắn muốn xóa "+product_remove_ids.length+" bản ghi",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Xác nhận",
                cancelButtonText: "Hủy",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "{{route('Room.delete.multi')}}?room_ids="+product_ids;
                }
            })
        }

        $(document).on('click', '.export-button', function (event) {
            event.preventDefault();
            let data = {};
            mergeSearch(data, datatable.context[0]);
            window.location.href = $(this).data('href') + "?" + $.param(data);
        })

    </script>
    @include('partial.confirm')
@endsection
