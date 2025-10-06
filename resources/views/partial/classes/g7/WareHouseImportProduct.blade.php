<script>
    class WareHouseImportProduct extends BaseChildClass {
        before (form)
        {
            this.no_set = ['amount'];
        }
        after() {
            if (this._qty == null) this._qty = 1;
            if (this._import_price == null) this._import_price = 0;
        }

        get qty() {
            return this._qty;
        }

        set qty(value) {
            this._qty = Number(value);
        }

        get amount() {
            return (this._import_price) ? (this._import_price * this._qty) : 0;
        }

        get import_price() {
            return this._import_price ? this._import_price.toLocaleString('en') : 0;
        }

        set import_price(value) {
            value = parseNumberString(value);
            this._import_price= value;
        }

        get submit_data() {
            return {
                name: this.product.name,
                unit_name: this.product.unit_name,
                unit_id: this.product.unit_id,
                product_id: this.product_id,
                qty: this._qty,
                import_price: this._import_price,
                amount: this.amount,
            }
        }
    }
</script>
