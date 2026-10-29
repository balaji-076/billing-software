<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('customer_name_snapshot');
            $table->string('customer_mobile_snapshot');
            $table->timestamp('invoice_date')->useCurrent();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('overall_discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->string('payment_method'); // CASH, UPI, CARD, etc.
            
            // Inga products/items-ah format-la store panrom
            $table->json('items'); 

            $table->string('invoice_status')->default('COMPLETED');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};