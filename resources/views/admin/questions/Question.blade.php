<script>
    class Question extends BaseClass {
        no_set = [];

        before(form) {
        }

        after(form) {

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
