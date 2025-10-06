<script>
  class BillOther extends BaseChildClass {

    before(form) {
      this.no_set = ['total_cost'];
	  this.number = ['qty', 'price'];
    }

    after(form) {
      	if (this._qty == null) this._qty = 1;
    }

    get total_cost() {
      return this._qty * (this._price || 0);
    }

	get is_other() {
      return true;
    }

    get submit_data() {
        return {
          name: this.name,
          qty: this._qty,
          price: this._price,
          is_other: true,
          index: this.index
        }
    }

  }
</script>
