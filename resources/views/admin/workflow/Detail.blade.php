<script>
    class Detail extends BaseChildClass {

        before(form) {
        }

        after(form) {

        }


        get submit_data() {
            let data =  {
                title: this.title ?? null,
                intro: this.intro ?? null,
            }

            return data;
        }


    }
</script>
