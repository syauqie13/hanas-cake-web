<?php

namespace App\Livewire\Shared\Category;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Category;

class CategoryList extends Component
{
    public $categories;

    protected $listeners = [
        'categoryCreated' => 'loadCategories',
        'categoryUpdated' => 'loadCategories',
        'deleteConfirmed' => 'delete'
    ];

    #[On('categoryCreated')]
    public function mount()
    {
        $this->loadCategories();
    }

    // 🔹 Ambil ulang data kategori
    public function loadCategories()
    {
        $this->categories = Category::latest()->get();
    }

    // 🔹 Buka modal create
    public function create()
    {
        $this->dispatch('openCreateModal');
    }

    // 🔹 Buka modal edit
    public function edit($id)
    {
        $this->dispatch('openEditModal', id: $id);
    }

    // 🔹 Konfirmasi hapus (trigger ke JS alert)
    public function deleteConfirm($id)
    {
        $this->dispatch('confirmDelete', id: $id);
    }

    // 🔹 Hapus data setelah konfirmasi
    public function delete($id)
    {
        Category::findOrFail($id)->delete();

        // Perbarui data & tampilkan notifikasi
        $this->loadCategories();
        $this->dispatch('notify', ['message' => 'Kategori berhasil dihapus.']);
    }

    public function render()
    {
        return view('livewire.shared.category.category-list', [
            'categories' => $this->categories
        ]);
    }
}
