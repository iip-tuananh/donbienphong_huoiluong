<script>
    class BaseChildClassWithFile extends BaseChildClass {

        get scope() {
            return this.parent.scope;
        }

        addFile(field = 'attachments') {
            let file = new File({}, this);
            if (!this[`addition_${field}`]) this[`addition_${field}`] = [];
            this[`addition_${field}`].push(file);
            setTimeout(() => {
                $(`#${file.element_id}`).trigger('click');
            });
        }

        removeFile(index, field = 'attachments') {
            this[`addition_${field}`].splice(index, 1);
        }

        deleteFile(file, field = 'attachments') {
            if (!this[`delete_${field}`]) this[`delete_${field}`] = [];
            this[`delete_${field}`].push(file.id);
            this[field] = this[field].filter(val => val.id != file.id);
        }
    }
</script>
