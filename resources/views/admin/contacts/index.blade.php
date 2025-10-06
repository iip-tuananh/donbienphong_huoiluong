@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
Quản lý liên hệ
@endsection

@section('title')
    Quản lý liên hệ
@endsection

@section('buttons')
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Contact">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-list">
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="" class="semi-bold">Chi tiết liên hệ</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Người gửi: <% contact.user_name %></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email: <% contact.email %></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">SĐT: <% contact.phone_number %></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày gửi: <% contact.day_send %></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea class="form-control" rows="5"> <% contact.content %></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i>
                            Đóng</button>
                    </div>
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
            url: '{!! route('contacts.searchData') !!}',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'user_name', title: 'Người gửi'},
            {data: 'email', title: 'Email'},
            {data: 'phone_number', title: 'Số điện thoại'},
            {data: 'content', title: 'Nội dung'},
            {data: 'created_at', title: 'Ngày gửi'},
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'user_name', search_type: "text", placeholder: "nhập tên"},
        ],
    }).datatable;

    createReviewCallback = (response) => {
        datatable.ajax.reload();
    }

    app.controller('Contact', function ($rootScope, $scope, $http) {
        $scope.loading = {};
        $scope.form = {}

        $('#table-list').on('click', '.show-detail', function () {
            $scope.data = datatable.row($(this).parents('tr')).data();
            $.ajax({
                type: 'GET',
                url: "/admin/contacts/" + $scope.data.id + "/detail",
                success: function(response) {
                    if (response.success) {
                        $scope.contact = response.data;
                        console.log($scope.contact);
                    } else {
                        toastr.error('Đã có lỗi xảy ra');
                    }
                },
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.$applyAsync();
                }
            });

            $('#modal-detail').modal('show');
        });


    })

</script>
@include('partial.confirm')
@endsection
