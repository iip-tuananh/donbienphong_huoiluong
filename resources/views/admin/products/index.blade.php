@extends('layouts.main')

@section('css')
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
          rel="stylesheet"/>
@endsection

@section('page_title')
    Quản lý hàng hóa
@endsection

@section('title')
    Quản lý hàng hóa
@endsection

@section('buttons')
    @if(Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN || Auth::user()->type == App\Model\Common\User::SUPER_ADMIN)
        <a href="{{ route('Product.create') }}" class="btn btn-outline-success btn-sm" class="btn btn-info"><i
                class="fa fa-plus"></i> Thêm mới</a>
        {{-- <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportExcel') }}" class="btn btn-info export-button btn-sm"><i class="fas fa-file-excel"></i> Xuất file excel</a>
        <a href="javascript:void(0)" target="_blank" data-href="{{ route('Product.exportPDF') }}" class="btn btn-warning export-button btn-sm"><i class="far fa-file-pdf"></i> Xuất file pdf</a> --}}
    @endif
@endsection

@section('content')
    <div>
        <div class="row" ng-controller="Product">
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

                                                <ui-select remove-selected="false" multiple ng-model="product.category_special_ids">
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

    @include('admin.products.Product')
    <script>
        let datatable = new DATATABLE('table-list', {
            ajax: {
                url: '/admin/products/searchData',
                data: function (d, context) {
                    DATATABLE.mergeSearch(d, context);
                }
            },

            columns: [
                {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
                {
                    data: 'code',
                    title: 'Sản phẩm',
                    className: 'text-start',
                    render: function(code, type, row) {
                        if (type === 'display') {
                            return `
        <div class="d-flex align-items-center">
          <img src="${row.image ? row.image.path :  ''}"
               alt="${row.name}"
               style="width:60px; height:60px; object-fit:cover; border-radius:4px; margin-right:8px;">
          <div>
            <div class="fw-semibold">${row.name}</div>
            <div class="text-muted" style="font-size:0.9em;">${code}</div>
          </div>
        </div>`;
                        }
                        return code;
                    }
                },
                {data: 'price', title: "Đơn giá bán"},
                {data: 'cate_id', title: 'Danh mục'},
                {data: 'category_special', title: 'Danh mục đặc biệt'},
                {
                    data: 'status',
                    title: "Trạng thái",
                    render: function (data) {
                        if (data == 0) {
                            return `<span class="badge badge-danger">Nháp</span>`;
                        } else {
                            return `<span class="badge badge-success">Xuất bản</span>`;
                        }
                    },
                    className: "text-center"
                },
                {data: 'action', orderable: false, title: "Hành động"}
            ],
            search_columns: [
                {data: 'name', search_type: "text", placeholder: "Tên hoặc mã sản phẩm"},
                {
                    data: 'status', search_type: "select", placeholder: "Trạng thái",
                    column_data: [{id: 1, name: "Xuất bản"}, {id: 0, name: "Nháp"}]
                },
                {
                    data: 'cate_id', search_type: "select", placeholder: "Danh mục",
                    column_data: @json(App\Model\Admin\Category::getForSelect())
                },
                {
                    data: 'cate_special_id', search_type: "select", placeholder: "Danh mục đặc biệt",
                    column_data: @json(App\Model\Admin\CategorySpecial::getForSelectForProduct())
                }
            ],
        }).datatable;

        app.controller('Product', function ($scope, $rootScope, $http) {
            $scope.units = @json(App\Model\Common\Unit::all());
            $scope.categories = @json(App\Model\Common\ProductCategory::all());
            $scope.categorieSpeicals = @json(\App\Model\Admin\CategorySpecial::getForSelectForProduct());
            $scope.loading = {};
            $scope.arrayInclude = arrayInclude;

            $rootScope.$on("createdProductCategory", function (event, data) {
                $scope.formEdit.all_categories.push(data);
                $scope.formEdit.product_category_id = data.id;
                $scope.$applyAsync();
            });

            // Show hàng hóa
            $('#table-list').on('click', '.show-product', function () {
                $scope.data = datatable.row($(this).parents('tr')).data();
                $scope.formEdit = new Product($scope.data, {scope: $scope});
                $scope.$apply();
                $('#show-modal').modal('show');
            });

            // Sửa hàng hóa
            $('#table-list').on('click', '.edit', function () {
                $scope.data = datatable.row($(this).parents('tr')).data();
                $scope.formEdit = new Product($scope.data, {scope: $scope});

                createUnitCallback = (response) => {
                    $scope.formEdit.all_units.push(response);
                    $scope.formEdit.unit_id = response.id;
                }

                $scope.errors = null;
                $scope.$apply();
                $('#edit-modal').modal('show');
            });

            // Submit mode mới
            $scope.submit = function () {
                $scope.loading.submit = false;
                $.ajax({
                    type: 'POST',
                    url: "/uptek/products/" + $scope.formEdit.id + "/update",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    processData: false,
                    contentType: false,
                    data: $scope.formEdit.submit_data,
                    success: function (response) {
                        if (response.success) {
                            $('#edit-modal').modal('hide');
                            toastr.success(response.message);
                            if (datatable.datatable) datatable.ajax.reload();
                            $scope.errors = null;
                        } else {
                            toastr.warning(response.message);
                            $scope.errors = response.errors;
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });
            }

            $('#table-list').on('click', '.add-category-special', function () {
                event.preventDefault();
                $scope.data = datatable.row($(this).parents('tr')).data();
                $.ajax({
                    type: 'GET',
                    url: "/admin/products/" + $scope.data.id + "/getData",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: $scope.data.id,

                    success: function (response) {
                        if (response.success) {
                            $scope.product = response.data;
                            console.log($scope.product);
                        } else {
                            toastr.warning(response.message);
                        }
                        $scope.$applyAsync();
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });

                $('#add-to-category-special').modal('show');
            })

            $scope.submit = function () {
                let url = "/admin/products/add-category-special";
                $scope.loading.submit = true;
                console.log($scope.product.category_special_ids);
                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: {
                        product_id: $scope.product.id,
                        category_special_ids: $scope.product.category_special_ids
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#add-to-category-special').modal('hide');
                            toastr.success(response.message);
                            datatable.ajax.reload();
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    },
                });
            }

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
                text: "Bạn chắc chắn muốn xóa "+product_remove_ids.length+" sản phẩm",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Xác nhận",
                cancelButtonText: "Hủy",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "{{route('products.delete.multi')}}?product_ids="+product_ids;
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
