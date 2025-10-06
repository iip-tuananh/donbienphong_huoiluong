@include('partial.classes.uptek.ServiceVehicleCategoryGroup')
<script>
  class ServiceVehicleCategory extends BaseChildClass {

    get available_categories() {
        let used = this.parent.service_vehicle_categories.reduce((acc, cur) => {
            if (cur != this) acc.push(cur.vehicle_category_id);
            return acc;
        }, []);
        return this.parent.all_vehicle_categories.filter(val => !arrayInclude(used, val.id));
    }

    get rowspan() {
        return this.groups.reduce((acc, cur) => {
            return acc + cur.rowspan;
        }, 0) + 2;
    }

    get groups() {
        return this._groups || []
    }

    set groups(value) {
        this._groups = (value || []).map(val => new ServiceVehicleCategoryGroup(val, this));
    }

    addGroup(product) {
        if (!this._groups) this._groups = [];
        this._groups.push(new ServiceVehicleCategoryGroup({}, this));
    }

    removeGroup(index) {
        this._groups.splice(index, 1);
    }

    get submit_data() {
        return {
			id: this.id,
            vehicle_category_id: this.vehicle_category_id,
            groups: this.groups.map(val => val.submit_data)
        }
    }

  }
</script>
