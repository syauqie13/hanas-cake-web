<?php

namespace App\Livewire\Karyawan\Pos;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;     // model dari tabel orders
use App\Models\Product;   // model dari tabel products
use App\Models\Category;  // model dari tabel categories
use App\Models\Inventory; // model dari tabel inventories
use App\Models\OrderItem; // model dari tabel order_items
use App\Models\ProductRecipe; // model dari tabel product_recipes

#[Layout('components.layouts.pos')]
class PosComponent extends Component
{

    // Properti untuk state
    public $search = '';
    public $selectedCategory = null;
    public $cart = []; // Ini akan menyimpan keranjang belanja

    // Properti untuk pembayaran
    public $total = 0;
    public $paid_amount = 0;
    public $change_amount = 0;
    public $payment_method = 'tunai';
    public $payment_status = 'lunas';
    public $customerSearch = '';
    public $customerResults = [];
    public $selectedCustomerId = null;
    public $selectedCustomerName = null;

    // Listener untuk menghitung kembalian secara real-time
    protected $listeners = ['calculateChange', 'customerCreated'];

    public function mount()
    {
        $this->calculateTotal();
    }

    // Fungsi ini dipanggil setiap kali ada perubahan pada properti
    public function render()
    {
        // Ambil produk berdasarkan pencarian dan kategori
        $products = Product::where('stock', '>', 0)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->get();

        $categories = Category::all();

        // Hitung total dan kembalian
        $this->calculateTotal();
        $this->calculateChange();

        return view('livewire.karyawan.pos.pos-component', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    // --- Manajemen Customer ---

    public function create()
    {
        $this->dispatch('openCreateModal');
    }

    // Metode ini akan dipanggil otomatis setiap kali $customerSearch berubah
    public function updatedCustomerSearch($value)
    {
        if (strlen($value) < 2) {
            $this->customerResults = [];
            return;
        }

        // Cari customer berdasarkan nama atau no. telepon
        $this->customerResults = Customer::where('name', 'like', '%' . $value . '%')
            ->orWhere('phone', 'like', '%' . $value . '%')
            ->take(5) // Ambil 5 hasil teratas saja
            ->get();
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->selectedCustomerId = $customer->id;
            $this->selectedCustomerName = $customer->name;

            // Kosongkan hasil pencarian dan input search
            $this->customerSearch = '';
            $this->customerResults = [];
        }
    }

    public function clearCustomer()
    {
        $this->selectedCustomerId = null;
        $this->selectedCustomerName = null;
    }

    // Metode ini dipanggil oleh listener saat modal berhasil buat customer
    public function customerCreated($customerId)
    {
        $this->selectCustomer($customerId);
        // Di sini Anda bisa tambahkan dispatch event untuk menutup modal
        // $this->dispatch('close-modal');
    }

    // --- Manajemen Keranjang (Cart) ---

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product)
            return;

        // Cek apakah produk sudah ada di keranjang
        if (isset($this->cart[$productId])) {
            // Cek stok
            if ($this->cart[$productId]['jumlah'] < $product->stock) {
                $this->cart[$productId]['jumlah']++;
            }
        } else {
            // Tambah baru ke keranjang
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price - ($product->price * $product->discount / 100), // Harga setelah diskon
                'discount' => $product->discount,
                'stock' => $product->stock,
                'jumlah' => 1,
            ];
        }

        $this->calculateTotal();
    }

    public function updateCartQuantity($productId, $jumlah)
    {
        if (isset($this->cart[$productId])) {
            $product = Product::find($productId);
            if ($jumlah > $product->stock) {
                $this->cart[$productId]['jumlah'] = $product->stock;
                session()->flash('error', 'Stok tidak mencukupi.');
            } elseif ($jumlah <= 0) {
                $this->removeFromCart($productId);
            } else {
                $this->cart[$productId]['jumlah'] = $jumlah;
            }
        }
        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->paid_amount = 0;

        // TAMBAHKAN DUA BARIS INI
        $this->selectedCustomerId = null;
        $this->selectedCustomerName = null;

        $this->calculateTotal(); // Ini akan me-reset $total menjadi 0
    }

    // --- Logika Perhitungan ---

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['jumlah'];
        }
    }

    public function calculateChange()
    {
        $paid = (float) $this->paid_amount;
        if ($paid >= $this->total) {
            $this->change_amount = $paid - $this->total;
        } else {
            $this->change_amount = 0;
        }
    }

    // --- Proses Pembayaran (Transaksi) ---

    public function processPayment()
    {
        // Validasi
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong.');
            return;
        }

        if ($this->paid_amount < $this->total) {
            $this->payment_status = 'belum_lunas';
            // Anda bisa memutuskan apakah transaksi belum lunas boleh disimpan atau tidak
            session()->flash('error', 'Jumlah bayar kurang dari total.');
            return; // Kita anggap harus lunas
        } else {
            $this->payment_status = 'lunas';
        }

        // Gunakan Database Transaction untuk memastikan semua data konsisten
        DB::beginTransaction();

        try {
            // 1. Buat Order
            $order = Order::create([
                'customer_id' => $this->selectedCustomerId,
                'cashier_id' => Auth::id(), // Karyawan yang login
                'tanggal' => now(),
                'total' => $this->total,
                'paid_amount' => $this->paid_amount,
                'change_amount' => $this->change_amount,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'order_type' => 'pos', // Sesuai skema
                'status' => 'selesai', // Transaksi POS langsung selesai
            ]);

            // 2. Buat Order Items (Detail Pesanan)
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['price'],
                    'subtotal' => $item['price'] * $item['jumlah'],
                ]);

                // 3. Kurangi Stok Produk (Opsional, tergantung model stok Anda)
                // $product = Product::find($item['product_id']);
                // $product->decrement('stock', $item['jumlah']);

                // 3. (LEBIH PENTING UNTUK F&B) Kurangi Stok Bahan Baku (Inventories)
                $recipes = ProductRecipe::where('product_id', $item['product_id'])->get();

                foreach ($recipes as $recipe) {
                    $inventoryItem = Inventory::find($recipe->inventory_id);
                    if ($inventoryItem) {
                        $quantityToReduce = $recipe->quantity_used * $item['jumlah'];
                        $inventoryItem->decrement('stock', $quantityToReduce);
                    }
                }
            }

            // Jika semua berhasil, commit transaksi
            DB::commit();

            $this->dispatch('notify', ['message' => 'Transaksi berhasil disimpan!']);
            $this->clearCart(); // Kosongkan keranjang

            // Di sini Anda bisa menambahkan logika untuk print struk (misalnya emit event ke JS)

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }
}
