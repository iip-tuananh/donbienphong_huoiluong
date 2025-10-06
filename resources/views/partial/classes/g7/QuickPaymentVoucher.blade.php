<script>
    class QuickPaymentVoucher extends PaymentVoucher {
        before(form) {
        }

        after(form) {
        }


        get submit_data() {
            return {
                payment_voucher_type_id: 0,
                record_date: this.record_date,
                recipient_type_id: 3,
                recipient_id: this.recipient_id,
                recipient_name: this.recipient_name,
                value: this._value,
                pay_type: this.pay_type,
                note: this.note
            }
        }
    }
</script>