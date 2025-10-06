<script>
    class ProductAttribute extends BaseChildClass {

        before(form) {
            this.attributes = {};
        }

        after(form) {

        }

        get attribute_id() {
            return this._attribute_id;
        }

        set attribute_id(value) {
            this._attribute_id = value
        }

        get value() {
            return this._value;
        }

        set value(value) {
            this._value= value
        }

        get submit_data() {
            return {
                attribute_id: this.attribute_id,
                value: this.value,
            }
        }

    }
</script>
