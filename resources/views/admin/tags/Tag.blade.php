
<script>
    class Tag extends BaseClass {
        no_set = [];
        categories = @json(\App\Model\Admin\Category::getForSelect(true));

        before(form) {
            this.image = {};
            this._values = [];
        }

        after(form) {

        }


        get submit_data() {
            let data = {
                id: this.id,
                code: this.code,
                name: this.name,
            }

            data = jsonToFormData(data);

            return data;
        }
    }
</script>
