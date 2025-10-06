<script>
    class FixedAsset extends BaseClass {
        no_set = [];
        all_units = @json(\App\Model\Common\Unit::getForSelect());

        before(form) {
            this.image = {};
        }

        after(form) {

        }

        get import_price_quota() {
            return this._import_price_quota ? this._import_price_quota.toLocaleString('en') : '';
        }

        set import_price_quota(value) {
            value = parseNumberString(value);
            this._import_price_quota = value;
        }

        get depreciation_period() {
            return this._depreciation_period;
        }

        set depreciation_period(value) {
            this._depreciation_period = Number(value);
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = {
                name: this.name,
                unit_id: this.unit_id,
                status: this.status,
                note: this.note,
                depreciation_period: this._depreciation_period,
                import_price_quota: this._import_price_quota,
            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
            if (image) data.append('image', image);
            return data;
        }
    }
</script>