@include('admin.posts.Block')
@include('admin.posts.Video')

<script>
    class Post extends BaseClass {
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect());
        all_tags = @json(\App\Model\Admin\Tag::getForSelect());
        statuses = @json(\App\Model\Admin\Post::STATUSES);
        types = @json(\App\Model\Admin\Post::TYPES);
        no_set = [];

        before(form) {
            this.image = {};
            this.status = 0;
            this.type = 1;
            this.tag_ids = [];
            this.files = [];
        }

        after(form) {
            this.videos = form.videos && form.videos.length
                ? form.videos
                : [
                    new Video({ title: null}),
                ];
        }

        get videos() {
            return this._videos || [];
        }

        set videos(value) {
            this._videos = (value || []).map(val => new Video(val, this));
        }

        addVideo(result) {
            if (!this._videos) this._videos = [];
            let new_result = new Video(result, this);
            this._videos.push(new_result);
            return new_result;
        }

        removeVideo(index) {
            this._videos.splice(index, 1);
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get price() {
            return this._price ? this._price.toLocaleString('en') : '';
        }

        set price(value) {
            value = parseNumberString(value);
            this._price = value;
        }

        get is_hot() {
            return this._is_hot;
        }

        set is_hot(value) {
            this._is_hot = !!value;
        }

        get submit_data() {
            let data = {
                cate_id: this.cate_id,
                name: this.name,
                intro: this.intro,
                body: this.body,
                status: this.status,
                type: this.type,
                price: this._price,
                tag_ids: this.tag_ids.map(val => val.id),
                is_hot: this.is_hot ? 1 : 0,
                videos: this.videos.map(val => val.submit_data)
            }
            let fd = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) fd.append('image', image);

            (this.files || []).forEach(f => {
                // file cũ: object có id (và KHÔNG phải File/Blob)
                const looksLikeFileObject = f && typeof f === 'object' && typeof f.size === 'number' && typeof f.name === 'string';
                if (f && typeof f === 'object' && 'id' in f && !looksLikeFileObject) {
                    fd.append('existing_file_ids[]', f.id); // chỉ gửi id
                    return;
                }
                // file mới: File / Blob (trên 1 số browser không có instanceof File)
                if (looksLikeFileObject) {
                    fd.append('files[]', f); // gửi binary
                }
            });


            return fd;
        }
    }
</script>
