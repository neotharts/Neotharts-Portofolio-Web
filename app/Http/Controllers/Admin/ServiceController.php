<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Service;
use App\Repositories\ArtworkRepository;
use App\Services\ServiceService;

class ServiceController extends Controller
{
    protected $serviceService;

    /**
     * Hanya admin yang bisa akses
     */
    public function __construct(ServiceService $serviceService)
    {
        $this->middleware(AdminMiddleware::class);
        $this->serviceService = $serviceService;
    }

    /**
     * Display a listing of services.
     */
    public function index(ArtworkRepository $artworkRepository)
    {
        $services = Service::ordered()->get();

        // Get latest artwork for each service
        $latestArtworks = [];
        foreach ($services as $service) {
            $latestArtwork = $artworkRepository->latestForService($service);

            $latestArtworks[$service->id] = $latestArtwork;
        }

        return view('admin.services.index', compact('services', 'latestArtworks'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(ServiceRequest $request)
    {
        $this->serviceService->create($request->validated(), $request->file('image'));

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil ditambahkan!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service, ArtworkRepository $artworkRepository)
    {
        $latestArtwork = $artworkRepository->latestForService($service);

        return view('admin.services.show', compact('service', 'latestArtwork'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $this->serviceService->update(
            $service,
            $request->validated(),
            $request->file('image'),
            $request->boolean('is_active')
        );

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil diperbarui!');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        $this->serviceService->delete($service);

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil dihapus!');
    }
}
