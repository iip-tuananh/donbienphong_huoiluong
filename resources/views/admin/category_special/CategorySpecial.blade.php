<script>
    class CategorySpecial extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
        }

        after(form) {

        }


        get submit_data() {
            let data = {
                name: this.name,
                order_number: this.order_number,
                show_home_page: this.show_home_page,
            }
            data = jsonToFormData(data);

            // let image = this.image.submit_data;
            //
            // if (image) data.append('image', image);

            return data;
        }
    }
</script>
