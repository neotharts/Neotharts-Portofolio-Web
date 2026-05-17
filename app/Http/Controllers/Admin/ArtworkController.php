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
        $artworks = $query->orderByDesc('created_at')->paginate(10);

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'type' => 'required|in:' . implode(',', $validTypes),
            'form' => 'nullable|string',
            'list_service' => 'nullable|array',
            'list_service.*' => 'in:' . implode(',', $availableServices),
            'art_for' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        // Default art_for ke 'myself' jika kosong
        if (empty($validated['art_for'])) {
            $validated['art_for'] = 'myself';
        }

        try {
            // Compress and upload image
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Compress image using ImageService
                $result = $this->imageService->compress($file, [
                    'max_width' => 1920,
                    'max_height' => 1920,
                    'quality' => 85,
                    'format' => 'webp',
                    'suffix' => '',
                ]);

                $validated['image'] = $result['path'];

                // Log compression info
                \Log::info('Image compressed', [
                    'original_size' => $result['original_size'],
                    'compressed_size' => $result['compressed_size'],
                    'saved' => $result['saved_percentage'] . '%',
                    'dimensions' => $result['dimensions'],
                ]);
            }

            // Set user_id dan published_at
            $validated['user_id'] = auth()->id();
            if ($validated['is_published']) {
                $validated['published_at'] = now();
            }

            // Create artwork
            Artwork::create($validated);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil ditambahkan!');
        } catch (\Exception $e) {
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

        return view('admin.artworks.show', compact('artwork'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'type' => 'required|in:' . implode(',', $validTypes),
            'form' => 'nullable|string',
            'list_service' => 'nullable|array',
            'list_service.*' => 'in:' . implode(',', $availableServices),
            'art_for' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        // Default art_for ke 'myself' jika kosong
        if (empty($validated['art_for'])) {
            $validated['art_for'] = 'myself';
        }

        try {
            // Upload image baru jika ada
            if ($request->hasFile('image')) {
                // Hapus image lama
                if ($artwork->image && Storage::disk('public')->exists($artwork->image)) {
                    Storage::disk('public')->delete($artwork->image);
                }

                $file = $request->file('image');

                // Compress image using ImageService
                $result = $this->imageService->compress($file, [
                    'max_width' => 1920,
                    'max_height' => 1920,
                    'quality' => 85,
                    'format' => 'webp',
                    'suffix' => '',
                ]);

                $validated['image'] = $result['path'];
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
            // Hapus image
            if ($artwork->image && Storage::disk('public')->exists($artwork->image)) {
                Storage::disk('public')->delete($artwork->image);
            }

            // Hapus artwork
            $artwork->delete();

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
