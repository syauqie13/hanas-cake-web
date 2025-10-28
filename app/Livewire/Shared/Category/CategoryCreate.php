<?php

namespace App\Livewire\Shared\Category;

use Livewire\Component;
use App\Models\Category;

class CategoryCreate extends Component
{
    public $name;
    public $showModal = false;

    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    /**
     * Tampilkan modal dan reset form
     */
    public function showCreateModal()
    {
        $this->reset(['name']);
        $this->showModal = true;

        // Tampilkan modal di frontend (pakai JS)
        $this->dispatch('show-create-modal');
    }

    /**
     * Simpan kategori baru
     */
    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|min:3|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $validated['name'],
        ]);

        // Tutup modal
        $this->dispatch('hide-create-modal');

        // Refresh list kategori
        $this->dispatch('categoryCreated');

        // Notifikasi sukses (misal pakai SweetAlert)
        $this->dispatch('notify', [
            'message' => 'Kategori berhasil ditambahkan!'
        ]);
    }

    public function render()
    {
        return view('livewire.shared.category.category-create');
    }
}
