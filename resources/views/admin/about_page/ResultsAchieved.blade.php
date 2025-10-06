<script>
    class Result extends BaseChildClass {

        before(form) {
        }

        after(form) {

        }


        get submit_data() {
            let data =  {
                title: this.title ?? null,
                content: this.content ?? null,
            }

            return data;
        }


    }
</script>
