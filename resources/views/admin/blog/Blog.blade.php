
<script>
    class Blog extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect(3));

        before(form) {
            this.image = {};
            this.status = 1;
        }

        after(form) {
        }


        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }


        clearImage() {
            if (this.image) this.image.clear();
            if (this.image_back) this.image_back.clear();
        }


        get submit_data() {
            let data = {
                title: this.title,
                title_eng: this.title_eng,
                body: this.body,
                body_eng: this.body_eng,
                intro_eng: this.intro_eng,
                intro: this.intro,
                cate_id: this.cate_id,
                status: this.status,
            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);
            return data;
        }
    }
</script>
