<div class="modal fade" id="create-product-category" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="createProductCategory">
    <form method="POST" role="form" id="createProductCategoryForm">
        @csrf
        <div class="modal-dialog">
            @include('common.product_categories.form')
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>
@include('common.product_categories.ProductCategory')
<script>
    let createProductCategoryCallback;
    app.controller('createProductCategory', function ($scope, $rootScope, $http) {
        $scope.form = new ProductCategory({}, {scope: $scope});
        $scope.loading = {};

        console.log($scope.form.categories);

        $(document).on('shown.bs.modal', '#create-product-category', function() {
            document.getElementById("createProductCategoryForm").reset();
        })

        // Submit Form tạo mới
        $scope.submit = function () {
            let url = "{!! route('ProductCategory.store') !!}";;
            $scope.loading.submit = true;
            // return 0;
            $.ajax({
                type: "POST",
                url: url,
                data: $scope.form.submit_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        $('#create-product-category').modal('hide');
                        $scope.form = new CustomerLevel({}, {scope: $scope});
                        toastr.success(response.message);
                        if(createProductCategoryCallback) createProductCategoryCallback(response.data);
                        $rootScope.$emit("createdProductCategory", response.data);
                        $scope.errors = null;
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
</script>