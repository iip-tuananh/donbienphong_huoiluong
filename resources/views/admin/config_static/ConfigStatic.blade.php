<script>
    class ConfigStatic extends BaseClass {
        no_set = [];
        statuses = @json(\App\Model\Admin\Partner::STATUSES);

        before(form) {
        }

        after(form) {

        }


        get submit_data() {
            let data = {
                title: this.title,
                des: this.des,
            }

            return data;
        }
    }
</script>
