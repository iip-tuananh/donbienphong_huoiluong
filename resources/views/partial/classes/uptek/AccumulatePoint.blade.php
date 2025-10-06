<script>
    class AccumulatePoint extends BaseClass {
        no_set = [];

        before(form) {

        }

        after(form) {

        }

        get accumulate_pay_point()
        {
            return this._accumulate_pay_point;
        }

        set accumulate_pay_point(value)
        {
            this._accumulate_pay_point = !!Number(value);
        }

        get allow_pay()
        {
            return this._allow_pay;
        }

        set allow_pay(value)
        {
            this._allow_pay = !!Number(value);
        }

        get submit_data() {
            return {
                value_to_point_rate: this.value_to_point_rate,
                point_to_money_rate: this.point_to_money_rate,
                allow_pay: this._allow_pay,
                accumulate_pay_point: this._accumulate_pay_point,
            }
        }
    }
</script>