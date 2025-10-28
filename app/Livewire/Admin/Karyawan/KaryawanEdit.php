<?php

namespace App\Livewire\Admin\Karyawan;

use App\Models\User;
use Livewire\Component;

class KaryawanEdit extends Component
{
    public $userId, $name, $email, $role;

    protected $listeners = ['openEditModal' => 'loadData'];

    public function loadData($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role ?? '';

        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        $user = User::findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        $this->dispatch('hideEditModal');
        $this->dispatch('karyawanUpdated');
        $this->dispatch('notify', ['message' => 'Karyawan berhasil diedit.']);
    }

    public function render()
    {
        return view('livewire.admin.karyawan.karyawan-edit');
    }
}
