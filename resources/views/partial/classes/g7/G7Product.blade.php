@include('partial.classes.uptek.Product')
<script>
    class G7Product extends BaseClass {
        all_categories = @json(\App\Model\Common\ProductCategory::getForSelect());
        all_units = @json(\App\Model\Common\Unit::getForSelect());
        statuses = @json(\App\Model\G7\G7Product::STATUSES);

        before(form) {
            this.no_set = ['root_product'];
            this.image = {};
            if (form.root_product) this.root_product = form.root_product;
        }

        after(form) {

        }

        get name() {
            if (this.root_product) return this.root_product.name;
            return this._name;
        }

        set name(value) {
            this._name = value;
        }

        get product_category_id() {
            if (this.root_product) return this.root_product.product_category_id;
            return this._product_category_id;
        }

        set product_category_id(value) {
            this._product_category_id = value;
        }

        get unit_id() {
            if (this.root_product) return this.root_product.unit_id;
            return this._unit_id;
        }

        set unit_id(value) {
            this._unit_id = value;
        }

        get price() {
            return this._price ? this._price.toLocaleString('en') : '';
        }

        set price(value) {
            value = parseNumberString(value);
            this._price = value;
        }

        get points() {
            return this._points;
        }

        set points(value) {
            this._points = Number(value);
        }

        get root_product() {
            return this._root_product;
        }

        set root_product(value) {
            this._root_product = new Product(value);
        }

        useRootProduct(product) {
            this.root_product = product;
            this.price = product.price;
            this.points = product.points;
            this.note = product.note;
            this.image.path = this.root_product.image.path;
        }

        removeRootProduct() {
            this._root_product = null;
            if (!this.image.file) this.image.path = null;
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = {
                root_product_id: (this.root_product || {}).id,
                name: this.name,
                code: this.code,
                barcode: this.barcode,
                status: this.status,
                product_category_id: this.product_category_id,
                note: this.note,
                points: this._points,
                price: this._price,
                unit_id: this.unit_id,
            }

            data = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) data.append('image', image);
            return data;
        }
    }
</script>
