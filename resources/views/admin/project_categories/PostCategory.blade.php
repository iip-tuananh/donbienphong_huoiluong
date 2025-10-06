<script>
    class PostCategory extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect());

        before(form) {
        }

        after(form) {
            if(this.categories) {
                this.all_categories = this.categories;
            }
        }


        get parent_id() {
            return this._parent_id;
        }

        set parent_id(value) {
            this._parent_id = Number(value);
        }


        get submit_data() {
            let data = {
                name: this.name,
                intro: this.intro,
            }
            data = jsonToFormData(data);

            return data;
        }
    }
</script>
