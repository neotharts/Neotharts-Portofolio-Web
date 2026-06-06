<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_instagram')->nullable();
            $table->enum('payment_method', ['qris', 'paypal'])->default('qris');
            $table->enum('status', ['unpaid', 'sketch', 'progress', 'finishing', 'done'])->default('unpaid');
            $table->integer('total_amount')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('service_name');
            $table->integer('unit_price');
            $table->integer('quantity')->default(1);
            $table->integer('additional_characters')->default(0);
            $table->enum('usage_type', ['personal', 'commercial'])->default('personal');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};