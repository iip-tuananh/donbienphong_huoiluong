<script>
    class G7FixedAssetImportDetail extends BaseChildClass {
        before (form) {
            this.no_set = ['total_price'];
			this.qty = 1;
			if (form.asset) this.price = form.asset.import_price_quota;
        }

        get qty() {
            return this._qty;
        }

        set qty(value) {
            this._qty = Number(value);
        }

		get price() {
            return this._price;
        }

        set price(value) {
            this._price = Number(value);
        }

        get total_price() {
            return (this._price || 0) * (this._qty || 0);
        }

        get submit_data() {
            return {
                asset_id: this.asset_id,
                qty: this._qty,
                price: this._price,
            }
        }
    }
</script>
