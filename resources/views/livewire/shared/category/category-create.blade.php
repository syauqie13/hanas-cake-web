<div>
    <div wire:ignore.self class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="border-0 shadow-lg modal-content rounded-3">
                <form wire:submit.prevent="save">
                    <!-- Header -->
                    <div class="pb-0 border-0 modal-header">
                        <h5 class="modal-title fw-bold text-dark" id="createModalLabel">
                            <i class="mr-2 fas fa-user-plus text-primary"></i> Tambah Karyawan
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
                    </div>

                    <!-- Footer -->
                    <div class="pt-0 border-0 modal-footer">
                        <button type="button" class="border btn btn-light" data-bs-dismiss="modal">
                            <i class="mr-1 fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="shadow-sm btn btn-primary">
                            <i class="mr-1 fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            // Tampilkan modal ketika event Livewire dipanggil
            // Tampilkan modal create
            window.addEventListener('show-create-modal', () => {
                $('#createModal').modal('show');
            });

            // Tutup modal create
            window.addEventListener('hide-create-modal', () => {
                $('#createModal').modal('hide');
            });

            // Ini berlaku untuk SEMUA modal
            document.addEventListener("livewire:navigated", () => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });
        </script>
    @endpush
</div>
