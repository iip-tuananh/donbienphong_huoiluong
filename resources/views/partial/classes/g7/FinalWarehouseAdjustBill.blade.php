@include('partial.classes.g7.FinalWarehouseAdjustBillProduct')
<script>
  class FinalWarehouseAdjustBill extends BaseChildClass {

    before(form) {
    }

    after(form) {
    }

    get export_products() {
        return this._export_products || [];
    }
    set export_products(value) {
        this._export_products = (value || []).map(val => new FinalWarehouseAdjustBillProduct(val, this));
    }

    get rowspan() {
      return this.export_products.length + 1;
    }

    get submit_data() {
        return {
          id: this.id,
          export_products: this.export_products.map(val => val.submit_data)
        }
    }

  }
</script>
