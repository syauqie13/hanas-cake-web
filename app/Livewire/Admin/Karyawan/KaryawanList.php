<?php

namespace App\Livewire\Admin\Karyawan;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class KaryawanList extends Component
{
    protected $listeners = [
        'karyawanCreated' => '$refresh',
        'deleteConfirmed' => 'delete',
        'karyawanUpdated' => '$refresh',
    ];

    public $karyawan;

    #[On('karyawanCreated')]
    public function loadKaryawan()
    {
        $this->karyawan = User::where('role', 'karyawan')->get();
    }

    public function mount()
    {
        $this->loadKaryawan();
    }

    public function create()
    {
        $this->dispatch('openCreateModal');
    }

    public function edit($id)
    {
        $this->dispatch('openEditModal', id: $id);
    }

    // 🔹 Step 1: Minta konfirmasi ke SweetAlert (frontend)
    public function deleteConfirm($id)
    {
        $this->dispatch('confirmDelete', id: $id);
    }

    // 🔹 Step 2: Setelah user konfirmasi di JS, event "deleteConfirmed" dikirim balik ke Livewire
    public function delete($id)
    {
        User::findOrFail($id)->delete();
        $this->dispatch('notify', ['message' => 'Karyawan berhasil dihapus.']);
        $this->loadKaryawan(); // refresh data manual
    }

    public function render()
    {
        return view('livewire.admin.karyawan.karyawan-list');
    }
}
