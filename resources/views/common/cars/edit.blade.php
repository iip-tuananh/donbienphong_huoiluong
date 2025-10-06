<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="POST" role="form" id="editModalForm">
        @csrf
        <div class="modal-dialog" style="max-width: 650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="semi-bold">Sửa xe</h4>
                </div>
                <div class="modal-body">
                    @include('common.cars.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                        <i ng-if="!loading.submit" class="fa fa-save"></i>
                        <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                        Lưu
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>