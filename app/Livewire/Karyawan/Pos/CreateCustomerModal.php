<?php

namespace App\Livewire\Karyawan\Pos;

use Livewire\Component;
use App\Models\Customer;

class CreateCustomerModal extends Component
{
    public $name;
    public $phone;
    public $address;
    public $showModal = false;

    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    public function showCreateModal()
    {
        $this->reset(['name', 'address', 'phone']);
        $this->showModal = true;

        $this->dispatch('show-create-modal');
    }

    public function save()
    {
        // 1. Validasi (address sudah nullable)
        $validated = $this->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|min:10|max:15|unique:customers,phone',
            'address' => 'nullable|string|max:255',
        ]);

        // 2. Buat customer dan simpan di variabel $customer
        $customer = Customer::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        // 3. Dispatch event untuk menutup modal (sesuai kode Anda)
        $this->dispatch('hide-create-modal');

        // 4. Dispatch event customerCreated DENGAN MENGIRIMKAN ID
        $this->dispatch('customerCreated', $customer->id); // <--- PERUBAHAN PENTING

        // 5. Dispatch notifikasi (sesuai kode Anda)
        $this->dispatch('notify', ['message' => 'Customer berhasil ditambahkan!']);

        // 6. Reset form
        $this->reset(['name', 'phone', 'address']);
    }

    public function render()
    {
        return view('livewire.karyawan.pos.create-customer-modal');
    }
}
