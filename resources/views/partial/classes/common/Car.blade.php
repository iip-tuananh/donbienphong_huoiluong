<script>
    class Car extends BaseClass {
        before(form) {
            this.all_customers = @json(\App\Model\Common\Customer::select('id','name')->get());
            // this.categories = @json(\App\Model\Common\VehicleCategory::select('id','name')->get());
            this.manufacts = @json(\App\Model\Common\VehicleManufact::select('id','name')->get());
            this.license_plates = @json(\App\Model\Common\LicensePlate::getForSelect());
            this.types = {};
        }

        after(form) {
            if(form.registration_deadline) this._registration_deadline = form.registration_deadline;
            if(form.hull_insurance_deadline) this._hull_insurance_deadline = form.hull_insurance_deadline;
            if(form.maintenance_dateline) this._maintenance_dateline = form.maintenance_dateline;
            if(form.insurance_deadline) this._insurance_deadline = form.insurance_deadline;
            if(form.customers) this.customer_ids = form.customers.map(function(val, index) {
                return val.id;
            });
        }

        get registration_deadline() {
            return dateGetter(this._registration_deadline, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set registration_deadline(value) {
            this._registration_deadline = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get hull_insurance_deadline() {
            return dateGetter(this._hull_insurance_deadline, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set hull_insurance_deadline(value) {
            this._hull_insurance_deadline = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get maintenance_dateline() {
            return dateGetter(this._maintenance_dateline, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set maintenance_dateline(value) {
            this._maintenance_dateline = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get insurance_deadline() {
            return dateGetter(this._insurance_deadline, 'YYYY-MM-DD', "DD/MM/YYYY");
        }

        set insurance_deadline(value) {
            this._insurance_deadline = dateSetter(value, "DD/MM/YYYY", 'YYYY-MM-DD');
        }

        get submit_data() {
            return {
                license_plate: this.license_plate,
                customer_ids: this.customer_ids,
                // category_id: this.category_id,
                manufact_id: this.manufact_id,
                type_id: this.type_id,
                registration_deadline: this._registration_deadline,
                hull_insurance_deadline: this._hull_insurance_deadline,
                maintenance_dateline: this._maintenance_dateline,
                insurance_deadline: this._insurance_deadline,
                // status: this.status
            }
        }
    }
</script>