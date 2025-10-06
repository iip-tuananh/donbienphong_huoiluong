<script>
    class PostCategory extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect(true));

        before(form) {
        }

        after(form) {
        }


        get parent_id() {
            return this._parent_id;
        }

        set parent_id(value) {
            this._parent_id = Number(value);
        }


        get submit_data() {
            let data = {
                parent_id: this.parent_id,
                name: this.name,
                intro: this.intro,
                show_home_page: this.show_home_page,
            }
            data = jsonToFormData(data);

            return data;
        }
    }
</script>
