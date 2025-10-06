<script>
    class File extends BaseChildClass {
        constructor(form, parent, $sce) {
            super(form, parent);
            this.no_set = [];
            this.$sce = $sce;

        }

        before(form) {
            this.pre = randomString(20);
            this.id = '';
        }

        after(form) {
            let self = this;
            $(document).on('change', `#${this.element_id}`, function (e) {
                let filename = e.target.files[0].name;
                self.name = filename;
                if (self.parent.scope) self.parent.scope.$apply();
            })
        }

        get element_id() {
            return this.pre + '-' + this.id;
        }

        get file() {
            if (!$(`#${this.element_id}`).get(0)) return null;
            return $(`#${this.element_id}`).get(0).files[0];
        }

        // Thêm getter preview_url để tạo blob URL tin cậy
        get preview_url() {
            console.log(this.$sce)
            if (!this.file) return null;
            // Tạo URL tạm thời cho file
            let blobUrl = URL.createObjectURL(this.file);
            // Sử dụng $sce để tin cậy URL đó
            return this.$sce.trustAsResourceUrl(blobUrl);
        }

        get submit_data() {
            return this.file;
        }
    }
</script>
