<script>
    class PolivicyDetail {
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
                title: this.title,
                title_eng: this.title_eng,
                topic_id: this.topic_id,
                content: this.content,
                content_eng: this.content_eng,
            }
        }

    }
</script>
