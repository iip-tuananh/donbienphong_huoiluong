<script>
    class DocumentVideo {
        constructor(object, parent) {
            this.no_set = [];
            this.parent = parent;
            if (object) {
                for (let key in object) {
                    if (!this.no_set.includes(key)) this[key] = object[key];
                }
            }
        }

        get submit_data() {
            return {
                link: this.link,
            }
        }

    }
</script>
