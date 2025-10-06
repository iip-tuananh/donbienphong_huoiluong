@include('partial.classes.uptek.ServiceProduct')
@include('partial.classes.uptek.ServiceVehicleCategory')
<script>
    class Service extends BaseClass {
    all_vehicle_categories = @json(\App\Model\Common\VehicleCategory::getForSelect());

    before(form) {
        this.image = {};
    }

    get image() {
        return this._image;
    }

    set image(value) {
        this._image = new Image(value, this);
    }

    get products() {
        return this._products || []
    }

    set products(value) {
        this._products = (value || []).map(val => new ServiceProduct(val, this));
    }

    addProduct(product) {
        if (!this._products) this._products = [];
        let exist = this.products.find(val => val.product_id == product.product_id);
        if (exist) exist.qty++;
        else this._products.push(new ServiceProduct({product, product_id: product.product_id}, this));
    }

    removeProduct(index) {
        this._products.splice(index, 1);
    }

    get service_vehicle_categories() {
        return this._service_vehicle_categories || []
    }

    set service_vehicle_categories(value) {
        this._service_vehicle_categories = (value || []).map(val => new ServiceVehicleCategory(val, this));
    }

    addVehicleCategory() {
        if (!this._service_vehicle_categories) this._service_vehicle_categories = [];
        this._service_vehicle_categories.push(new ServiceVehicleCategory({}, this));
    }

    removeVehicleCategory(index) {
        this._service_vehicle_categories.splice(index, 1);
    }

    get submit_data() {
        let data = {
            name: this.name,
            service_type_id: this.service_type_id,
            service_vehicle_categories: this.service_vehicle_categories.map(val => val.submit_data),
            products: this.products.map(val => val.submit_data)
        }

        data = jsonToFormData(data);
        let image = this.image.submit_data;
        if (image) data.append('image', image);
        return data;
    }

  }
</script>
