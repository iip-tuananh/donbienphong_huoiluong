<script>
    class Gallery extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
        }

        after(form) {

        }

        // Ảnh đại diện
        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);

        }

        clearImage() {
            if (this.image) this.image.clear();
        }

        get submit_data() {
            let data = {
                title: this.title,
            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;

            if (image) data.append('image', image);

            return data;
        }
    }
</script>
