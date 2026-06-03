<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Service;
use App\Repositories\ArtworkRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArtworkService
{
    protected $imageService;
    protected $artworkRepository;

    public function __construct(ImageService $imageService, ArtworkRepository $artworkRepository)
    {
        $this->imageService = $imageService;
        $this->artworkRepository = $artworkRepository;
    }

    /**
     * Create a new artwork.
     *
     * @param array $data
     * @param array $files
     * @param string|null $imageOrder
     * @return Artwork
     */
    public function create(array $data, array $files, ?string $imageOrder = null): Artwork
    {
        return DB::transaction(function () use ($data, $files, $imageOrder) {
            $data['is_published'] = isset($data['is_published']) && $data['is_published'];
            
            // Sync with DB active services
            $dbServices = Service::active()->orderBy('sort_order')->pluck('name')->toArray();
            $availableServices = !empty($dbServices) ? $dbServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];
            $availableServicesLower = array_map('strtolower', $availableServices);

            $validatedListService = [];
            if (!empty($data['list_service'])) {
                foreach ($data['list_service'] as $service) {
                    $serviceLower = strtolower(trim((string)$service));
                    if (in_array($serviceLower, $availableServicesLower)) {
                        $index = array_search($serviceLower, $availableServicesLower);
                        $validatedListService[] = $dbServices[$index];
                    }
                }
            }
            $data['list_service'] = $validatedListService;

            if (empty($data['form'])) {
                $data['form'] = $this->resolveLegacyForm($validatedListService);
            }

            if (empty($data['art_for'])) {
                $data['art_for'] = 'myself';
            }
            $data['sort_order'] = $data['sort_order'] ?? 0;

            Log::info('Artwork creation in service', [
                'title' => $data['title'],
                'type' => $data['type'],
                'list_service' => $data['list_service'],
            ]);

            // Handle image compression and uploads
            $imagePaths = $this->storeArtworkImages($files);

            // Reorder images if requested
            if ($imageOrder && count($imagePaths) > 0) {
                $requestedOrder = json_decode($imageOrder, true);
                if (is_array($requestedOrder) && count($requestedOrder) === count($imagePaths)) {
                    $orderedPaths = [];
                    foreach ($requestedOrder as $requestedName) {
                        foreach ($imagePaths as $path) {
                            if (basename($path) === $requestedName) {
                                $orderedPaths[] = $path;
                                break;
                            }
                        }
                    }
                    if (count($orderedPaths) === count($imagePaths)) {
                        $imagePaths = $orderedPaths;
                    }
                }
            }

            $data['image'] = $imagePaths[0] ?? null;
            $data['images'] = $imagePaths;
            $data['user_id'] = auth()->user()->getKey();
            
            if ($data['is_published']) {
                $data['published_at'] = now();
            }

            $artwork = Artwork::create($data);
            $this->artworkRepository->syncServices($artwork, $validatedListService);

            Log::info('Artwork created successfully in service', ['id' => $artwork->id]);

            return $artwork;
        });
    }

    /**
     * Update an existing artwork.
     *
     * @param Artwork $artwork
     * @param array $data
     * @param array $requestData
     * @return Artwork
     */
    public function update(Artwork $artwork, array $data, array $requestData): Artwork
    {
        return DB::transaction(function () use ($artwork, $data, $requestData) {
            $imageOrder = $requestData['image_order'] ?? null;
            $imageOrder = $imageOrder ? json_decode($imageOrder, true) : null;
            
            $imagesToDelete = $requestData['images_to_delete'] ?? null;
            $imagesToDelete = $imagesToDelete ? json_decode($imagesToDelete, true) : [];
            $imagesToDelete = is_array($imagesToDelete) ? $imagesToDelete : [];

            if (empty($data['art_for'])) {
                $data['art_for'] = 'myself';
            }
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $currentImages = $artwork->gallery_images ?? [];
            $newImagesArray = $currentImages;

            Log::info('=== Artwork Service Update Start ===', ['id' => $artwork->id]);

            // 1. Delete marked images
            if (!empty($imagesToDelete)) {
                foreach ($imagesToDelete as $imageToDelete) {
                    if (Storage::disk('public')->exists($imageToDelete)) {
                        Storage::disk('public')->delete($imageToDelete);
                    }
                    $newImagesArray = array_filter($newImagesArray, fn($img) => $img !== $imageToDelete);
                }
                $newImagesArray = array_values($newImagesArray);
            }

            // 2. Handle replacing all images
            if (isset($requestData['images']) && is_array($requestData['images'])) {
                foreach ($newImagesArray as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }

                $imagePaths = $this->storeArtworkImages($requestData['images']);
                $data['image'] = $imagePaths[0] ?? null;
                $data['images'] = $imagePaths;
            }
            // 3. Handle appending/adding images
            elseif (isset($requestData['add_images']) && is_array($requestData['add_images'])) {
                $croppedOriginals = isset($requestData['cropped_originals'])
                    ? json_decode($requestData['cropped_originals'], true) ?? []
                    : [];
                $numCropped = count($croppedOriginals);

                $addedImages = $this->storeArtworkImages($requestData['add_images']);

                if ($numCropped > 0 && $numCropped <= count($addedImages)) {
                    foreach ($croppedOriginals as $index => $originalPath) {
                        if (isset($addedImages[$index])) {
                            $pos = array_search($originalPath, $newImagesArray);
                            if ($pos !== false) {
                                $newImagesArray[$pos] = $addedImages[$index];
                            } else {
                                $newImagesArray[] = $addedImages[$index];
                            }
                            unset($addedImages[$index]);
                        }
                    }
                    $addedImages = array_values($addedImages);
                }

                $finalImages = array_merge($newImagesArray, $addedImages);
                $data['images'] = array_values($finalImages);
                $data['image'] = $data['images'][0] ?? null;
            }
            // 4. Handle reordering of existing images
            elseif ($imageOrder && is_array($imageOrder)) {
                $orderedImages = [];
                foreach ($imageOrder as $orderItem) {
                    if (isset($orderItem['type']) && $orderItem['type'] === 'existing' && isset($orderItem['path'])) {
                        if (!in_array($orderItem['path'], $imagesToDelete)) {
                            $orderedImages[] = $orderItem['path'];
                        }
                    }
                }

                if (count($orderedImages) > 0) {
                    $data['images'] = array_values($orderedImages);
                    $data['image'] = $data['images'][0] ?? null;
                } else {
                    unset($data['images']);
                    unset($data['image']);
                }
            }
            // 5. Keep existing
            else {
                unset($data['images']);
                unset($data['image']);
            }

            $data['is_published'] = isset($requestData['is_published']);
            $data['list_service'] = $data['list_service'] ?? [];

            if (empty($data['form'])) {
                $data['form'] = $this->resolveLegacyForm($data['list_service']);
            }

            // Sync publish dates
            if ($data['is_published'] && !$artwork->is_published) {
                $data['published_at'] = now();
            } elseif (!$data['is_published']) {
                $data['published_at'] = null;
            }

            $artwork->update($data);
            $this->artworkRepository->syncServices($artwork, $data['list_service']);

            Log::info('Artwork updated successfully in service', ['id' => $artwork->id]);

            return $artwork;
        });
    }

    /**
     * Delete an artwork and its assets.
     *
     * @param Artwork $artwork
     * @return void
     */
    public function delete(Artwork $artwork): void
    {
        DB::transaction(function () use ($artwork) {
            foreach (array_unique($artwork->gallery_images) as $image) {
                if ($image && Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
            $artwork->delete();
            Log::info('Artwork and its images deleted', ['id' => $artwork->id]);
        });
    }

    /**
     * Resolve legacy form column based on list of services.
     */
    private function resolveLegacyForm(array $services): string
    {
        $knownForms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        foreach ($services as $service) {
            $normalized = strtolower(trim((string)$service));

            foreach ($knownForms as $form) {
                if (str_contains($normalized, $form)) {
                    return $form;
                }
            }
        }

        return 'chibi';
    }

    /**
     * Store and compress files.
     */
    private function storeArtworkImages(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->imageService->compress($file, [
                    'max_width' => 1920,
                    'max_height' => 1920,
                    'quality' => 85,
                    'format' => 'jpeg',
                    'suffix' => '',
                ]);

                $paths[] = $result['path'];
            }
        }

        return $paths;
    }
}
