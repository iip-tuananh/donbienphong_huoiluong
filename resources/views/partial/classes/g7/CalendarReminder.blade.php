<script>
    class CalendarReminder extends BaseClass {

        before(form) {
			this.statuses = @json(\App\Model\G7\CalendarReminder::STATUSES);
			this.types = @json(\App\Model\G7\CalendarReminder::TYPES);
			this.customers = @json(\App\Model\Common\Customer::getForSelect());
			this.cars = @json(\App\Model\Common\Car::getForSelect());
            // this.reminder_date = moment().format('DD/MM/YYYY');
        }

        after(form) {
            this.value = (this.cost_after_vat - this.payed_value);
            if(form.reminder_date) this._reminder_date = form.reminder_date;
        }

        get reminder_date() {
            return dateGetter(this._reminder_date, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set reminder_date(value) {
            this._reminder_date = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get reminder_type ()
        {
            return this._reminder_type;
        }

        set reminder_type (value)
        {
            value = Number(value);
            this._reminder_type = value;
        }

        get car_id ()
        {
            return this._car_id;
        }

        set car_id (value)
        {
            value = Number(value);
            this._car_id = value;
        }


        get customer_id ()
        {
            return this._customer_id;
        }

        set customer_id (value)
        {
            value = Number(value);
            this._customer_id = value;
        }

        get submit_data() {
            return {
                reminder_date: this._reminder_date,
                reminder_type: this._reminder_type,
                customer_id: this._customer_id,
                car_id: this._car_id,
                note: this.note,
            }
        }
    }
</script>