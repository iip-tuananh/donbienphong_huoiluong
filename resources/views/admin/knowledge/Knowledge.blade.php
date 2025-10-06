@include('admin.posts.Block')

<script>
    class Knowledge extends BaseClass {
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect(4));
        statuses = @json(\App\Model\Admin\Post::STATUSES);
        no_set = [];

        before(form) {
            this.image = {};
            this.status = 0;
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
            let data = new FormData();

            safeAppend(data, 'name', this.name);
            safeAppend(data, 'cate_id', this.cate_id);
            safeAppend(data, 'intro', this.intro);
            safeAppend(data, 'body', this.body);
            safeAppend(data, 'status', this.status);

            let image = this.image.submit_data;
            if (image) data.append('image', image);

            return data;
        }
    }
</script>
