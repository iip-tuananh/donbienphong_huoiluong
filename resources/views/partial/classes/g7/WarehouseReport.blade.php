<script>
    class WarehouseReport {
        no_set = [];
        constructor(object) {
            if (object) {
                for (let key in object) {
                    if (!this.no_set.includes(key)) this[key] = object[key];
                }
            }
        }

		get from_date() {
            return dateGetter(this._from_date);
        }

        set from_date(value) {
            this._from_date = dateSetter(value);
        }

        get to_date() {
            return dateGetter(this._to_date);
        }

        set to_date(value) {
            this._to_date = dateSetter(value);
        }

        get submit_data() {
            return {
                from_date: this._from_date,
                to_date: this._to_date,
                product_name: this.product_name,
                product_code: this.product_code,
				object_name: this.object_name,
				payment_method: this.payment_method
            }
        }
    }
</script>
