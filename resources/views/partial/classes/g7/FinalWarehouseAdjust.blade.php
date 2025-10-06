@include('partial.classes.g7.FinalWarehouseAdjustBill')
<script>
    class FinalWarehouseAdjust extends BaseClass {
        before(form) {
            this.no_set = [];
        }
        after(form) {

        }

        get bills() {
            return this._bills || [];
        }
        set bills(value) {
            this._bills = (value || []).map(val => new FinalWarehouseAdjustBill(val, this));
        }

        get submit_data() {
            return {
                note: this.note,
                bills: this.bills.map(val => val.submit_data),
            }
        }
    }
</script>
