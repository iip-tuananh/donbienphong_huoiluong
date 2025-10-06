<script>
  class G7ServiceVehicleCategoryProduct extends BaseChildClass {

    after() {
      if (this._qty == null) this._qty = 1;
    }

    get qty() {
        return this._qty;
    }

    set qty(value) {
        this._qty = Number(value);
    }

    get service_price() {
        return this._service_price ? this._service_price.toLocaleString('en') : '';
    }

    set service_price(value) {
        this._service_price = parseNumberString(value);
    }

    get submit_data() {
        return {
            g7_product_id: this.g7_product_id,
            service_price: this._service_price,
            qty: this._qty
        }
    }

  }
</script>
