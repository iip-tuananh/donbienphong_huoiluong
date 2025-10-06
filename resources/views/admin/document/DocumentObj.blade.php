@include('admin.document.DocumentGallery')
@include('admin.document.DocumentVideo')

<script>
    class DocumentObj extends BaseClass {
        no_set = [];

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


        clearImage() {
            if (this.image) this.image.clear();
            if (this.image_back) this.image_back.clear();
        }

        get galleries() {
            return this._galleries || [];
        }

        set galleries(value) {
            this._galleries = (value || []).map(val => new DocumentGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new DocumentGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }

        get videos() {
            return this._videos || [];
        }

        set videos(value) {
            this._videos = (value || []).map(val => new DocumentVideo(val, this));
        }

        addVideo() {
            if (!this._videos) this._videos = [];
            this._videos.push(new DocumentVideo({}, this));
            console.log( this._videos)
        }

        removeVideo(index) {
            this._videos.splice(index, 1);
        }


        get submit_data() {
            let data = {
                title: this.title,
                title_eng: this.title_eng,
                videos: this.videos.map(val => val.submit_data)
            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);

            this.galleries.forEach((g, i) => {
                if (g.id) data.append(`galleries[${i}][id]`, g.id);
                let gallery = g.image.submit_data;
                if (gallery) data.append(`galleries[${i}][image]`, gallery);
                else data.append(`galleries[${i}][image_obj]`, g.id);
            })

            return data;
        }
    }
</script>
