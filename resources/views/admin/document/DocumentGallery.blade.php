<script>
    class DocumentGallery extends BaseChildClassWithFile {

        before(form) {
            this.image = {};
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            return {
            }
        }
    }
</script>
