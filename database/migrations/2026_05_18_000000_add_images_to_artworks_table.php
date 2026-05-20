<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });

        DB::table('artworks')
            ->whereNotNull('image')
            ->orderBy('id')
            ->get(['id', 'image'])
            ->each(function ($artwork) {
                DB::table('artworks')
                    ->where('id', $artwork->id)
                    ->update(['images' => json_encode([$artwork->image])]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
