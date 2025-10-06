@include('partial.classes.common.PromoCampaignCheckpointProduct')
<script>
  class PromoCampaignCheckpoint extends BaseChildClass {

	before(form) {
		this.type = 1;
	}

    after() {
		this.types = [
			{id: 1, name: "Phần trăm"},
			{id: 2, name: "Số tiền"},
		];
    }

    get rowspan() {
        return this.products.length + 2;
    }

    get products() {
        return this._products || []
    }

    set products(value) {
        this._products = (value || []).map(val => new PromoCampaignCheckpointProduct(val, this));
    }

    addProduct(product) {
        if (!this._products) this._products = [];
        let exist = this.products.find(val => val.product_id == product.product_id);
        if (exist) exist.qty++;
        else this._products.push(new PromoCampaignCheckpointProduct({product, product_id: product.product_id}, this));
    }

    removeProduct(index) {
        this._products.splice(index, 1);
    }

	get from() {
		return this._from;
	}

	set from(value) {
		this._from = Number(value);
	}

	get to() {
		return this._to;
	}

	set to(value) {
		this._to = Number(value);
	}

	get value() {
		return this._value;
	}

	set value(value) {
		value = Number(value);
		if (this.type == 1 && value > 100) value = 100;
		this._value = value;
	}

    get submit_data() {
        return {
            from: this.from,
            to: this.to,
			type: this.type,
			value: this.value,
            products: this.products.map(val => val.submit_data)
        }
    }

  }
</script>
