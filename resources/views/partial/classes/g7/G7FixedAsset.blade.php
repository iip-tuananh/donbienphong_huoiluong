@include('partial.classes.uptek.FixedAsset')
<script>
    class G7FixedAsset extends BaseClass {
        all_units = @json(\App\Model\Common\Unit::getForSelect());
        statuses = @json(\App\Model\G7\G7FixedAsset::STATUSES);

        before(form) {
            this.no_set = ['root'];
            this.image = {};
            if (form.root) this.root = form.root;
        }

        after(form) {

        }

        get name() {
            if (this.root) return this.root.name;
            return this._name;
        }

        set name(value) {
            this._name = value;
        }

        get code() {
            if (this.root) return this.root.code;
            return this._code;
        }

        set code(value) {
            this._code = value;
        }

        get unit_id() {
            if (this.root) return this.root.unit_id;
            return this._unit_id;
        }

        set unit_id(value) {
            this._unit_id = value;
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

        get root() {
            return this._root;
        }

        set root(value) {
            this._root = new FixedAsset(value);
        }

        useRootFixedAsset(obj) {
            this.root = obj;
            this.depreciation_period = obj.depreciation_period;
            this.import_price_quota = obj.import_price_quota;
            this.note = obj.note;
            this.image.path = this.root.image.path;
        }

        removeRootFixedAsset() {
            this._root = null;
            if (!this.image.file) this.image.path = null;
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = {
                root_id: (this.root || {}).id,
                name: this.name,
                code: this.code,
                status: this.status,
                note: this.note,
                unit_id: this.unit_id,
                depreciation_period: this._depreciation_period,
                import_price_quota: this._import_price_quota,
            }

            data = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) data.append('image', image);
            return data;
        }
    }
</script>
