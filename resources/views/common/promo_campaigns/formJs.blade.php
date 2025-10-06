$scope.loading = {};

let product_options = {
    title: "Sản phẩm",
    ajax: {
        url: "{!! route('Product.searchData') !!}",
        data: function (d, context) {
            DATATABLE.mergeSearch(d, context);
            d.status = 1;
        }
    },
    columns: [
        {data: 'DT_RowIndex', orderable: false, title: "STT"},
        {data: 'name', title: "Tên vật tư"},
        {data: 'category', title: "Loại vật tư"},
    ],
    search_columns: [
        {data: 'name', search_type: "text", placeholder: "Tên vật tư"},
        {
            data: 'product_category_id', search_type: "select", placeholder: "Loại vật tư",
            column_data: @json(\App\Model\Common\ProductCategory::getForSelect())
        }
    ]
};

$scope.searchProduct = function(checkpoint) {
	$scope.current_checkpoint = checkpoint;
	$scope.searchProductModal.open();
}

$scope.searchProductModal = new BaseSearchModal(
    product_options,
    function(obj) {
        $scope.chooseProduct(obj);
    }
);

$scope.chooseProduct = function(obj) {
    sendRequest({
        type: 'GET',
        url: `/common/products/${obj.id}/getData`,
        success: function(response) {
            if (response.success) {
                $scope.current_checkpoint.addProduct(response.data);
                toastr.success('Thêm thành công');
                $scope.$applyAsync();
            }
        }
    });
}

$scope.searchCampaignProduct = new BaseSearchModal(
    product_options,
    function(obj) {
        $scope.chooseCampaignProduct(obj);
    }
);

$scope.chooseCampaignProduct = function(obj) {
    sendRequest({
        type: 'GET',
        url: `/common/products/${obj.id}/getData`,
        success: function(response) {
            if (response.success) {
                $scope.form.useProduct(response.data);
                toastr.success('Thêm thành công');
                $scope.$applyAsync();
				$scope.searchCampaignProduct.close();
            }
        }
    });
}
