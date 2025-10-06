@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý khối nội dung HTML
@endsection

@section('page_title')
Quản lý khối nội dung HTML
@endsection

@section('buttons')
{{--<a href="{{ route('Block.create') }}" class="btn btn-outline-success btn-sm" class="btn btn-info"><i class="fa fa-plus"></i> Thêm mới</a>--}}
@endsection

@section('content')
<div ng-cloak>
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
    </div>
</div>
@endsection

@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
		ajax: {
			url: '{!! route('Block.searchData') !!}',
			data: function (d, context) {
				DATATABLE.mergeSearch(d, context);
			}
		},
		columns: [
			{data: 'DT_RowIndex', orderable: false, title: "STT", className: "text-center"},
            {data: 'name', title: 'Tên'},
            {data: 'created_at', title: "Ngày tạo", className: "text-center"},
            {data: 'created_by', title: "Người tạo", className: "text-center"},
			{data: 'action', orderable: false, title: "Hành động"}
		],
		search_columns: [
			{data: 'name', search_type: "text", placeholder: "Tìm theo tên"},
		],
	}).datatable;

    app.controller('Product', function ($scope, $rootScope, $http) {
        $scope.units = @json(App\Model\Common\Unit::all());
        $scope.categories = @json(App\Model\Common\ProductCategory::all());
        $scope.loading = {};

        $rootScope.$on("createdProductCategory", function (event, data){
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
        $scope.submit = function() {
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
                success: function(response) {
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
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                }
            });
        }
    })
    $(document).on('click', '.export-button', function(event) {
        event.preventDefault();
        let data = {};
        mergeSearch(data, datatable.context[0]);
        window.location.href = $(this).data('href') + "?" + $.param(data);
    })

</script>
@include('partial.confirm')
@endsection
