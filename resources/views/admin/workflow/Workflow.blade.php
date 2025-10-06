@include('admin.workflow.Detail')

<script>
    class Workflow extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
            this.image_back = {};
        }

        after(form) {
            this.detail = form.content && form.content.length
                ? form.content
                : [
                    new Detail({ title: null, content: null}),
                ];
        }

        get detail() {
            return this._content || [];
        }

        set detail(value) {
            this._content = (value || []).map(val => new Detail(val, this));
        }

        addResult(result) {
            if (!this._content) this._content = [];
            let new_result = new Detail(result, this);
            this._content.push(new_result);
            return new_result;
        }

        removeResult(index) {
            this._content.splice(index, 1);
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get image_back() {
            return this._image_back;
        }

        set image_back(value) {
            this._image_back = new Image(value, this);
        }

        clearImage() {
            if (this.image) this.image.clear();
            if (this.image_back) this.image_back.clear();
        }


        get submit_data() {
            let data = {
                detail: this.detail.map(val => val.submit_data)

            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);


            return data;
        }
    }
</script>
