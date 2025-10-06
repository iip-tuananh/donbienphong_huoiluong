@include('partial.classes.common.PromoCampaignCheckpoint')
<script>
    class PromoCampaign extends BaseClass {

	before(form) {
		this.all_g7s = @json(App\Model\Uptek\G7Info::getForSelect());
		this.all_types = [
			{id: 1, name: "Chiết khấu hóa đơn"},
			{id: 2, name: "Tặng sản phẩm theo hóa đơn"},
			{id: 3, name: "Mua hàng tặng hàng"}
		];
		this.type = 1;
	}

    after(form) {
		this.g7_ids = (this.g7s || []).map(val => val.id);
    }

	useProduct(product) {
		this.product = product;
		this.product_id = product.id;
	}

	changeType() {
		if (this.type == 3) {
			this.checkpoints = [{}];
		}
	}

    get checkpoints() {
        return this._checkpoints || []
    }

    set checkpoints(value) {
        this._checkpoints = (value || []).map(val => new PromoCampaignCheckpoint(val, this));
    }

    addCheckpoint(product) {
        if (!this._checkpoints) this._checkpoints = [];
        this._checkpoints.push(new PromoCampaignCheckpoint({}, this));
    }

    removeCheckpoint(index) {
        this._checkpoints.splice(index, 1);
    }

	get for_all() {
        return this._for_all
    }

    set for_all(value) {
        this._for_all = !!Number(value);
    }

    get submit_data() {
        return {
            name: this.name,
			type: this.type,
			for_all: this.for_all,
			g7_ids: this.g7_ids,
			limit: this.limit,
			product_id: this.product_id,
			note: this.note,
			start_date: this.start_date,
			end_date: this.end_date,
			checkpoints: this.checkpoints.map(val => val.submit_data)
        };
    }

  }
</script>
