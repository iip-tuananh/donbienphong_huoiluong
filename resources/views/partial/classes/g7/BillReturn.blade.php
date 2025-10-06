@include('partial.classes.g7.BillReturnProduct')
<script>
    class BillReturn extends BaseClass {

        before(form) {
            this.no_set = ['total_cost', 'vat_cost', 'cost_after_vat'];
			this.type = 1;
			this.bill_date = moment().format("YYYY-MM-DD HH:mm")
        }

        after(form) {

        }

		useBill(bill) {
			this.bill = bill;
			this.products = bill.products;
			this.vat_percent = +bill.vat_percent;
		}

		get total_cost() {
			return this.products.reduce((acc, cur) => {
				return acc + cur.total_cost;
			}, 0);
		}

		get vat_cost() {
			return Math.round(this.total_cost * this.vat_percent / 100);
		}

		get cost_after_vat() {
			return this.total_cost + this.vat_cost;
		}

        get products() {
            return this._products || [];
        }
        set products(value) {
            this._products = (value || []).map(val => new BillReturnProduct(val, this));
        }

        get submit_data() {
            return {
				type: this.type,
				bill_date: this.bill_date,
				total_cost: this.total_cost,
				bill_id: (this.bill || {}).id,
                products: this.products.map(val => val.submit_data),
                note: this.note,
            }
        }
    }
</script>
