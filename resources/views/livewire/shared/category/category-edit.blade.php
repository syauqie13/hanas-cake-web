<div>
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="border-0 shadow-lg modal-content rounded-3">
                <form wire:submit.prevent="update">
                    <!-- Header -->
                    <div class="pb-0 border-0 modal-header">
                        <h5 class="modal-title fw-bold text-dark" id="editModalLabel">
                            <i class="mr-2 fas fa-user-edit text-primary"></i> Edit Karyawan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="fs-4">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Nama</label>
                            <input type="text" class="rounded-lg shadow-sm form-control" placeholder="Masukkan nama"
                                wire:model.defer="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Slug</label>
                            <input type="text" class="rounded-lg shadow-sm form-control"
                                placeholder="Slug otomatis dari nama (bisa diubah)" wire:model.defer="slug">
                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-0 border-0 modal-footer">
                        <button type="button" class="border btn btn-light" data-dismiss="modal">
                            <i class="mr-1 fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="shadow-sm btn btn-primary">
                            <i class="mr-1 fas fa-save"></i> Simpan Perubahan
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
