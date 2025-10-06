@include('partial.classes.g7.G7FixedAssetImportDetail')
<script>
    class G7FixedAssetImport extends BaseClass {

		before(form) {
			this.no_set = ['amount', 'vat_cost', 'amount_after_vat', 'qty'];
			this.statuses = @json(\App\Model\G7\G7FixedAssetImport::STATUSES);
			this.suppliers = @json(\App\Model\G7\Supplier::getForSelect());
            this.vat_percent = 10;
			this.import_date = moment().format('YYYY-MM-DD HH:mm')
        }

        get vat_percent() {
            return this._vat_percent;
        }

        set vat_percent(value) {
            this._vat_percent = Number(value);
        }

        get qty(){
            return this.details.reduce((acc, cur) => {
				return acc + (cur._qty || 0);
			}, 0);
        }

        get vat_cost() {
            return Math.round(this.amount * (this._vat_percent || 0) / 100);
        }

        get amount(){
            return this.details.reduce((acc, cur) => {
				return acc + cur.total_price;
			}, 0);
        }

        get amount_after_vat(){
            return this.amount + this.vat_cost;
        }

        get details() {
            return this._details || []
        }

        set details(value) {
            this._details = (value || []).map(val => new G7FixedAssetImportDetail(val, this));
        }

        addDetail(asset) {
            if (!this._details) this._details = [];
            let exist = this.details.find(val => val.asset_id == asset.id);
            if (exist) exist.qty++;
            else this._details.push(new G7FixedAssetImportDetail({asset, asset_id: asset.id}, this));
        }

        removeDetail(index) {
            this._details.splice(index, 1);
        }

        get submit_data() {
            return {
                import_date: this.import_date,
                supplier_id: this.supplier_id,
                note: this.note,
				amount: this.amount,
                vat_percent: this._vat_percent,
                details: this.details.map(val => val.submit_data)
            }
        }

    }
</script>
