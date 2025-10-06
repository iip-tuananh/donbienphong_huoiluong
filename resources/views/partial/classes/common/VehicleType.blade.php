<script>
    class VehicleType extends BaseClass {
        categories = @json(\App\Model\Common\VehicleCategory::getForSelect());
        manufacts = @json(\App\Model\Common\VehicleManufact::getForSelect());
        statuses = @json(\App\Model\Common\VehicleType::STATUSES);
        before(form) {
            this.status = 1;
        }

        after(form) {
        }

        get submit_data() {
            return {
                name: this.name,
                vehicle_category_id: this.vehicle_category_id,
                vehicle_manufact_id: this.vehicle_manufact_id,
                status: this.status
            }
        }
    }
</script>