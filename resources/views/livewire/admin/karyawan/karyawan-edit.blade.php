<div>
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <form wire:submit.prevent="update">
                    <!-- Header -->
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="editModalLabel">
                            <i class="fas fa-user-edit text-primary mr-2"></i> Edit Karyawan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="fs-4">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Nama</label>
                            <input type="text" class="form-control rounded-lg shadow-sm" placeholder="Masukkan nama"
                                wire:model.defer="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Email</label>
                            <input type="email" class="form-control rounded-lg shadow-sm" placeholder="Masukkan email"
                                wire:model.defer="email">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mb-2">
                            <label class="fw-semibold">Role</label>
                            <select class="form-control rounded-lg shadow-sm" wire:model.defer="role">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
        <script>
            window.addEventListener('showEditModal', () => {
                $('#editModal').modal('show');
            });

            window.addEventListener('hideEditModal', () => {
                $('#editModal').modal('hide');
            });

            // Tambahan: supaya backdrop hilang total setelah Livewire refresh
            document.addEventListener("livewire:navigated", () => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });
        </script>

        <script>
            window.addEventListener('notify', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: event.detail.message || 'Aksi berhasil dijalankan!',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });
        </script>
    @endpush


</div>
