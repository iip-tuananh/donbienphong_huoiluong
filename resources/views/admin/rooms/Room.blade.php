@include('admin.rooms.RoomGallery')

<script>
    class Room extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\Category::getForSelect());

        before(form) {
            this.image = {};
            this.image_back = {};
            this.status = 1;
        }

        after(form) {
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
            this._galleries = (value || []).map(val => new RoomGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new RoomGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }

        get submit_data() {
            let data = {
                name: this.name,
                area: this.area,
                view: this.view,
                bedrooms: this.bedrooms,
                description: this.description,
                highlight: this.highlight,
                amenities: this.amenities,
                maximum_occupancy: this.maximum_occupancy,
                status: this.status,
                name_eng: this.name_eng,
                area_eng: this.area_eng,
                view_eng: this.view_eng,
                bedrooms_eng: this.bedrooms_eng,
                maximum_occupancy_eng: this.maximum_occupancy_eng,
                description_eng: this.description_eng,
                highlight_eng: this.highlight_eng,
                amenities_eng: this.amenities_eng,
            }

            data = jsonToFormData(data);

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
