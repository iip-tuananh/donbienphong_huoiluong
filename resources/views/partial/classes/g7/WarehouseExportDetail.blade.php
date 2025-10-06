<script>
  class WarehouseExportDetail extends BaseChildClass {

    before(form) {
		this.no_set = [];
		this.number = ['qty']
    }

    get submit_data() {
        return {
			product_id: this.product_id,
			qty: this._qty,
        }
    }

  }
</script>
