<script>
    get expected_borrow_date() {
        if (!this._expected_borrow_date) return '';
        return moment(this._expected_borrow_date, "YYYY-MM-DD").format("DD/MM/YYYY");
    }

    set expected_borrow_date(value) {
        if (!value) return this._expected_borrow_date = null;
        this._expected_borrow_date = moment(value, "DD/MM/YYYY").format("YYYY-MM-DD");
    }
</script>