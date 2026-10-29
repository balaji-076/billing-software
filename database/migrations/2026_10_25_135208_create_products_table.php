<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('product_name');
            $table->string('sku');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_price', 10, 2);
            $table->string('unit')->default('pcs');
            $table->string('image_url')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->decimal('informational_qty', 10, 2)->default(0);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();

            // Oru store-kulla SKU unique-ah irukka
            $table->unique(['store_id', 'sku']);
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};