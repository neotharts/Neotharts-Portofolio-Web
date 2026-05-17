<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Service;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * Display commission page.
     */
    public function index()
    {
        // Get active services, ordered by sort_order
        $services = Service::active()
            ->ordered()
            ->get();

        // Get latest artwork image for each service
        $serviceLatestImages = [];
        $serviceLatestArtworks = [];
        foreach ($services as $service) {
            $latestArtwork = Artwork::where('is_published', true)
                ->whereRaw('LOWER(list_service) LIKE ?', ['%' . strtolower($service->name) . '%'])
                ->orderByDesc('published_at')
                ->first();
            $serviceLatestImages[$service->id] = $latestArtwork?->image;
            $serviceLatestArtworks[$service->id] = $latestArtwork;
        }

        // Get all published artworks for modal
        $artworks = Artwork::where('is_published', true)
            ->orderByDesc('published_at')
            ->get();

        // Convert artworks to array for JavaScript
        $artworksArray = $artworks->map(function ($artwork) {
            return [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'description' => $artwork->description,
                'image' => $artwork->image,
                'type' => $artwork->type,
                'form' => $artwork->form,
                'list_service' => $artwork->list_service ?? [],
                'art_for' => $artwork->art_for,
                'published_at' => $artwork->published_at?->toISOString(),
                'created_at' => $artwork->created_at?->toISOString(),
            ];
        })->toArray();

        // Convert service latest artworks to array for smart crop JS
        $serviceLatestImagesArray = [];
        foreach ($serviceLatestArtworks as $id => $artwork) {
            if ($artwork) {
                $serviceLatestImagesArray[$id] = [
                    'image' => $artwork->image,
                    'title' => $artwork->title,
                ];
            }
        }

        return view('commission', [
            'services' => $services,
            'artworks' => $artworks,
            'artworksArray' => $artworksArray,
            'serviceLatestImages' => $serviceLatestImages,
            'serviceLatestImagesArray' => $serviceLatestImagesArray,
        ]);
    }
}