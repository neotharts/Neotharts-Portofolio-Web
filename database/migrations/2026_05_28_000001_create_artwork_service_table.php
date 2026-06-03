<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['artwork_id', 'service_id']);
        });

        $services = DB::table('services')
            ->select('id', 'name')
            ->get()
            ->mapWithKeys(fn ($service) => [strtolower($service->name) => $service->id]);

        DB::table('artworks')
            ->select('id', 'list_service')
            ->whereNotNull('list_service')
            ->orderBy('id')
            ->chunk(100, function ($artworks) use ($services) {
                foreach ($artworks as $artwork) {
                    $serviceNames = json_decode($artwork->list_service, true);

                    if (!is_array($serviceNames)) {
                        continue;
                    }

                    foreach ($serviceNames as $serviceName) {
                        $serviceId = $services[strtolower((string) $serviceName)] ?? null;

                        if (!$serviceId) {
                            continue;
                        }

                        DB::table('artwork_service')->insertOrIgnore([
                            'artwork_id' => $artwork->id,
                            'service_id' => $serviceId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_service');
    }
};
