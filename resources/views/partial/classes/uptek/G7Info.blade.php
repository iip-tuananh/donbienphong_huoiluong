<script>
    class G7Info extends BaseClass {
        no_set = [];
        provinces = @json(\Vanthao03596\HCVN\Models\Province::select('id','name_with_type')->get());

        before(form) {
            this.image = {};
        }

        after(form) {

        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = {
                name: this.name,
                mobile: this.mobile,
                email: this.email,
                province_id: this.province_id,
                district_id: this.district_id,
                ward_id: this.ward_id,
                adress: this.adress,
                status: this.status,
                note: this.note,
            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
            if (image) data.append('image', image);
            return data;
        }
    }
</script>