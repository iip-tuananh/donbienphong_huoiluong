@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
    Chi tiết thông tin cộng tác viên
@endsection

@section('title')
    Chi tiết thông tin cộng tác viên
@endsection

@section('buttons')
@endsection

@section('content')

<div ng-controller="Customer" ng-cloak>
    <!-- Container chính -->
    <div class="row g-3">

        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Thông tin cộng tác viên</h6>
                </div>
                <div class="card-body">
                    <!-- Avatar -->
                    <div class="text-center mb-4">
                        <img
                            src="<% form.avatar ? form.avatar.path : '/site/imgs/avatar-default.png' %>"
                            alt="<% form.fullname %>"
                            class="rounded-circle border"
                            style="width:100px; height:100px; object-fit:cover;"
                        >
                    </div>

                    <!-- Thông tin chi tiết -->
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="fw-bold">Cộng tác viên</span>
                            <span><% form.fullname %> – <% form.code %></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="fw-bold">Ngày tham gia</span>
                            <span><% form.date_join %></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0 py-2">
                            <span class="fw-bold">Email</span>
                            <span><% form.email %></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="fw-bold">Trạng thái</span>
                            <select class="form-select form-select-sm w-auto"
                                    ng-model="form.status">
                                <option value="">Chọn trạng thái</option>
                                <option ng-repeat="s in statuses"
                                        ng-value="s.id"
                                        ng-selected="s.id == form.status">
                                    <% s.name %>
                                </option>
                            </select>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="fw-bold">Số bài viết đã đóng góp</span>
                            <span><% form.posts_count %></span>

                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>


    <div class="row">
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

    <hr>

    <div class="text-right">
        <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
            <i ng-if="!loading.submit" class="fa fa-save"></i>
            <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
            Lưu
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-danger btn-cons">
            <i class="fa fa-remove"></i> Quay lại
        </a>
    </div>
</div>
@endsection

@section('script')
    @include('admin.customers.Customer')

    <script>
        app.controller('Customer', function ($scope, $http) {
            $scope.form = new Customer(@json($object), {scope: $scope});
            $scope.statuses = @json(\App\Model\Common\Customer::STATUSES);
            $scope.loading = {};

            $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;

                $.ajax({
                    type: 'POST',
                    url: "/admin/customers/"+$scope.form.id+"/update",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
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
            let datatable = new DATATABLE('table-list', {
                ajax: {
                    url: '/admin/customers/searchDataPost',
                    data: function (d, context) {
                        DATATABLE.mergeSearch(d, context);
                        d.customer_id = {{ $object->id }};
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
                    {data: 'title', title: 'Tiêu đề'},
                    {data: 'created_at', title: 'Ngày gửi'},
                    {data: 'status', title: 'Trạng thái', className: "text-center"},
                    {data: 'updated_by', title: 'Người cập nhật', className: "text-center"},
                    {data: 'action', orderable: false, title: "Hành động", className: "text-center"}
                ],
                search_columns: [
                    {data: 'title', search_type: "text", placeholder: "Tiêu đề"},
                ],
            }).datatable;

        });
    </script>
@endsection
