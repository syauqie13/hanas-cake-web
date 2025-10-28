<div>
    <div wire:ignore.self class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <form wire:submit.prevent="save">
                    <!-- Header -->
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="createModalLabel">
                            <i class="fas fa-user-plus text-primary mr-2"></i> Tambah Karyawan
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
                            <label class="fw-semibold">Password</label>
                            <input type="password" class="form-control rounded-lg shadow-sm"
                                placeholder="Masukkan password" wire:model.defer="password">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan
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
