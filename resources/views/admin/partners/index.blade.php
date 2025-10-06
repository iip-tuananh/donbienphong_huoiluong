@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Thêm bản tin số mới nhất
@endsection

@section('title')
    Thêm bản tin số mới nhất
@endsection

@section('buttons')
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Partner">
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
    {{-- Form tạo mới --}}
    @include('admin.partners.create')
    @include('admin.partners.edit')
</div>
@endsection

@section('script')
@include('admin.partners.Partner')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '/admin/news-digital/searchData',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tiêu đề'},
            {data: 'author', title: 'Tác giả'},
            {data: 'page', title: 'Số trang', className: "text-center"},
            {
                data: 'sort_order',
                title: 'Thứ tự hiển thị',
                className: 'text-center',
                render: function (data, type, row) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            {data: 'updated_at', title: 'Ngày cập nhật'},
            {data: 'updated_by', title: 'Người cập nhật'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tiêu đề"},
        ],
        create_modal_2: true
    }).datatable.on('draw', function () {
        $('#table-list tbody').sortable({
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function (event, ui) {
                let newOrder = [];

                $('#table-list tbody tr').each(function (index) {
                    const rowData = datatable.row(this).data();
                    if (rowData) {
                        newOrder.push({
                            id: rowData.id,
                            sort: index + 1
                        });
                    }
                });

                $.ajax({
                    url: '/admin/news-digital/updateSortOrder',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: {
                        order: newOrder
                    },
                    success: function (res) {
                        datatable.ajax.reload();
                    },
                    error: function () {
                        alert('Có lỗi khi cập nhật thứ tự');
                    }
                });
            }
        }).disableSelection();
    });

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }
    app.controller('Partner', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $(document).on('click', '.create-modal', function () {
            $scope.errors = null;
            $rootScope.$emit("createPartner", $scope.errors, $scope.form);
        })

        $('#table-list').on('click', '.edit', function () {
            $scope.errors = null;

            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/news-digital/" + $scope.data.id + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: $scope.data.id,

                success: function(response) {
                    if (response.success) {
                        $scope.booking = response.data;
                        $rootScope.$emit("editPartner", $scope.booking);
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
            $('#edit-partner').modal('show');
        });
    })
</script>
@include('partial.confirm')
@endsection
