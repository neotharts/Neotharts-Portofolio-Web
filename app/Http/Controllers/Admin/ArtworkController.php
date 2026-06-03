<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtworkRequest;
use App\Http\Requests\UpdateArtworkRequest;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Artwork;
use App\Models\Service;
use App\Repositories\ArtworkRepository;
use App\Services\ArtworkService;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    protected $artworkService;
    protected $artworkRepository;

    /**
     * Hanya admin yang bisa akses
     */
    public function __construct(ArtworkService $artworkService, ArtworkRepository $artworkRepository)
    {
        $this->middleware(AdminMiddleware::class);
        $this->artworkService = $artworkService;
        $this->artworkRepository = $artworkRepository;
    }

    /**
     * Display a listing of artworks.
     */
    public function index(Request $request)
    {
        $query = Artwork::with(['user', 'services']);

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
            $query->where(function ($serviceQuery) use ($request) {
                $serviceQuery->whereHas('services', fn ($query) => $query->where('name', $request->service))
                    ->orWhereJsonContains('list_service', $request->service);
            });
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
    public function store(StoreArtworkRequest $request)
    {
        try {
            $this->artworkService->create(
                $request->validated(),
                $request->file('images'),
                $request->image_order
            );

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
    public function update(UpdateArtworkRequest $request, Artwork $artwork)
    {
        // Hanya admin yang bisa update
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        try {
            $this->artworkService->update($artwork, $request->validated(), $request->all());

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
            $this->artworkService->delete($artwork);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
