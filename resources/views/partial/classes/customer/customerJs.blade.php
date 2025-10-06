<script>
    class Customer {
        constructor(customer) {
            if (customer) {
                for (let key in customer) {
                    this[key] = customer[key];
                }
                this.mobile = customer.mobile;
                if (customer.customer_type == 1) this.phones = customer.mobile.split(',').map(val => val.trim());
            }
        }

        get customer_type() {
            return this._customer_type;
        }

        set customer_type(value) {
            this.mobile = '';
            this.phones = [''];
            this._customer_type = value;
        }

        get delivery_places() {
            return this._delivery_places;
        }

        set delivery_places(value) {
            if (!value) this._delivery_places = null;
            this._delivery_places = value.map(val => new DeliveryPlace(val));
        }

        get submit_data() {
            return {...this, customer_type: this.customer_type};
        }

        addPhone() {
            this.phones.push('');
        }

        removePhone(index) {
            this.phones.splice(index, 1);
        }

        addDeliveryPlace(place) {
            if (!this._delivery_places) this._delivery_places = [];
            this._delivery_places.push(new DeliveryPlace(place));
        }
    }
</script>