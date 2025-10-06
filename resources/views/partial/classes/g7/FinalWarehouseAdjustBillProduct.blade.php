<script>
  class FinalWarehouseAdjustBillProduct extends BaseChildClass {

    before(form) {
      this.no_set = ['diff'];
    }

    after(form) {
    }

    get qty() {
        return this._qty;
    }

    set qty(value) {
        this._qty = Number(value);
    }

    get real_qty() {
        return this._real_qty;
    }

    set real_qty(value) {
        this._real_qty = Number(value);
    }

    get diff() {
      return (this.real_qty || 0) - (this.qty || 0);
    }

    get submit_data() {
        return {
          id: this.id,
          real_qty: this._real_qty,
          note: this.note
        }
    }

  }
</script>
