<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all services grouped by form to check for duplicates
        $formCounts = DB::table('services')
            ->whereNotNull('form')
            ->select('form', DB::raw('COUNT(*) as count'))
            ->groupBy('form')
            ->get();

        foreach ($formCounts as $item) {
            $services = DB::table('services')->where('form', $item->form)->get();

            if ($item->count > 1) {
                // Multiple services with same form - first one gets number, others keep form name
                $counter = 1;
                foreach ($services as $service) {
                    if ($counter === 1) {
                        // First service: add number
                        DB::table('services')
                            ->where('id', $service->id)
                            ->update(['name' => ucfirst($item->form) . ' 1']);
                    } else {
                        // Other services: just use form name
                        DB::table('services')
                            ->where('id', $service->id)
                            ->update(['name' => ucfirst($item->form)]);
                    }
                    $counter++;
                }
            } else {
                // Single service with this form - just use the form name
                DB::table('services')
                    ->where('form', $item->form)
                    ->update(['name' => ucfirst($item->form)]);
            }
        }

        // Now drop the form column
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('form')->nullable();
        });

        // Note: The original names cannot be restored without backup
    }
};