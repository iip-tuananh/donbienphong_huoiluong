@include('admin.abouts.ResultsAchieved')

<script>
    class About extends BaseClass {
        no_set = [];

        before(form) {
            this.image = {};
            this.image_back = {};
        }

        after(form) {
            this.results = form.results && form.results.length
                ? form.results
                : [
                    new Result({ title: null}),
                ];
        }

        get results() {
            return this._results || [];
        }

        set results(value) {
            this._results = (value || []).map(val => new Result(val, this));
        }

        addResult(result) {
            if (!this._results) this._results = [];
            let new_result = new Result(result, this);
            this._results.push(new_result);
            return new_result;
        }

        removeResult(index) {
            this._results.splice(index, 1);
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
                title: this.title,
                intro: this.intro,
                youtube: this.youtube,
                service_title: this.service_title,
                service_description: this.service_description,
                experience_number: this.experience_number,
                experience_text: this.experience_text,
                phone_number: this.phone_number,
                results: this.results.map(val => val.submit_data)

            }

            data = jsonToFormData(data);

            let image = this.image.submit_data;
            if (image) data.append('image', image);
            let image_back = this.image_back.submit_data;
            if (image_back) data.append('image_back', image_back);

            return data;
        }
    }
</script>
