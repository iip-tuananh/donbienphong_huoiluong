@include('admin.spa.Block')

<script>
    class CloudPool extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
            this.status = 0;
        }

        after(form) {
            this.blocks = form.blocks && form.blocks.length
                ? form.blocks
                : [
                    new Block({ title: '', content: '', galleries: [] }),
                    new Block({ title: '', content: '', galleries: [] }),
                    new Block({ title: '', content: '', galleries: [] }),
                ];
        }

        set blocks(value) {
            this._blocks = (value || []).map(val => new Block(val, this));
        }

        get blocks() {
            return this._blocks;
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = new FormData();

            safeAppend(data, 'title', this.title);
            safeAppend(data, 'title_eng', this.title_eng);

            let image = this.image.submit_data;
            if (image) data.append('image', image);

            this.blocks.forEach((block, bIndex) => {
                data.append(`blocks[${bIndex}][code]`, 'block-'+bIndex);
                data.append(`blocks[${bIndex}][title]`, block.title ?? '');
                data.append(`blocks[${bIndex}][title_eng]`, block.title_eng ?? '');
                data.append(`blocks[${bIndex}][body]`, block.body ?? '');
                data.append(`blocks[${bIndex}][body_eng]`, block.body_eng ?? '');

                block.galleries.forEach((g, gIndex) => {
                    if (g.id) {
                        data.append(`blocks[${bIndex}][galleries][${gIndex}][id]`, g.id);
                    }
                    let gallerySubmit = g.image.submit_data; // Đây có thể là file object
                    if (gallerySubmit) {
                        data.append(`blocks[${bIndex}][galleries][${gIndex}][image]`, gallerySubmit);
                    } else {
                        data.append(`blocks[${bIndex}][galleries][${gIndex}][image_obj]`, g.id);
                    }
                });
            });

            return data;
        }
    }
</script>
