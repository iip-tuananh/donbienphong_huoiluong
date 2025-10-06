<script>
    class Category extends BaseClass {
        no_set = [];
        all_categories = @json(\App\Model\Admin\Category::getForSelect());

        before(form) {
            this.image = {};
            this.banner = {};
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
// Ảnh đại diện
        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

		clearImage() {
			if (this.image) this.image.clear();
		}
// Ảnh banner
        get banner() {
            return this._banner;
        }

        set banner(value) {
            this._banner = new Image(value, this);
        }

		clearBanner() {
			if (this.banner) this.banner.clear();
		}

        get submit_data() {
            let data = {
                name: this.name,
                parent_id: this._parent_id,
                intro: this.intro,
                short_des: this.short_des,
                icon: this.icon,
                show_home_page: this.show_home_page,
                link: this.link,
            }
            data = jsonToFormData(data);
            let image = this.image.submit_data;
            let banner = this.banner.submit_data;
            console.log(image);
            console.log(banner);
            if (image) data.append('image', image);
            if (banner) data.append('banner', banner);

            return data;
        }
    }
</script>
