<script>
    class Store extends BaseClass {
        no_set = [];

        before(form) {
        }

        after(form) {

        }


        get submit_data() {
            let data = {
                name: this.name,
                address: this.address,
                phone: this.phone,
                des: this.des,
                email: this.email,
                tax_code: this.tax_code,
                province_id: this.province_id,
                hotline: this.hotline,
                lat: this.lat,
                long: this.long,
            }
            // console.log(data);
            // return;
            data = jsonToFormData(data);

            return data;
        }
    }
</script>
