<script>
  class BillService extends BaseChildClass {

    before(form) {
      this.no_set = ['total_cost'];
    }

    after(form) {
      if (this._qty == null) this._qty = 1;
    }

    get qty() {
        return this._qty;
    }

    set qty(value) {
        this._qty = Number(value);
    }

    get total_cost() {
      return this._qty * this.price;
    }

    get price() {
      return this._price;
    }

    set price(value) {
      this._price = Number(value);
    }

    get is_product() {
      return false;
    }

    get is_service() {
      return true;
    }

    get submit_data() {
      return {
        service_id: this.service_id,
        group_id: this.group_id,
        qty: this._qty,
        price: this.price,
        is_service: true,
        index: this.index
      }
    }

  }
</script>
