<script>
    class Customer extends BaseClass {
        no_set = [];

        before(form) {

        }

        after(form) {

        }

        get submit_data() {
            let data = {
                status: this.status,
            }

            return data;
        }
    }
</script>
