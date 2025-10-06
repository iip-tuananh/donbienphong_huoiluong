<script>
    class Origin extends BaseClass {
        no_set = [];

        before(form) {

        }

        after(form) {

        }

        get submit_data() {
            let data = {
                name: this.name,
                code: this.code,
            }

            return data;
        }
    }
</script>
