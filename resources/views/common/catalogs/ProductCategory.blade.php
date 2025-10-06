<script>
    class ProductCategory extends BaseClass {
        categories = @json(\App\Model\Common\ProductCategory::getForSelect());

        before(form) {
            this.no_set = ['booking_time'];
        }

        after(form) {
            this._booking_time = form.booking_time;
        }


        get status() {
            return this._status;
        }

        set status(value) {
            this._status = value;
        }

        get submit_data() {
            return {
                customer_id: this._customer_id,
                booking_time: this._booking_time,
                g7_id: this._g7_id,
                status: this._status,
                note: this.note,
            }
        }
    }
</script>