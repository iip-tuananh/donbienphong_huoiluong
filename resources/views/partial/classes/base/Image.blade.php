<script>
    class Image extends BaseChildClass {
        no_set = [];
        constructor(form, parent) {
            super(form, parent);
        }

        before(form) {
            this.pre = randomString(20);
            this.id = '';
        }

        get path() {
            return this._path || "{{ asset('img/icons/image-icon.png') }}";
        }

        set path(value) {
            this._path = value;
        }

        after(form) {
            let self = this;
            $(document).on('change', `#${this.element_id}`, function(e) {
                let fr = new FileReader();
                fr.readAsDataURL(this.files[0]);
                fr.onload = function(e) {
                    self.path = this.result;
                    if (self.parent.scope) self.parent.scope.$apply();
                };
            })
            setTimeout(() => {
                $(`#${this.element_id}`).val(null);
            });
        }

		clear() {
			$(`#${this.element_id}`).val(null);
		}

        get element_id() {
            return this.pre + '-' + this.id;
        }

        get file() {
            if (!$(`#${this.element_id}`).get(0)) return null;
            return $(`#${this.element_id}`).get(0).files[0];
        }

        get submit_data() {
            return this.file;
        }
    }
</script>
