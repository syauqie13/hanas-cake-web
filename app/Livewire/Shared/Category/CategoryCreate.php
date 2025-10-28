<?php

namespace App\Livewire\Shared\Category;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryCreate extends Component
{
    public $name, $slug;
    public $showModal = false;

    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    /**
     * Tampilkan modal dan reset form
     */
    public function showCreateModal()
    {
        $this->reset(['name', 'slug']);
        $this->showModal = true;

        // Tampilkan modal di frontend (pakai JS)
        $this->dispatch('show-create-modal');
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
    }

    public function updatedName()
    {
        $this->generateSlug();
    }

    /**
     * Simpan kategori baru
     */
    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|min:3|max:255|unique:categories,name',
            'slug' => 'required|string|unique:products,slug',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
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
