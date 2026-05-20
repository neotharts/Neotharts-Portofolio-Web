<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Artwork;
use App\Models\Service;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    protected $imageService;

    /**
     * Hanya admin yang bisa akses
     */
    public function __construct(ImageService $imageService)
    {
        $this->middleware(AdminMiddleware::class);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of artworks.
     */
    public function index(Request $request)
    {
        $query = Artwork::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by form
        if ($request->filled('form')) {
            $query->byForm($request->form);
        }

        // Filter by service
        if ($request->filled('service')) {
            $query->byService($request->service);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Order dan pagination
        $artworks = $query->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(10);

        // Get filter options
        // Get unique types from artworks (not from services)
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types);

        $forms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        // Available services for filtering - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        // Get services grouped by type for dynamic form loading
        $servicesByType = Service::active()->get()->groupBy('type');

        return view('admin.artworks.index', compact('artworks', 'types', 'forms', 'servicesByType', 'availableServices'));
    }

    /**
     * Show the form for creating a new artwork.
     */
    public function create()
    {
        // Get unique types from artworks
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types);

        // Get services grouped by type for dynamic form dropdown
        $servicesByType = Service::active()->get()->groupBy('type');

        // Available services for list_service checkbox - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        // Get forms (service names) grouped by type
        $formsByType = Service::active()->get()->groupBy('type')->map(function ($services) {
            return $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->starting_price,
                ];
            });
        });

        return view('admin.artworks.create', compact('types', 'servicesByType', 'formsByType', 'availableServices'));
    }

    /**
     * Store a newly created artwork.
     */
    public function store(Request $request)
    {
        // Available services for validation - synced with services table (lowercase for comparison)
        $dbServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
        $availableServices = !empty($dbServices) ? $dbServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        // Normalize to lowercase for case-insensitive matching
        $availableServicesLower = array_map('strtolower', $availableServices);

        // Valid types for artwork - get from Artwork table
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $validTypes = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($validTypes);

        // Build validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'required|array|min:1|max:12',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'image_order' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $validTypes),
            'form' => 'nullable|string|max:255',
            'list_service' => 'nullable|array',
            'art_for' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];

        // Custom error messages
        $messages = [
            'title.required' => 'Judul artwork wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'images.required' => 'Minimal satu gambar wajib diupload',
            'images.array' => 'Upload gambar tidak valid',
            'images.min' => 'Minimal satu gambar wajib diupload',
            'images.max' => 'Maksimal 12 gambar per artwork',
            'images.*.image' => 'Semua file harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP',
            'type.required' => 'Tipe artwork wajib dipilih',
            'type.in' => 'Tipe artwork tidak valid',
        ];

        // Validasi
        $validated = $request->validate($rules, $messages);

        // Handle is_published - checkbox unchecked means false
        $validated['is_published'] = $request->has('is_published');

        // Handle list_service - normalize to lowercase and filter valid services
        $validatedListService = [];
        if (!empty($validated['list_service'])) {
            foreach ($validated['list_service'] as $service) {
                $serviceLower = strtolower(trim($service));
                if (in_array($serviceLower, $availableServicesLower)) {
                    // Use the original case from database
                    $index = array_search($serviceLower, $availableServicesLower);
                    $validatedListService[] = $dbServices[$index];
                }
            }
        }
        $validated['list_service'] = $validatedListService;

        if (empty($validated['form'])) {
            $validated['form'] = $this->resolveLegacyForm($validatedListService);
        }

        // Default art_for ke 'myself' jika kosong
        if (empty($validated['art_for'])) {
            $validated['art_for'] = 'myself';
        }
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        try {
            \Log::info('Artwork store - request received', [
                'title' => $validated['title'],
                'type' => $validated['type'],
                'list_service' => $validated['list_service'],
                'is_published' => $validated['is_published'],
            ]);

            $imagePaths = $this->storeArtworkImages($request->file('images', []));

            // Handle image order if provided
            if ($request->filled('image_order')) {
                $requestedOrder = json_decode($request->image_order, true);
                if (is_array($requestedOrder) && count($requestedOrder) === count($imagePaths)) {
                    // Reorder images based on requested order (filename-based)
                    $orderedPaths = [];
                    foreach ($requestedOrder as $requestedName) {
                        foreach ($imagePaths as $path) {
                            if (basename($path) === $requestedName) {
                                $orderedPaths[] = $path;
                                break;
                            }
                        }
                    }
                    // If order doesn't match, just use the original order
                    if (count($orderedPaths) === count($imagePaths)) {
                        $imagePaths = $orderedPaths;
                    }
                }
            }

            $validated['image'] = $imagePaths[0];
            $validated['images'] = $imagePaths;

            // Set user_id dan published_at
            $validated['user_id'] = auth()->id();
            if ($validated['is_published']) {
                $validated['published_at'] = now();
            }

            // Create artwork
            $artwork = Artwork::create($validated);

            \Log::info('Artwork created', [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'image' => $artwork->image,
                'list_service' => $artwork->list_service,
            ]);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil ditambahkan!');

        } catch (\Exception $e) {
            \Log::error('Artwork store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified artwork.
     */
    public function show(Artwork $artwork)
    {
        // Hanya admin atau pemilik yang bisa lihat
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $galleryImages = $artwork->gallery_images;

        return view('admin.artworks.show', compact('artwork', 'galleryImages'));
    }

    /**
     * Show the form for editing the specified artwork.
     */
    public function edit(Artwork $artwork)
    {
        // Hanya admin yang bisa edit
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        // Get unique types from artworks
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types);

        // Get forms (service names) for the artwork's type
        $forms = Service::active()->where('type', $artwork->type)->pluck('name')->toArray();
        $forms = !empty($forms) ? $forms : ['chibi', 'headshot', 'halfbody', 'fullbody'];

        // Available services for list_service checkbox - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        // Get services grouped by type for dynamic form loading
        $servicesByType = Service::active()->get()->groupBy('type');

        return view('admin.artworks.edit', compact('artwork', 'types', 'forms', 'servicesByType', 'availableServices'));
    }

    /**
     * Update the specified artwork.
     */
    public function update(Request $request, Artwork $artwork)
    {
        // Hanya admin yang bisa update
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        // Available services for validation - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        // Valid types for artwork - get from Artwork table
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $validTypes = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($validTypes);

        // Validasi
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array|max:12',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'add_images' => 'nullable|array|max:12',
            'add_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'image_order' => 'nullable|string',
            'images_to_delete' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $validTypes),
            'form' => 'nullable|string',
            'list_service' => 'nullable|array',
            'list_service.*' => 'in:' . implode(',', $availableServices),
            'art_for' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image order and deletion
        $imageOrder = $request->filled('image_order') ? json_decode($request->image_order, true) : null;
        $imagesToDelete = $request->filled('images_to_delete') ? json_decode($request->images_to_delete, true) : [];
        $imagesToDelete = is_array($imagesToDelete) ? $imagesToDelete : [];

        // Default art_for ke 'myself' jika kosong
        if (empty($validated['art_for'])) {
            $validated['art_for'] = 'myself';
        }
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        try {
            // Get current artwork images
            $currentImages = $artwork->gallery_images ?? [];
            $newImagesArray = $currentImages;

            // Log untuk debugging
            \Log::info('=== Artwork Update Start ===');
            \Log::info('Request has add_images:', ['has' => $request->hasFile('add_images')]);
            \Log::info('Request has images:', ['has' => $request->hasFile('images')]);
            \Log::info('Current images:', ['count' => count($currentImages), 'images' => $currentImages]);

            // 1. Delete images marked for deletion
            if (!empty($imagesToDelete)) {
                \Log::info('Deleting images:', ['to_delete' => $imagesToDelete]);
                foreach ($imagesToDelete as $imageToDelete) {
                    if (Storage::disk('public')->exists($imageToDelete)) {
                        Storage::disk('public')->delete($imageToDelete);
                        \Log::info('Deleted file:', ['path' => $imageToDelete]);
                    }
                    $newImagesArray = array_filter($newImagesArray, fn($img) => $img !== $imageToDelete);
                }
                $newImagesArray = array_values($newImagesArray);
            }

            // 2. Handle "Replace All" - if images[] has files, delete all and replace
            if ($request->hasFile('images')) {
                \Log::info('Replacing all images');

                // Delete existing images
                foreach ($newImagesArray as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }

                // Upload new images
                $imagePaths = $this->storeArtworkImages($request->file('images', []));
                \Log::info('New image paths:', ['paths' => $imagePaths]);

                $validated['image'] = $imagePaths[0];
                $validated['images'] = $imagePaths;
            }
            // 3. Handle "Add Images" - append new images to existing
            elseif ($request->hasFile('add_images')) {
                \Log::info('Adding new images');

                // Check if we have cropped existing images
                $croppedOriginals = $request->filled('cropped_originals')
                    ? json_decode($request->cropped_originals, true) ?? []
                    : [];
                $numCropped = count($croppedOriginals);

                // Upload new images (this includes cropped versions of existing images)
                $addedImages = $this->storeArtworkImages($request->file('add_images', []));
                \Log::info('Added image paths:', ['paths' => $addedImages, 'count' => count($addedImages), 'cropped_count' => $numCropped]);

                // If we have cropped existing images, the first N files are cropped versions
                // We need to replace the original paths with the new cropped paths
                if ($numCropped > 0 && $numCropped <= count($addedImages)) {
                    // Replace original paths with cropped versions
                    foreach ($croppedOriginals as $index => $originalPath) {
                        if (isset($addedImages[$index])) {
                            // Find and replace the original path in the array
                            $pos = array_search($originalPath, $newImagesArray);
                            if ($pos !== false) {
                                $newImagesArray[$pos] = $addedImages[$index];
                            } else {
                                // Original already deleted, insert at the end of existing images
                                $newImagesArray[] = $addedImages[$index];
                            }
                            // Remove from addedImages to avoid duplication
                            unset($addedImages[$index]);
                        }
                    }
                    $addedImages = array_values($addedImages);
                }

                // Merge remaining new images with existing (after deletion and crop replacement)
                $finalImages = array_merge($newImagesArray, $addedImages);
                \Log::info('Merged images:', ['count' => count($finalImages), 'images' => $finalImages]);

                $validated['images'] = array_values($finalImages);
                $validated['image'] = $validated['images'][0] ?? null;
            }
            // 4. Just reorder/delete existing images (no new images uploaded)
            elseif ($imageOrder && is_array($imageOrder)) {
                \Log::info('Reordering existing images');

                // Get ordered existing images (excluding deleted)
                $orderedImages = [];
                foreach ($imageOrder as $orderItem) {
                    if (isset($orderItem['type']) && $orderItem['type'] === 'existing' && isset($orderItem['path'])) {
                        // Check if this existing image wasn't deleted
                        if (!in_array($orderItem['path'], $imagesToDelete)) {
                            $orderedImages[] = $orderItem['path'];
                        }
                    }
                }

                \Log::info('Reordered images:', ['count' => count($orderedImages), 'images' => $orderedImages]);

                // Only update if we have all images accounted for
                if (count($orderedImages) > 0) {
                    $validated['images'] = array_values($orderedImages);
                    $validated['image'] = $validated['images'][0] ?? null;
                } else {
                    // No changes to images, keep existing
                    unset($validated['images']);
                    unset($validated['image']);
                }
            }
            // 5. No image changes at all - just keep existing images
            else {
                \Log::info('No image changes, keeping existing');
                unset($validated['images']);
                unset($validated['image']);
            }

            \Log::info('Final images to save:', ['images' => $validated['images'] ?? 'unchanged']);

            $validated['is_published'] = $request->has('is_published');
            $validated['list_service'] = $validated['list_service'] ?? [];

            if (empty($validated['form'])) {
                $validated['form'] = $this->resolveLegacyForm($validated['list_service']);
            }

            // Update published_at jika status berubah
            if ($validated['is_published'] && !$artwork->is_published) {
                $validated['published_at'] = now();
            } elseif (!$validated['is_published']) {
                $validated['published_at'] = null;
            }

            // Update artwork
            $artwork->update($validated);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified artwork.
     */
    public function destroy(Artwork $artwork)
    {
        // Hanya admin yang bisa hapus
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        try {
            $this->deleteArtworkImages($artwork);

            // Hapus artwork
            $artwork->delete();

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Keep the old non-null form column populated while the dashboard uses services.
     */
    private function resolveLegacyForm(array $services): string
    {
        $knownForms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        foreach ($services as $service) {
            $normalized = strtolower(trim($service));

            foreach ($knownForms as $form) {
                if (str_contains($normalized, $form)) {
                    return $form;
                }
            }
        }

        return 'chibi';
    }

    /**
     * Compress and store all uploaded artwork images.
     */
    private function storeArtworkImages(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $result = $this->imageService->compress($file, [
                'max_width' => 1920,
                'max_height' => 1920,
                'quality' => 85,
                'format' => 'webp',
                'suffix' => '',
            ]);

            $paths[] = $result['path'];

            \Log::info('Image compressed', [
                'path' => $result['path'],
                'size' => $result['compressed_size'],
            ]);
        }

        return $paths;
    }

    /**
     * Delete all files used by an artwork gallery.
     */
    private function deleteArtworkImages(Artwork $artwork): void
    {
        foreach (array_unique($artwork->gallery_images) as $image) {
            if ($image && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }
    }
}
