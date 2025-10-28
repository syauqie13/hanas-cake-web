<?php

namespace App\Livewire\Admin\Karyawan;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class KaryawanCreate extends Component
{

    public $name, $email, $password;
    public $showModal = false;
    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    // Tampilkan modal dan reset form
    public function showCreateModal()
    {
        $this->reset(['name', 'email', 'password']);
        $this->showModal = true;

        $this->dispatch('show-create-modal');
    }

    // Simpan karyawan baru
    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'karyawan',
        ]);

        $this->dispatch('hide-create-modal');

        // Emit event untuk refresh list karyawan
        $this->dispatch('karyawanCreated');

        // Notifikasi SweetAlert
        $this->dispatch('notify', [
            'message' => 'Karyawan berhasil ditambahkan!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.karyawan.karyawan-create');
    }
}
