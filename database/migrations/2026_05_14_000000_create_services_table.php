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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama service: Headshot, Halfbody, etc
            $table->text('description')->nullable();
            $table->integer('starting_price'); // Harga awal
            $table->string('type')->default('komisi'); // Type: komisi, personal, organisasi, fanart
            $table->string('form')->nullable(); // Form: chibi, headshot, halfbody, fullbody
            $table->string('image')->nullable(); // Gambar contoh
            $table->text('features')->nullable(); // JSON array of features
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};