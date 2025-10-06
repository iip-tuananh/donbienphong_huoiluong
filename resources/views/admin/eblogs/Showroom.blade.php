<script>
    class Showroom extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
            this.file = {};
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

        get file() {
            return this._file;
        }

        set file(value) {
            this._file = new File(value, this);

        }

        clearFile() {
            if (this.file) this.file.clear();
        }

        get submit_data() {
            let data = {
                title: this.title,
                intro: this.intro,
            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            let file = this.file.submit_data;

            if (image) data.append('image', image);
            if (file) data.append('file', file);

            return data;
        }
    }
</script>
