<script>
  class G7ServiceProduct extends BaseChildClass {

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
            g7_product_id: this.g7_product_id,
            qty: this._qty
        }
    }

  }
</script>
