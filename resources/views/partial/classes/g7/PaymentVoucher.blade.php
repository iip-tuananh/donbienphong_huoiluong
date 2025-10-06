<script>
    class PaymentVoucher extends BaseClass {
        statuses = @json(\App\Model\G7\PaymentVoucher::STATUSES);
        recipient_types =@json(\App\Model\G7\PaymentVoucher::RECIPIENT_TYPES);
        suppliers = @json(\App\Model\G7\Supplier::getForSelect());

        before(form) {
            this.pay_type = 1;
            this.record_date = moment().format(' YYYY-MM-DD HH:mm');
        }

        after(form) {
            this.value = (this.amount_after_vat - this.payed_value);

            // if(this.record_date) this.record_date = this._record_date;
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

        get amount_after_vat ()
        {
            return this._amount_after_vat || 0;
        }

        set amount_after_vat (value)
        {
            value = Number(value);
            this._amount_after_vat = value;
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
                payment_voucher_type_id: this.payment_voucher_type_id,
                record_date: this.record_date,
                recipient_type_id: this.recipient_type_id,
                recipient_id: this.recipient_id,
                recipient_name: this.recipient_name,
                value: this._value,
                pay_type: this.pay_type,
                note: this.note,
                ware_house_import_id: this.id,
                supplier_id: this.supplier_id,
                month: this.month,
                g7_fixed_asset_import_id: this.g7_fixed_asset_import_id
            }
        }
    }
</script>