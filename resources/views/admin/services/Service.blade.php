@include('admin.products.ProductGallery')
<script>
    class Service extends BaseClass {
        statuses = @json(\App\Model\Admin\Service::STATUSES);
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect(3));

        no_set = [];

        before(form) {
            this.image = {};
            this.image_label = {};
            this.status = 0;
        }

        after(form) {

        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get image_label() {
            return this._image_label;
        }

        set image_label(value) {
            this._image_label = new Image(value, this);
        }

        get base_price() {
            return this._base_price ? this._base_price.toLocaleString('en') : '';
        }

        set base_price(value) {
            value = parseNumberString(value);
            this._base_price = value;
        }

        get price() {
            return this._price ? this._price.toLocaleString('en') : '';
        }

        set price(value) {
            value = parseNumberString(value);
            this._price = value;
        }

        get galleries() {
            return this._galleries || [];
        }

        set galleries(value) {
            this._galleries = (value || []).map(val => new ProductGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new ProductGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }

        get submit_data() {
            let data = {
                name: this.name,
                description: this.description,
                content: this.content,
                status: this.status,
                cate_id: this.cate_id,
            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
            if (image) data.append('image', image);

            let image_label = $(`#${this.image_label.element_id}`).get(0).files[0];
            if (image_label) data.append('image_label', image_label);


            return data;
        }
    }
</script>
