<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->onDelete('set null');
            $table->foreignId('cashier_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->dateTime('tanggal');
            $table->decimal('total', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->string('payment_method', 50)->nullable();
            $table->enum('payment_status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->enum('order_type', ['online', 'pos'])->default('pos');
            $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
