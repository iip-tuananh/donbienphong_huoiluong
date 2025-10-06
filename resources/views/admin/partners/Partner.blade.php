<script>
    class Partner extends BaseClass {
        no_set = [];
        statuses = @json(\App\Model\Admin\Partner::STATUSES);

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
                name: this.name,
                author: this.author,
                page: this.page,
                phone_number: this.phone_number,
                sort_order: this.sort_order,
            }
            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);

            return data;
        }
    }
</script>
