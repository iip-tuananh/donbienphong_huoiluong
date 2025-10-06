<script>
    class Topic extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
        }

        after(form) {

        }

        get submit_data() {
            return {
                name: this.name,
                name_eng: this.name_eng,
                description: this.description,
                description_eng: this.description_eng,
            }

        }
    }
</script>
