<!-- Reusable Confirm Action Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="confirmActionModalLabel">Konfirmasi Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-question-circle text-warning fs-1" id="confirmModalIcon"></i>
                </div>
                <h6 class="fw-semibold mb-2" id="confirmModalTitle">Apakah Anda yakin?</h6>
                <p class="text-muted small mb-0" id="confirmModalMessage">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <form id="confirmModalForm" method="POST" action="">
                    @csrf
                    <span id="confirmModalMethod"></span>
                    <button type="submit" class="btn btn-danger px-4" id="confirmModalBtn">Ya, Lanjutkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
