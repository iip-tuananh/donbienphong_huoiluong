@include('partial.classes.g7.WareHouseImportProduct')
<script>
    class WareHouseImport extends BaseClass {
        statuses = @json(\App\Model\G7\G7Product::STATUSES);
        suppliers = @json(\App\Model\G7\Supplier::getForSelect());

        before(form) {
            this.no_set = ['amount','amount_after_vat','import_date','qty'];
            this.vat_percent = 10;
            this._pay_type = 1;
            this.import_date = moment().format('HH:mm DD/MM/YYYY');
        }

        after(form) {
            // this._import_date = form.import_date;
        }

        get import_date() {
            return dateGetter(this._import_date, 'YYYY-MM-DD HH:mm', "HH:mm DD/MM/YYYY");
        }

        set import_date(value) {
            this._import_date = dateSetter(value, "HH:mm DD/MM/YYYY", 'YYYY-MM-DD HH:mm');
        }

        get pay_type() {
            return this._pay_type;
        }

        set pay_type(value) {
            this._pay_type = Number(value);
        }

        get vat_percent() {
            return this._vat_percent;
        }

        set vat_percent(value) {
            this._vat_percent = Number(value);
        }

        get qty(){
            let total_qty = 0;
            for(var i = 0; i < this.products.length; i++){
                var product = this.products[i];
                total_qty += product.qty;
            }
            return total_qty;
        }

        get vat() {
            return this.amount * (this._vat_percent || 0) / 100;
        }

        get amount(){
            let total = 0;
            for(var i = 0; i < this.products.length; i++){
                var product = this.products[i];
                total += product.amount;
            }
            return total;
        }

        set amount(value) {
            this._amount = Number(value);
        }

        get amount_after_vat(){
            return this.amount + this.vat;
        }

        set amount_after_vat(value) {
            this._amount_after_vat = Number(value);
        }

        get products() {
            return this._products || []
        }

        set products(value) {
            this._products = (value || []).map(val => new WareHouseImportProduct(val, this));
        }

        addProduct(product) {
            if (!this._products) this._products = [];
            let exist = this.products.find(val => val.product_id == product.product_id);
            if (exist) exist.qty++;
            else this._products.push(new WareHouseImportProduct({product, product_id: product.product_id}, this));
        }

        removeProduct(index) {
            this._products.splice(index, 1);
        }

        get submit_data() {
            return {
                import_date: this._import_date,
                supplier_id: this.supplier_id,
                qty: this.qty,
                pay_type: this._pay_type,
                note: this.note,
                vat_percent: this._vat_percent,
                vat: this.vat,
                amount: this.amount,
                amount_after_vat: this.amount_after_vat,
                products: this.products.map(val => val.submit_data)
            }
        }

    }
</script>
