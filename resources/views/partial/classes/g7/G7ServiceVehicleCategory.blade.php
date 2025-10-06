@include('partial.classes.g7.G7ServiceVehicleCategoryGroup')
<script>
  class G7ServiceVehicleCategory extends BaseChildClass {

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
        }, 0) + 1;
    }

    get groups() {
        return this._groups || []
    }

    set groups(value) {
        this._groups = (value || []).map(val => new G7ServiceVehicleCategoryGroup(val, this));
    }

    addGroup(product) {
        if (!this._groups) this._groups = [];
        this._groups.push(new G7ServiceVehicleCategoryGroup({}, this));
    }

    removeGroup(index) {
        this._groups.splice(index, 1);
    }

    get submit_data() {
        return {
            vehicle_category_id: this.vehicle_category_id,
            groups: this.groups.map(val => val.submit_data)
        }
    }

  }
</script>
