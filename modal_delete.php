<!-- Modal xác nhận xoá -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xoá</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Huỷ</button>
                <!-- Nút xác nhận xoá sẽ submit form -->
                <button type="submit" form="deleteForm" name="delete_medicine"
                    class="btn btn-danger btn-sm">Xoá</button>
            </div>
        </div>
    </div>
</div>