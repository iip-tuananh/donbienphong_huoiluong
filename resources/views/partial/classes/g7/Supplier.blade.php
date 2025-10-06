<script>
    class Supplier extends BaseClass {
        no_set = [];
        before(form) {
        }

        after(form) {
        }

        get submit_data() {

            let data = {
                mobile: this.mobile,
                name: this.name,
                status: this.status,
                address: this.address,
            }

            return data;
        }
    }
</script>