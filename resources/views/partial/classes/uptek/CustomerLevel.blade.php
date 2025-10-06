<script>
    class CustomerLevel extends BaseClass {
        no_set = [];
        before(form) {

        }

        after(form) {

        }

        get submit_data() {
            return {
                name: this.name,
                point: this.point,
            }
        }
    }
</script>