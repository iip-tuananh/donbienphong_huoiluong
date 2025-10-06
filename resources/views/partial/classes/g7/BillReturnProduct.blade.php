<script>
  class BillReturnProduct extends BaseChildClass {

    before(form) {
		this.no_set = ['total_cost'];
		this.number = ['qty']
    }

	get total_cost() {
		return (this._qty || 0) * this.price;
	}

    get submit_data() {
        return {
			product_id: this.product_id,
			qty: this._qty,
        }
    }

  }
</script>
