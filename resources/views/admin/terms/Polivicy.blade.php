<script>
    class Polivicy extends BaseClass {
        no_set = [];

        before(form) {
        }

        after(form) {

        }

        get details() {
            return this._details|| [];
        }

        set details(value) {
            this._details = (value || []).map(val => new PolivicyDetail(val, this));
        }

        addDetail() {
            if (!this._details) this._details = [];
            this._details.push(new PolivicyDetail({}, this));
        }

        removeDetail(index) {
            this._details.splice(index, 1);
        }

        get submit_data() {
            return {
                details: this.details.map(val => val.submit_data)
            }

        }
    }
</script>
