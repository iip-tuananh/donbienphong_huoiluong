<script>
    class Recruitment extends BaseClass {
        before(form) {
            this.image = {};
            this.no_set = ['expiration_date'];
        }


        after(form) {
            this._expiration_date = form.expiration_date;
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }



        get expiration_date() {
            return dateGetter(this._expiration_date, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set expiration_date(value) {
            this._expiration_date = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get submit_data() {
            let data = {
                title: this.title,
                address: this.address,
                salary: this.salary,
                description: this.description,
                expiration_date: this._expiration_date,

            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
            if (image) data.append('image', image);
            return data;
        }
    }
</script>
