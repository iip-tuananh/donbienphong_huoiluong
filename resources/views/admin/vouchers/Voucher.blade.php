<script>
    class Voucher extends BaseClass {
        no_set = [];

        before(form) {
        }

        after(form) {

        }

        get to_date() {
            return this._to_date ? moment(this._to_date).format('DD/MM/YYYY') : '';
        }

        set to_date(value) {
            this._to_date = value ? moment(value).format('YYYY-MM-DD') : '';
        }

        get from_date() {
            return this._from_date ? moment(this._from_date).format('DD/MM/YYYY') : '';
        }

        set from_date(value) {
            this._from_date = value ? moment(value).format('YYYY-MM-DD') : '';
        }


        get submit_data() {
            let data = {
                name: this.name,
                code: this.code,
                limit_bill_value: this.limit_bill_value,
                limit_product_qty: this.limit_product_qty,
                limit_user_qty: this.limit_user_qty,
                value: this.value,
                quantity: this.quantity,
                status: this.status,
                from_date: this._from_date,
                to_date: this._to_date,
                description: this.description,
                content: this.content,
            }
            data = jsonToFormData(data);

            return data;
        }
    }
</script>
