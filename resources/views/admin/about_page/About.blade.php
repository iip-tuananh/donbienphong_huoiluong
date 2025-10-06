@include('admin.abouts.ResultsAchieved')

<script>
    class About extends BaseClass {
        no_set = [];

        before(form) {
            this.banner = {};
            this.image = {};
            this.image_second = {};
        }

        after(form) {
        }


        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get image_second() {
            return this._image_second;
        }

        set image_second(value) {
            this._image_second = new Image(value, this);
        }

        get banner() {
            return this._banner;
        }

        set banner(value) {
            this._banner = new Image(value, this);
        }

        clearImage() {
            if (this.image) this.image.clear();
        }


        get submit_data() {
            let data = {
                title: this.title,
                intro: this.intro,
                title_1: this.title_1,
                intro_1: this.intro_1,
                content_1: this.content_1,
                title_2: this.title_2,
                intro_2: this.intro_2,
                content_2: this.content_2,
            }

            data = jsonToFormData(data);

            let banner = this.banner.submit_data;
            if (banner) data.append('banner', banner);
            let image = this.image.submit_data;
            if (image) data.append('image', image);
            let image_second = this.image_second.submit_data;
            if (image_second) data.append('image_second', image_second);

            return data;
        }
    }
</script>
