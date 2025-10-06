<script>
    class G7Employee extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
            this.gender = 1;
        }

        after(form) {
            this._birth_day = form.birth_day;
            this._start_date = form.start_date;
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get birth_day() {
            return dateGetter(this._birth_day, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set birth_day(value) {
            this._birth_day = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get start_date() {
            return dateGetter(this._start_date, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set start_date(value) {
            this._start_date = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get submit_data() {
            let data = {
                name: this.name,
                mobile: this.mobile,
                email: this.email,
                start_date: this._start_date,
                birth_day: this._birth_day,
                address: this.address,
                gender: this.gender,
                status: this.status
            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
            if (image) data.append('image', image);
            return data;
        }
    }
</script>