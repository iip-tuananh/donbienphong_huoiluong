<script>
    class PrintTemplate extends BaseClass {
        no_set = [];

        before(form) {

        }

        after(form) {

        }

        get submit_data() {
            let data = {
                name: this.name,
                code: this.code,
                template: this.template,
            }
            return data;
        }
    }
</script>