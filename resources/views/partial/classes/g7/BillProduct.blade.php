<script>
  class BillProduct extends BaseChildClass {

    before(form) {
      this.no_set = ['total_cost'];
	  this.number = ['qty'];
    }

    after(form) {
      if (this._qty == null) this._qty = 1;
    }

    get price() {
      if (this.parent.mode == 'show') return this._price;
	  if (!this.product) return 0;
	  if (this.product.g7_price) return this.product.g7_price.price
      return this.product.price || 0;
    }

    set price(value) {
      this._price = Number(value);
    }

    get total_cost() {
      return this._qty * this.price;
    }

    get is_product() {
      return true;
    }

    get is_service() {
      return false;
    }

    get submit_data() {
        return {
          product_id: this.product_id,
          qty: this._qty,
          price: this.price,
          is_product: true,
          index: this.index
        }
    }

  }
</script>
