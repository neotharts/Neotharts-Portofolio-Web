<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Hanya admin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    /**
     * Display a listing of services.
     */
    public function index()
    {
        $services = Service::ordered()->get();

        // Get latest artwork for each service
        $latestArtworks = [];
        foreach ($services as $service) {
            // Use LIKE for case-insensitive search (SQLite compatible)
            $latestArtwork = \App\Models\Artwork::where('is_published', true)
                ->whereRaw('LOWER(list_service) LIKE ?', ['%' . strtolower($service->name) . '%'])
                ->orderByDesc('published_at')
                ->first();

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starting_price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Parse features from textarea (one per line)
        if (!empty($validated['features'])) {
            $features = array_filter(array_map('trim', explode("\n", $validated['features'])));
            $validated['features'] = json_encode($features);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . strtolower(str_replace(' ', '_', $validated['name'])) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $validated['image'] = $path;
        }

        // Set type to komisi by default (or remove it)
        $validated['type'] = 'komisi';

        Service::create($validated);

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil ditambahkan!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // Get latest artwork using this service (case-insensitive)
        $latestArtwork = \App\Models\Artwork::where('is_published', true)
            ->whereRaw('LOWER(list_service) LIKE ?', ['%' . strtolower($service->name) . '%'])
            ->orderByDesc('published_at')
            ->first();

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
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starting_price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Parse features from textarea (one per line)
        if (!empty($validated['features'])) {
            $features = array_filter(array_map('trim', explode("\n", $validated['features'])));
            $validated['features'] = json_encode($features);
        } else {
            $validated['features'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($service->image && \Storage::disk('public')->exists($service->image)) {
                \Storage::disk('public')->delete($service->image);
            }
            $file = $request->file('image');
            $filename = time() . '_' . strtolower(str_replace(' ', '_', $validated['name'])) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $validated['image'] = $path;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil diperbarui!');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        // Delete image
        if ($service->image && \Storage::disk('public')->exists($service->image)) {
            \Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
                        ->with('success', 'Service berhasil dihapus!');
    }
}