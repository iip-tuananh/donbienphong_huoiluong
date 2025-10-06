<script>
  class ServiceProduct extends BaseChildClass {

    after() {
      if (this._qty == null) this._qty = 1;
    }

    get qty() {
        return this._qty;
    }

    set qty(value) {
        this._qty = Number(value);
    }

    get submit_data() {
        return {
            product_id: this.product_id,
            qty: this._qty
        }
    }

  }
</script>
