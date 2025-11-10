<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();

            // Un usuario no puede tener el mismo producto duplicado en el carrito
            $table->unique(['user_id', 'product_id']);
            
            // Índices para mejor rendimiento
            $table->index('user_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};