@include('partial.classes.g7.G7ServiceProduct')
@include('partial.classes.g7.G7ServiceVehicleCategory')
<script>
    class G7Service extends BaseClass {
    all_vehicle_categories = @json(\App\Model\Common\VehicleCategory::getForSelect());
    statuses = @json(\App\Model\G7\G7Service::STATUSES);

    before(form) {
        this.image = {};
    }

    get image() {
        return this._image;
    }

    set image(value) {
        this._image = new Image(value, this);
    }
    get name() {
        if (this.root_service) return this.root_service.name;
        return this._name;
    }

    set name(value) {
        this._name = value;
    }

    get code() {
        if (this.root_service) return this.root_service.code;
        return this._code;
    }

    set code(value) {
        this._code = value;
    }

    get service_type_id() {
        if (this.root_service) return this.root_service.service_type_id;
        return this._service_type_id;
    }

    set service_type_id(value) {
        this._service_type_id = value;
    }

    useRootService(service) {
        this.root_service = service;
        this.products = service.products;
        this.service_vehicle_categories = service.service_vehicle_categories;
    }

    removeRootService() {
        this.root_service = null;
    }

    get products() {
        return this._products || [];
    }

    set products(value) {
        this._products = (value || []).map(val => new G7ServiceProduct(val, this));
    }

    addProduct(product) {
        if (!this._products) this._products = [];
        let exist = this.products.find(val => val.g7_product_id == product.g7_product_id);
        if (exist) exist.qty++;
        else this._products.push(new G7ServiceProduct({product, g7_product_id: product.g7_product_id}, this));
    }

    removeProduct(index) {
        this._products.splice(index, 1);
    }

    get service_vehicle_categories() {
        return this._service_vehicle_categories || []
    }

    set service_vehicle_categories(value) {
        this._service_vehicle_categories = (value || []).map(val => new G7ServiceVehicleCategory(val, this));
    }

    addVehicleCategory() {
        if (!this._service_vehicle_categories) this._service_vehicle_categories = [];
        this._service_vehicle_categories.push(new G7ServiceVehicleCategory({}, this));
    }

    removeVehicleCategory(index) {
        this._service_vehicle_categories.splice(index, 1);
    }

    get submit_data() {
        let data = {
            root_service_id: (this.root_service || {}).id,
            name: this.name,
            code: this.code,
            status: this.status,
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
