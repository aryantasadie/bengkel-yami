<div class="modal-backdrop" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title text-danger">Konfirmasi Hapus</h3>
                <button type="button" class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="delete-message">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger delete-confirm">Ya, Hapus Data</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-backdrop.active {
    display: flex;
}
.modal-dialog {
    background: white;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 400px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
}
@keyframes modalSlideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--neutral-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
}
.modal-close {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--neutral-400);
    cursor: pointer;
}
.modal-close:hover {
    color: var(--neutral-700);
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--neutral-200);
    background: var(--neutral-50);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.btn-outline {
    background: white;
    border: 1px solid var(--neutral-300);
    color: var(--neutral-700);
}
.btn-outline:hover {
    background: var(--neutral-50);
    color: var(--neutral-900);
}
.btn-danger {
    background: var(--danger);
    color: white;
}
.btn-danger:hover {
    background: #B91C1C;
}
</style>
