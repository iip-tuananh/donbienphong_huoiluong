@include('admin.experience.ExperienceGallery')

<script>
    class Experience extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
        }

        after(form) {
        }


        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }


        clearImage() {
            if (this.image) this.image.clear();
            if (this.image_back) this.image_back.clear();
        }

        get galleries() {
            return this._galleries || [];
        }

        set galleries(value) {
            this._galleries = (value || []).map(val => new ExperienceGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new ExperienceGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }

        get submit_data() {
            let data = {
                name: this.name,
                name_eng: this.name_eng,
                body: this.body,
                body_eng: this.body_eng,
            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);

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
