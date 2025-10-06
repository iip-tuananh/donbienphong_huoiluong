@include('partial.classes.g7.WarehouseExportDetail')
<script>
    class WarehouseExport extends BaseClass {

        before(form) {
            this.no_set = [];
			this.type = 1;
        }

        after(form) {
            if(form.bill) this.useBill(form.bill);
            if(form.details) this.details = form.details;
        }

		useBill(bill) {
			this.bill = bill;
			this.details = bill.export_products;
            this.customer_name = bill.customer_name;
            this.bill_date = bill.bill_date;
            this.bill_noe = bill.note;
		}

        // useWarehouseExport(w) {
        //     this.code = w.code;
        //     this.type = w.type;
        //     this.note
        // }

        get details() {
            return this._details || [];
        }
        set details(value) {
            this._details = (value || []).map(val => new WarehouseExportDetail(val, this));
        }

        get submit_data() {
            return {
				type: this.type,
				bill_id: (this.bill || {}).id,
                details: this.details.map(val => val.submit_data),
                note: this.note,
            }
        }
    }
</script>
