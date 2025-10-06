<script>
    class Customer extends BaseClass {
        customer_groups = @json(\App\Model\Common\CustomerGroup::getForSelect());
        provinces = @json(\Vanthao03596\HCVN\Models\Province::select('id', 'name_with_type')->get());
        statuses = @json(\App\Model\Common\Customer::STATUSES);
        before(form) {
            this.status = 1;
            this.no_set = ['birth_day'];
            this.gender = 1;
        }

        after(form) {
            this._birth_day = form.birth_day;
        }

        get birth_day() {
            return dateGetter(this._birth_day, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set birth_day(value) {
            this._birth_day = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }


        get province_id ()
        {
            return this._province_id || 0;
        }

        set province_id (value)
        {
            value = Number(value);
            this._province_id = value;
        }

        get district_id ()
        {
            return this._district_id || 0;
        }

        set district_id (value)
        {
            value = Number(value);
            this._district_id = value;
        }

        get ward_id ()
        {
            return this._ward_id || 0;
        }

        set ward_id (value)
        {
            value = Number(value);
            this._ward_id = value;
        }

        // get gender ()
        // {
        //     return this._gender || 0;
        // }

        // set gender (value)
        // {
        //     value = Number(value);
        //     this._gender = value;
        // }

        get submit_data() {
            return {
                name: this.name,
                mobile: this.mobile,
                email: this.email,
                birth_day: this._birth_day,
                gender: this.gender,
                customer_group_id: this.customer_group_id,
                province_id: this._province_id,
                district_id: this._district_id,
                ward_id: this._ward_id,
                adress: this.adress,
                status: this.status,
                car_id: this.car_id
            }
        }
    }
</script>