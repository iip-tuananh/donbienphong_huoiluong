<script>
    class Booking extends BaseClass {
        statuses = @json(\App\Model\Common\Booking::STATUSES);
        customers = @json(\App\Model\Common\Customer::getForSelect());
        g7 = @json(\App\Model\Uptek\G7Info::getForSelect());

        before(form) {
            this.no_set = ['booking_time'];
        }

        after(form) {
            this._booking_time = form.booking_time;
        }

        get booking_time() {
            return dateGetter(this._booking_time, 'YYYY-MM-DD HH:mm', "HH:mm DD/MM/YYYY");
        }

        set booking_time(value) {
            this._booking_time = dateSetter(value, "HH:mm DD/MM/YYYY", 'YYYY-MM-DD HH:mm');
        }

        get customer_id() {
            return this._customer_id;
        }

        set customer_id(value) {
            this._customer_id = value;
        }

        get g7_id() {
            return this._g7_id;
        }

        set g7_id(value) {
            this._g7_id = value;
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