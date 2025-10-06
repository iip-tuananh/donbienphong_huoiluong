<script>
    class Video extends BaseChildClass {

        before(form) {
        }

        after(form) {

        }


        get submit_data() {
            let data =  {
                title: this.title ?? null,
                link: this.link ?? null,
            }

            return data;
        }


    }
</script>
