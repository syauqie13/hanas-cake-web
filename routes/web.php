<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Admin\Karyawan\KaryawanList;
use App\Livewire\Shared\Category\CategoryList;
use App\Livewire\Shared\Product\ProductList;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/start-session', Login::class)->name('login');
Route::get('/auth/register', Register::class)->name('register');

Route::prefix('admin')->middleware(['auth', 'is.admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardAdmin::class)->name('dashboard');
    Route::get('/employee', KaryawanList::class)->name('list-karyawan');
    Route::get('/product', ProductList::class)->name('list-product');
    Route::get('/category', CategoryList::class)->name('list-category');
});
