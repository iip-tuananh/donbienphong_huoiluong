<script>
    class ReceiptVoucher extends BaseClass {

        before(form) {
			this.statuses = @json(\App\Model\G7\ReceiptVoucher::STATUSES);
			this.payer_types = @json(\App\Model\G7\ReceiptVoucher::PAYER_TYPES);
			this.customers = @json(\App\Model\Common\Customer::getForSelect());
            this.pay_type = 1;
            this.record_date = moment().format('HH:mm DD/MM/YYYY');
        }

        after(form) {
            this.value = (this.cost_after_vat - this.payed_value);
        }

        get record_date() {
            return dateGetter(this._record_date, 'YYYY-MM-DD HH:mm', "HH:mm DD/MM/YYYY");
        }

        set record_date(value) {
            this._record_date = dateSetter(value, "HH:mm DD/MM/YYYY", 'YYYY-MM-DD HH:mm');
        }

        get payed_value ()
        {
            return this._payed_value || 0;
        }

        set payed_value (value)
        {
            value = Number(value);
            this._payed_value = value;
        }

        get pay_type ()
        {
            return this._pay_type;
        }

        set pay_type (value)
        {
            value = Number(value);
            this._pay_type = value;
        }

        get cost_after_vat ()
        {
            return this._cost_after_vat || 0;
        }

        set cost_after_vat (value)
        {
            value = Number(value);
            this._cost_after_vat = value;
        }

        get value() {
            return this._value ? this._value.toLocaleString('en') : '';
        }

        set value(value) {
            value = parseNumberString(value);
            this._value = value;
        }

        get submit_data() {
            return {
                receipt_voucher_type_id: this.receipt_voucher_type_id,
                record_date: this._record_date,
                payer_type_id: this.payer_type_id,
                payer_id: this.payer_id,
                payer_name: this.payer_name,
                value: this._value,
                pay_type: this.pay_type,
                note: this.note,
                customer_id: this.customer_id,
                bill_id: this.id,
            }
        }
    }
</script>
