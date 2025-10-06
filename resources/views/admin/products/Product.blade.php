@include('admin.products.ProductGallery')
@include('admin.products.ProductAttribute')
@include('admin.products.ProductVideo')
<script>
    class Product extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\Category::getForSelect());

        before(form) {
            this.image = {};
            this.image_back = {};
            this.status = 0;
            this.attribute_values = form.attribute_values || [];
            this.videos = form.videos || [];
        }

        after(form) {

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

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get image_back() {
            return this._image_back;
        }

        set image_back(value) {
            this._image_back = new Image(value, this);
        }

        clearImage() {
            if (this.image) this.image.clear();
            if (this.image_back) this.image_back.clear();
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

        set attribute_values(value) {
            this._attributes = (value || []).map(val => new ProductAttribute(val, this));
        }

        get attribute_values() {
            return this._attributes;
        }

        addAttribute() {
            this._attributes.push(new ProductAttribute({}, this));
        }

        removeAttribute(index) {
            this._attributes.splice(index, 1);
        }

        set videos(value) {
            this._videos = (value || []).map(val => new ProductVideo(val, this));
        }

        get videos() {
            return this._videos;
        }

        addVideo() {
            console.log(this._videos)
            this._videos.push(new ProductVideo({}, this));
        }

        removeVideo(index) {
            this._videos.splice(index, 1);
        }

        get use_url_custom() {
            return this._use_url_custom;
        }

        set use_url_custom(value) {
            this._use_url_custom= !!Number(value) || null;
        }

        get manufacturer_id() {
            return this._manufacturer_id;
        }

        set manufacturer_id(value) {
            this._manufacturer_id = value || null;
        }

        get origin_id() {
            return this._origin_id;
        }

        set origin_id(value) {
            this._origin_id = value || null;
        }

        get documents() {
            return (this.attachments || '').split(', ').map(val => val.trim()).filter(val => !!val);
        }

        // get tag_ids() {
        //     return this._tag_ids;
        // }
        //
        // set tag_ids(value) {
        //     this._tag_ids = (value || []).map(val => val.id);
        // }

        get submit_data() {
            let data = {
                cate_id: this.cate_id,
                code: this.code,
                name: this.name,
                base_price: this._base_price,
                price: this._price,
                short_des: this.short_des,
                intro: this.intro,
                body: this.body,
                status: this.status,
                manufacturer_id: this.manufacturer_id,
                origin_id: this.origin_id,
                title_seo: this.title_seo,
                content_seo: this.content_seo,
                use_url_custom: this.use_url_custom,
                url_custom: this.url_custom,
                tag_ids: this.tag_ids,
                state: this.state,
                is_pin: this.is_pin,
                attributes: this.attribute_values.map(val => val.submit_data),
                videos: this.videos.map(val => val.submit_data)
            }

            console.log(this.use_url_custom);
            data = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) data.append('image', image);
            let image_back = this.image_back.submit_data;
            if (image_back) data.append('image_back', image_back);

            this.galleries.forEach((g, i) => {
                if (g.id) data.append(`galleries[${i}][id]`, g.id);
                let gallery = g.image.submit_data;
                if (gallery) data.append(`galleries[${i}][image]`, gallery);
                else data.append(`galleries[${i}][image_obj]`, g.id);
            })

            return data;
        }
    }
</script>
