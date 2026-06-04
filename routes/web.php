<?php

use App\Livewire\Auth\Login;
use Illuminate\Http\Request;
use App\Livewire\Front\Konten;
use App\Livewire\Auth\Register;
use App\Livewire\Frontend\Shop;
use App\Livewire\Frontend\CartPage;
use App\Livewire\Frontend\MyOrders;
use App\Livewire\SearchResultsPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Frontend\CheckoutPage;
use App\Http\Controllers\StrukController;
use App\Livewire\Karyawan\ProductionList;
use App\Livewire\Karyawan\Pos\PosComponent;
use App\Livewire\Karyawan\DashboardKaryawan;
use App\Livewire\Shared\Product\ProductList;
use App\Livewire\Admin\Karyawan\KaryawanList;
use App\Http\Controllers\ValidationController;
use App\Livewire\Shared\Category\CategoryList;
use App\Livewire\Karyawan\Order\OrderManagement;
use App\Livewire\Frontend\UserProfile\EditProfile;
use App\Livewire\Karyawan\Shipping\ZoneManagement;

use App\Livewire\Shared\Inventories\InventoryList;
use App\Http\Controllers\CustomerPaymentController;
use App\Livewire\Karyawan\Pos\PosManagement;
use App\Livewire\Shared\User\Profil;
use App\Livewire\Shared\User\UpdatePassword;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Frontend\AddressCreate;
use App\Livewire\Frontend\AddressEdit;
use App\Livewire\Frontend\SetupPin;
use App\Livewire\Frontend\MyFavorites;
use App\Livewire\Frontend\UserProfile\ProfileDashboard;
use App\Livewire\Karyawan\Store\StoreManagement;
use Illuminate\Support\Facades\Artisan;

Route::get('/gas-migrate', function () {
    // clear config cache
    Artisan::call('config:clear');

    // migrate fresh + seed
    Artisan::call('migrate:fresh', [
        '--force' => true,
        '--seed' => true
    ]);

    return "Database berhasil di-fresh + seed!";
});

// ✅ Jalankan migrasi BARU saja (aman, tidak menghapus data yang sudah ada)
Route::get('/run-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return response('<pre style="font-family:monospace;background:#1a1a2e;color:#00ff88;padding:20px;border-radius:8px;">
<strong style="color:#fff;font-size:18px;">✅ Migrasi Berhasil</strong>

' . htmlspecialchars($output) . '
</pre>');
    } catch (\Exception $e) {
        return response('<pre style="font-family:monospace;background:#1a1a2e;color:#ff4444;padding:20px;border-radius:8px;">
<strong style="color:#fff;font-size:18px;">❌ Migrasi Gagal</strong>

' . htmlspecialchars($e->getMessage()) . '
</pre>', 500);
    }
});
Route::get('/gas-fresh', function () {
    // 1. Tentukan path target (sumber asli foto) dan link (tujuan di public_html)
    // SESUAIKAN "syauqieb" dengan username DirectAdmin / nama folder home kamu!
    $targetFolder = '/home/syauqieb/domains/hanascake.syauqiebill.my.id/storage/app/public';
    $linkFolder = '/home/syauqieb/domains/hanascake.syauqiebill.my.id/public_html/storage';

    // 2. Jika folder link di public_html sudah ada atau berupa broken symlink, hapus dulu
    if (file_exists($linkFolder) || is_link($linkFolder)) {
        // Jika berupa link/shortcut, gunakan unlink
        if (is_link($linkFolder)) {
            unlink($linkFolder);
        } else {
            // Jika berupa folder biasa, hapus foldernya
            File::deleteDirectory($linkFolder);
        }
    }

    // 3. Buat symlink menggunakan fungsi murni PHP
    if (symlink($targetFolder, $linkFolder)) {
        return "🔥 Symlink sukses dibuat! Gambar harusnya sudah muncul.";
    } else {
        return "❌ Gagal membuat symlink. Cek kembali path foldernya.";
    }
});

Route::get('/', Konten::class)->name('front');

Route::get('/auth/start-session', Login::class)->name('login')->middleware('guest');
Route::get('/auth/register', Register::class)->name('register')->middleware('guest');

// 1. HALAMAN NOTICE (Tampilan "Cek Email")
Route::get('/email/verify', VerifyEmail::class)->middleware('auth')->name('verification.notice');

// Route verifikasi link lama telah dihapus dan digantikan dengan verifikasi kode OTP di VerifyEmail.php



Route::prefix('admin')->middleware(['auth', 'is.admin','verified'])->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardAdmin::class)->name('dashboard');
    Route::get('/employee', KaryawanList::class)->name('list-karyawan');
    Route::get('/product', ProductList::class)->name('list-product');
    Route::get('/category', CategoryList::class)->name('list-category');
    Route::get('/profil', Profil::class)->name('profile');
    Route::get('/profil/update-password', Profil::class)->name('update.password');
});

Route::get('/search', SearchResultsPage::class)->name('search.results');

Route::prefix('karyawan')->middleware(['auth', 'is.karyawan', 'verified'])->name('karyawan.')->group(function () {
    Route::get('/dashboard', DashboardKaryawan::class)->name('dashboard');
    Route::get('/product', ProductList::class)->name('list-product');
    Route::get('/category', CategoryList::class)->name('list-category');
    Route::get('/pos', PosComponent::class)->name('pos');
    Route::get('/inventory', InventoryList::class)->name('list-inventory');
    Route::get('/validasi/{merchantOrderId}', [ValidationController::class, 'show'])->name('kasir.validasi');
    Route::get('/struk/{order}', [StrukController::class, 'print'])->name('struk.print');
    Route::get('/orders', OrderManagement::class)->name('orders.list');
    Route::get('/pos/management', PosManagement::class)->name('pos.list');
    Route::get('/production-list', ProductionList::class)->name('production-list');
    Route::get('/shipping-zones', ZoneManagement::class)->name('shipping-zones');
    Route::get('/stores', StoreManagement::class)->name('stores');

    Route::get('/profil', Profil::class)->name('profile');
    Route::get('/update-password', UpdatePassword::class)->name('update.password');

});

Route::get('/ecommerce', Shop::class)->name('ecommerce');
Route::get('/store-selection', \App\Livewire\Frontend\StoreSelection::class)->name('store-selection');
Route::get('/cart', CartPage::class)->name('cart');

Route::prefix('pelanggan')->middleware(['auth', 'is.pelanggan', 'verified'])->name('pelanggan.')->group(function () {

    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/alamat', \App\Livewire\Frontend\AddressSelection::class)->name('alamat');
    Route::get('/alamat/tambah', AddressCreate::class)->name('alamat.tambah');
    Route::get('/alamat/{id}/edit', AddressEdit::class)->name('alamat.edit');
    Route::get('/my-orders', MyOrders::class)->name('my-orders');
    Route::get('/profile', ProfileDashboard::class)->name('profile');
    Route::get('/profile/edit', EditProfile::class)->name('profile.edit');
    Route::get('/profile/pengaturan', App\Livewire\Frontend\UserProfile\Settings::class)->name('profile.settings');
    Route::get('/syarat-ketentuan', \App\Livewire\Frontend\TermsPage::class)->name('terms');
    Route::get('/kebijakan-privasi', \App\Livewire\Frontend\PrivacyPage::class)->name('privacy');
    Route::get('/orders/{order}/success', \App\Livewire\Frontend\OrderSuccessPage::class)->name('orders.success');
    Route::get('/pay/{order}', [CustomerPaymentController::class, 'show'])->name('pay');
    Route::get('/setup-pin', SetupPin::class)->name('setup-pin');
    Route::get('/favorites', MyFavorites::class)->name('favorites');
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('front'));
    })->name('logout');
});






