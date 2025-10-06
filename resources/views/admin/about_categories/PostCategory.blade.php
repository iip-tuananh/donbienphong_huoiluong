<script>
    class PostCategory extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect());

        before(form) {
            this.image = {};
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

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

		clearImage() {
			if (this.image) this.image.clear();
		}

        get submit_data() {
            let data = {
                name: this.name,
                name_eng: this.name_eng,
                parent_id: this._parent_id,
                intro: this.intro,
                intro_eng: this.intro_eng,
            }
            data = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) data.append('image', image);

            return data;
        }
    }
</script>
