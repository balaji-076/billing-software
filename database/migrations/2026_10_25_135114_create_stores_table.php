<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_code')->unique(); // VIGNESH, KIRUBA, VP
            $table->string('store_name');
            $table->string('gstin')->nullable();
            $table->json('phone_numbers')->nullable();
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stores'); }
};