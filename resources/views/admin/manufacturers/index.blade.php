@extends('layouts.main')

@section('css')
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
          rel="stylesheet"/>
@endsection

@section('page_title')
Quản lý danh mục hãng sản xuất
@endsection

@section('title')
    Quản lý danh mục hãng sản xuất
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-manufacturer" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Manufactures">
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
        @include('admin.manufacturers.create')
        @include('admin.manufacturers.edit')

    </div>

</div>
@endsection

@section('script')

    <script type="text/javascript"
            src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

@include('admin.manufacturers.Manufacturer')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '{!! route('manufacturers.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columnDefs: [
            {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            }
        ],
        select: {
            'style': 'multi'
        },
        columns: [
            {data: 'id', orderable: false},
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'code', title: 'Mã'},
            {data: 'name', title: 'Tên hãng sản xuất'},
            {data: 'image', title: 'Ảnh đại diện'},
            {data: 'products', title: 'Sản phẩm'},
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tên hãng"},
        ],
        act: true,
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('Manufactures', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.categories = @json(\App\Model\Admin\Category::getForSelect2());
        $scope.form = {}

        $('#table-list').on('click', '.edit', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();

            $.ajax({
                type: 'GET',
                url: "/admin/manufacturers/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;

                        $rootScope.$emit("editManufacturer", $scope.booking);
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
            $('#edit-manufacturer').modal('show');
        });

    })

    function removeProductArr() {
        var manufacturers_remove_ids = [];
        var rows_selected = datatable.column(0).checkboxes.selected();

        $.each(rows_selected, function (index, rowId) {
            manufacturers_remove_ids.push(rowId);
        });

        if(manufacturers_remove_ids.length == 0) {
            toastr.warning("Chưa có hãng sản xuất nào được chọn");
            return;
        }

        var manu_ids = manufacturers_remove_ids.join(',');

        swal({
            title: "Xác nhận xóa!",
            text: "Bạn chắc chắn muốn xóa "+manufacturers_remove_ids.length+" hãng sản xuất",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Xác nhận",
            cancelButtonText: "Hủy",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: 'GET',
                    url: "/admin/manufacturers/check-act-delete/?manu_ids="+manu_ids,
                    success: function(response) {
                        if (response.check) {
                            window.location.href = "{{route('manufacturers.delete.multi')}}?manu_ids="+manu_ids;
                        } else {
                            toastr.warning("Tồn tại hãng sản xuất có sản phẩm, không thể xóa !");
                        }
                    },
                    error: function(e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function() {
                    }
                });

                {{--window.location.href = "{{route('manufacturers.delete.multi')}}?manu_ids="+manu_ids;--}}
            }
        })
    }

    $(document).on('click', '.export-button', function(event) {
        event.preventDefault();
        let data = {};
        mergeSearch(data, datatable.context[0]);
        window.location.href = $(this).data('href') + "?" + $.param(data);
    })
</script>
@include('partial.confirm')
@endsection
