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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            // Tipe artwork: Komisi, Personal, Organisasi, Fanart
            $table->enum('type', ['komisi', 'personal', 'organisasi', 'fanart']);
            // Form artwork: Chibi, Headshot, Halfbody, Fullbody
            $table->enum('form', ['chibi', 'headshot', 'halfbody', 'fullbody']);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Index untuk query yang sering digunakan
            $table->index('user_id');
            $table->index('type');
            $table->index('form');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
