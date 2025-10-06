@extends('layouts.main')

@section('css')
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
          rel="stylesheet"/>
@endsection

@section('page_title')
    Danh sách hạng phòng
@endsection

@section('title')
    Danh sách hạng phòng
@endsection

@section('buttons')
    @if(Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN || Auth::user()->type == App\Model\Common\User::SUPER_ADMIN)
        <a href="{{ route('Room.create') }}" class="btn btn-outline-success btn-sm" class="btn btn-info"><i
                class="fa fa-plus"></i> Thêm mới</a>
        {{-- <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportExcel') }}" class="btn btn-info export-button btn-sm"><i class="fas fa-file-excel"></i> Xuất file excel</a>
        <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportPDF') }}" class="btn btn-warning export-button btn-sm"><i class="far fa-file-pdf"></i> Xuất file pdf</a> --}}
    @endif
@endsection

@section('content')
    <div>
        <div class="row" ng-controller="Tour">
            <div class="col-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="table-list">
                        </table>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="add-to-category-special" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="semi-bold">Thêm vào danh mục đặc biệt</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group custom-group" ng-cloak>
                                                <label class="form-label required-label">Danh mục đặc biệt</label>

                                                <ui-select remove-selected="false" multiple ng-model="tour.category_special_ids">
                                                    <ui-select-match placeholder="Chọn danh mục đặc biệt">
                                                        <% $item.name %>
                                                    </ui-select-match>
                                                    <ui-select-choices
                                                        repeat="item.id as item in (categorieSpeicals | filter: $select.search)">
                                                        <span ng-bind="item.name"></span>
                                                    </ui-select-choices>
                                                </ui-select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success btn-cons" ng-click="submit()"
                                    ng-disabled="loading.submit">
                                <i ng-if="!loading.submit" class="fa fa-save"></i>
                                <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                                Lưu
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                    class="fas fa-window-close"></i> Hủy
                            </button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

        </div>
    </div>


    @include('common.units.createUnit')
@endsection

@section('script')
    <script type="text/javascript"
            src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

    @include('admin.rooms.Room')
    <script>
        let datatable = new DATATABLE('table-list', {
            ajax: {
                url: '/admin/rooms/searchData',
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
                {data: 'name', title: 'Hạng phòng'},
                {data: 'area', title: 'Diện tích'},
                {data: 'view', title: 'View'},
                {data: 'bedrooms', title: 'Phòng ngủ'},
                {
                    data: 'status',
                    title: "Trạng thái",
                    render: function (data) {
                        if (data == 2) {
                            return `<span class="badge badge-danger">Đang chờ</span>`;
                        } else {
                            return `<span class="badge badge-success">Xuất bản</span>`;
                        }
                    },
                    className: "text-center"
                },
                {data: 'action', orderable: false, title: "Hành động"}
            ],
            search_columns: [
                {data: 'name', search_type: "text", placeholder: "Hạng phòng"},
                {
                    data: 'status', search_type: "select", placeholder: "Trạng thái",
                    column_data: [{id: 1, name: "Xuất bản"}, {id: 2, name: "Đang chờ"}]
                },
            ],
            act: true,
        }).datatable;

        app.controller('Tour', function ($scope, $rootScope, $http) {
            $scope.categories = @json(\App\Model\Admin\Category::all());
            $scope.categorieSpeicals = @json(\App\Model\Admin\CategorySpecial::getForSelectForProduct());
            $scope.loading = {};
            $scope.arrayInclude = arrayInclude;

            $rootScope.$on("createdProductCategory", function (event, data) {
                $scope.formEdit.all_categories.push(data);
                $scope.formEdit.product_category_id = data.id;
                $scope.$applyAsync();
            });

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
