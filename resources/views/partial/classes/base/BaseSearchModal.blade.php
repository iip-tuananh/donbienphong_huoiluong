<script>
    class BaseSearchModal {
    constructor(options, callback = null) {
        this.id = `${randomString(10)}-${new Date().getTime()}`;
        this.options = options || {};
        this.title = this.options.title || 'Tìm kiếm';
        this.callback = callback;
        this.table_id = this.id + '-table';
        this.base_element = $(`
        <div class="modal fade search-modal" tabindex="-1" id="${this.id}" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="max-width:960px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="semi-bold modal-title">${this.title}</h4>
                        <button type="button" class="close btn" data-dismiss="modal"><i class="fas fa-window-close" style="color:red"></i></button>
                    </div>
                    <div class="modal-body">
                        <table class="modal-table" id="${this.table_id}" style="width:100%">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="fas fa-window-close" style="color:#fff"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `);
        $("body").append(this.base_element);
        this.datatable = new DATATABLE(this.table_id, {...this.options, in_modal: true}).datatable;

        let self = this;

        $(document).on('click', `#${this.table_id} tr`, function (e) {
            e.preventDefault();
            let tr = $(this);
            let obj = self.datatable.row( tr ).data();
            if (!obj) return;
            if (self.callback) self.callback(obj);
        });
    }

    open() {
        this.base_element.modal('show');
        this.datatable.draw();
    }

    close() {
        this.base_element.modal('hide');
    }
}
</script>