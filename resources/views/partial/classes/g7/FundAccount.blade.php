<script>
    class FundAccount extends BaseClass {
        no_set = [];

        before(form) {

        }

        after(form) {

        }

        get monney() {
            return this._monney ? this._monney.toLocaleString('en') : '';
        }

        set monney(value) {
            value = parseNumberString(value);
            this._monney = value;
        }

        get submit_data() {
            let data = {
                type: this.type,
                name: this.name,
                monney: this._monney,
                acc_num: this.acc_num,
                acc_holder: this.acc_holder,
                bank: this.bank,
                branch: this.branch,
                status: this.status,
                note: this.note,
            }
            return data;
        }
    }
</script>