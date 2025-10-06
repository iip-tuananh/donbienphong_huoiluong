@include('admin.projects.ProjectGallery')
<script>
    class Project extends BaseClass {
        statuses = @json(\App\Model\Admin\Service::STATUSES);
        no_set = [];
        all_categories = @json(\App\Model\Admin\PostCategory::getForSelect(2));

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

        get galleries() {
            return this._galleries || [];
        }

        set galleries(value) {
            this._galleries = (value || []).map(val => new ProjectGallery(val, this));
        }

        addGallery(gallery) {
            if (!this._galleries) this._galleries = [];
            let new_gallery = new ProjectGallery(gallery, this);
            this._galleries.push(new_gallery);
            return new_gallery;
        }

        removeGallery(index) {
            this._galleries.splice(index, 1);
        }


        get is_highlight() {
            return this._is_highlight;
        }

        set is_highlight(value) {
            this._is_highlight = !!value;
        }

        get submit_data() {
            let data = {
                name: this.name,
                chu_dau_tu: this.chu_dau_tu,
                hang_muc: this.hang_muc,
                vi_tri: this.vi_tri,
                quy_mo: this.quy_mo,
                nam_hoan_thien: this.nam_hoan_thien,
                khu_vuc: this.khu_vuc,
                is_highlight: this.is_highlight ? 1 : 0,
                content: this.content,
                status: this.status,
            }

            data = jsonToFormData(data);
            let image = $(`#${this.image.element_id}`).get(0).files[0];
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
