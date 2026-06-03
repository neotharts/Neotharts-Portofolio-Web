<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    /**
     * Create a new service.
     *
     * @param array $data
     * @param UploadedFile|null $imageFile
     * @return Service
     */
    public function create(array $data, ?UploadedFile $imageFile = null): Service
    {
        return DB::transaction(function () use ($data, $imageFile) {
            // Parse features
            if (!empty($data['features'])) {
                $features = array_filter(array_map('trim', explode("\n", $data['features'])));
                $data['features'] = json_encode(array_values($features));
            } else {
                $data['features'] = null;
            }

            // Handle image upload
            if ($imageFile) {
                $filename = time() . '_' . strtolower(str_replace(' ', '_', $data['name'])) . '.' . $imageFile->getClientOriginalExtension();
                $path = $imageFile->storeAs('services', $filename, 'public');
                $data['image'] = $path;
            }

            $data['type'] = 'komisi';
            $data['is_active'] = isset($data['is_active']) && $data['is_active'];
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $service = Service::create($data);
            Log::info('Service created in ServiceService', ['id' => $service->id]);

            return $service;
        });
    }

    /**
     * Update an existing service.
     *
     * @param Service $service
     * @param array $data
     * @param UploadedFile|null $imageFile
     * @param bool $isActive
     * @return Service
     */
    public function update(Service $service, array $data, ?UploadedFile $imageFile = null, bool $isActive = false): Service
    {
        return DB::transaction(function () use ($service, $data, $imageFile, $isActive) {
            // Parse features
            if (!empty($data['features'])) {
                $features = array_filter(array_map('trim', explode("\n", $data['features'])));
                $data['features'] = json_encode(array_values($features));
            } else {
                $data['features'] = null;
            }

            // Handle image upload
            if ($imageFile) {
                // Delete old image
                if ($service->image && Storage::disk('public')->exists($service->image)) {
                    Storage::disk('public')->delete($service->image);
                }
                
                $filename = time() . '_' . strtolower(str_replace(' ', '_', $data['name'])) . '.' . $imageFile->getClientOriginalExtension();
                $path = $imageFile->storeAs('services', $filename, 'public');
                $data['image'] = $path;
            }

            $data['is_active'] = $isActive;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $service->update($data);
            Log::info('Service updated in ServiceService', ['id' => $service->id]);

            return $service;
        });
    }

    /**
     * Delete a service and its image.
     *
     * @param Service $service
     * @return void
     */
    public function delete(Service $service): void
    {
        DB::transaction(function () use ($service) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $service->delete();
            Log::info('Service deleted in ServiceService', ['id' => $service->id]);
        });
    }
}
