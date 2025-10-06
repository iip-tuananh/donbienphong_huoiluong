@include('admin.posts.BlockGallery')

<script>
    class Block extends BaseChildClass {

        before(form) {
        }

        after(form) {

        }

        get galleries() {
            return this._galleries || [];
        }

        set galleries(value) {
            this._galleries = (value || []).map(val => new BlockGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new BlockGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }

        get submit_data() {
            let data =  {
                code: this.code,
                content: this.content,
                title: this.title,
            }

            // data = jsonToFormData(data);
            //
            // this.galleries.forEach((g, i) => {
            //     if (g.id) data.append(`galleries[${i}][id]`, g.id);
            //     let gallery = g.image.submit_data;
            //     if (gallery) data.append(`galleries[${i}][image]`, gallery);
            //     else data.append(`galleries[${i}][image_obj]`, g.id);
            // })
            return data;
        }


    }
</script>
