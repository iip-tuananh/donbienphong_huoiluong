<script>
    class ProductVideo extends BaseChildClass {

        before(form) {
            this.videos = {};
        }

        after(form) {

        }

        get submit_data() {
            return {
                link: this.link,
                video: this.video,
            }
        }

    }
</script>
