<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    use HasFactory;

    protected $table = 'product_recipes';

    protected $fillable = [
        'product_id',
        'inventory_id',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Produk yang memiliki resep ini
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Bahan baku (inventori) yang digunakan
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    // Hitung total kebutuhan bahan jika produksi beberapa unit produk
    public function totalUsageForProduction(float $qtyProduct): float
    {
        return $this->quantity_used * $qtyProduct;
    }
}
