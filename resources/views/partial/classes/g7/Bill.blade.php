@include('partial.classes.g7.BillProduct')
@include('partial.classes.g7.BillService')
@include('partial.classes.g7.BillOther')
@include('partial.classes.common.PromoCampaign')
<script>
    class Bill extends BaseClass {
        before(form) {
			this.cars = @json(App\Model\Common\Car::getForSelect());
			this.statuses = @json(App\Model\G7\Bill::STATUSES);
			this.service_groups = @json(App\Model\Uptek\Service::getForSelect());
			this.product_caterories = @json(App\Model\Common\ProductCategory::getForSelect());
			this.point_rate = @json(App\Model\Uptek\AccumulatePoint::getPointRate());
			this.all_customers = @json(App\Model\Common\Customer::getForSelect());
            this.no_set = [
                'bill_date', 'service_total_cost', 'product_total_cost', 'total_cost',
                'cost_after_sale', 'cost_after_promo', 'vat_cost', 'cost_after_vat', 'sale_cost'
            ];
            this.vat_percent = 0;
            this.bill_date = moment().format('YYYY-MM-DD HH:mm');
            this.allow_point = false;
            this.update_reminder = false;
            this.today = new Date();
        }

        after(form) {
            this.list = [...this.products, ...this.services, ...this.others].sort((a, b) => a.index - b.index);
            this.sale_cost = form.sale_cost;
            if(form.car) {
                this.registration_deadline = form.car.registration_deadline;
                this.hull_insurance_deadline = form.car.hull_insurance_deadline;
                this.maintenance_dateline = form.car.maintenance_dateline;
                this.insurance_deadline = form.car.insurance_deadline;
            }
        }

		get customers() {
			if (this.car && this.car.customers) return this.car.customers;
			return this.all_customers;
		}

        get point_money()
        {
            return this.points ? (this.points * this.point_rate).toLocaleString('en') : 0;
        }

        set point_money(value)
        {
            this._point_money = Number(value);
        }

        get products() {
            return this._products || [];
        }
        set products(value) {
            this._products = (value || []).map(val => new BillProduct(val, this));
        }
        get list_products() {
            return (this.list || []).filter(val => val.is_product);
        }
        addProduct(product) {
            if (!this.list) this.list = [];
            let exist = this.list_products.find(val => val.product_id == product.product_id);
            if (exist) exist.qty++;
            else this.list.push(new BillProduct({
                product,
                product_id: product.product_id,
                index: this.list.length
            }, this));
        }
        get services() {
            return this._services || []
        }
        set services(value) {
            this._services = (value || []).map(val => new BillService(val, this));
        }
        get list_services() {
            return (this.list || []).filter(val => val.is_service);
        }

        checkServiceExists(id) {
			return this.list_services.find(s => s.group_id == id);
		}

        checkProductExists(id) {
			return this.list_products.find(p => p.product.id == id);
		}

		get others() {
            return this._others || [];
        }
        set others(value) {
            this._others = (value || []).map(val => new BillOther(val, this));
        }
        get list_others() {
            return (this.list || []).filter(val => val.is_other);
        }
        addOther(product) {
            if (!this.list) this.list = [];
            this.list.push(new BillOther({index: this.list.length}, this));
        }

        isValidRegistration() {
            return this.registration_deadline >= moment().format('YYYY-MM-DD');
        }
        isValidHullInsuranceDeadline() {
            return this.hull_insurance_deadline >= moment().format('YYYY-MM-DD');
        }
        isValidMaintenanceDateline() {
            return this.maintenance_dateline >= moment().format('YYYY-MM-DD');
        }
        isValidInsuranceDeadline() {
            return this.insurance_deadline >= moment().format('YYYY-MM-DD');
        }

        addService(service) {
            if (!this.list) this.list = [];
            let exist = this.list_services.find(val => val.service_id == service.service.id && val.group_id == service.id);
            if (exist) exist.qty++;
            else this.list.push(new BillService({
                service_id: service.service.id,
                group_id: service.id,
                service: service.service,
                group: service,
                price: service.price,
                index: this.list.length
            }, this));
        }

        removeItem(index) {
            this.list.forEach((item, i) => {
                if (i > index) item.index--;
            })
            this.list.splice(index, 1);
        }

        // get bill_date() {
        //     return dateGetter(this._bill_date, 'YYYY-MM-DD HH:mm', "HH:mm DD/MM/YYYY");
        // }

        // set bill_date(value) {
        //     this._bill_date = dateSetter(value, "HH:mm DD/MM/YYYY", 'YYYY-MM-DD HH:mm');
        // }

        // get registration_deadline () {
        //     return dateGetter(this._registration_deadline, 'YYYY-MM-DD', "DD/MM/YYYY");
        // }

        // set registration_deadline (value) {
        //     this._registration_deadline = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        // }


        get vehicle_category_id() {
            if (this.mode == 'show') return this._vehicle_category_id;
            return this.car ? this.car.category_id : null;
        }
        set vehicle_category_id(value) {
            this._vehicle_category_id = value;
        }

        get service_total_cost() {
            return this.list_services.reduce((acc, cur) => acc + cur.total_cost, 0);
        }
        get service_total_qty() {
            return this.list_services.reduce((acc, cur) => acc + cur.qty, 0);
        }

        get product_total_qty() {
            return this.list_products.reduce((acc, cur) => acc + cur.qty, 0);
        }
        get product_total_cost() {
            return this.list_products.reduce((acc, cur) => acc + cur.total_cost, 0);
        }

        get total_qty() {
            return this.service_total_qty + this.product_total_qty;
        }

		get other_total_cost() {
            return this.list_others.reduce((acc, cur) => acc + cur.total_cost, 0);
        }

        get total_cost() {
            return this.service_total_cost + this.product_total_cost + this.other_total_cost;
        }
        get sale_cost() {
            return this._sale_cost ? this._sale_cost.toLocaleString('en') : 0;
        }
        set sale_cost(value) {
            value = parseNumberString(value);
            if (value > this.total_cost) value = this.total_cost;
            this._sale_cost = value;
        }
        get cost_after_sale() {
            return this.total_cost - (this._sale_cost || 0);
        }
		get promo_value() {
			if (this.mode == 'show') return this._promo_value;
			if (!this.promo || this.promo.type != 1) return 0;
			if (this.promo.type == 1) {
				let checkpoint = this.promo.checkpoints.find(val => val._from <= this.cost_after_sale && val._to > this.cost_after_sale);
				if (!checkpoint) return 0;
				if (checkpoint.type == 1) return Math.round(this.cost_after_sale * checkpoint.value / 100);
				else return checkpoint.value > this.cost_after_sale ? this.cost_after_sale : checkpoint.value;
			}
		}

		set promo_value(value) {
			this._promo_value = value;
		}
		get cost_after_promo() {
            return this.cost_after_sale - this.promo_value;
        }
        get vat_percent() {
            return this._vat_percent || 0;
        }
        set vat_percent(value) {
            value = Number(value);
            if (value > 99) value = 99;
            this._vat_percent = value;
        }
        get vat_cost() {
            return Math.round(this.cost_after_promo * this.vat_percent / 100);
        }
        get cost_after_vat() {
            return this.cost_after_promo + this.vat_cost;
        }

		get promo() {
            return this._promo;
        }
        set promo(value) {
            this._promo = new PromoCampaign(value, {});
        }

		get promo_products() {
			if (this.mode == 'show') return this._promo_products;
			if (!this.promo || (this.promo.type != 2 && this.promo.type != 3)) return [];
			if (this.promo.type == 2) {
				let checkpoint = this.promo.checkpoints.find(val => val._from <= this.cost_after_sale && val._to > this.cost_after_sale);
				if (!checkpoint) return [];
				return checkpoint.products;
			}
			if (this.promo.type == 3) {
				return this._promo_products;
			}
		}

		set promo_products(value) {
			this._promo_products = value;
		}

		calculatePromo() {
			if (this.promo && this.promo.type == 3) {
				let exist = this.list.find(val => val.product_id == this.promo.product_id);
				let checkpoint = this.promo.checkpoints[0];
				if (!exist || !checkpoint || checkpoint._from > exist._qty) return [];
				let ratio = Math.floor(exist._qty / checkpoint._from);
				this.promo_products = checkpoint.products.map(p => ({...p, qty: p._qty * ratio}));
			}
		}

        get submit_data() {
            return {
                bill_date: this.bill_date,
                car_id: this.car_id,
                customer_id: this.customer_id,
                // payment_method: this.payment_method,
                service_total_cost: this.service_total_cost,
                product_total_cost: this.product_total_cost,
                total_cost: this.total_cost,
                sale_cost: this._sale_cost || 0,
                cost_after_sale: this.cost_after_sale,
				promo_value: this.promo_value,
				cost_after_promo: this.cost_after_promo,
                vat_percent: this.vat_percent,
                vat_cost: this.vat_cost,
                cost_after_vat: this.cost_after_vat,
                note: this.note,
				promo_id: (this.promo || {}).id,
				promo_products: (this.promo_products || []).map(val => ({
					product_id: val.product_id,
					qty: val.qty
				})),
                list: this.list.map(val => val.submit_data),
                allow_point: this.allow_point,
                points: this.points,
                point_money: this._point_money,
                update_reminder: this.update_reminder,
                registration_deadline: this.registration_deadline,
                hull_insurance_deadline: this.hull_insurance_deadline,
                maintenance_dateline: this.maintenance_dateline,
                insurance_deadline: this.insurance_deadline,
            }
        }
    }
</script>
