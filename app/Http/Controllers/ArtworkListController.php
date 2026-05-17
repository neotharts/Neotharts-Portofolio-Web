<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Service;
use Illuminate\Http\Request;

class ArtworkListController extends Controller
{
    /**
     * Display artwork list page.
     */
    public function index(Request $request)
    {
        $query = Artwork::where('is_published', true);

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by service if provided
        if ($request->filled('service')) {
            $query->whereJsonContains('list_service', $request->service);
        }

        $artworks = $query->orderByDesc('published_at')->get();

        // Get unique types from database for filter
        $dbTypes = Artwork::where('is_published', true)
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values()
            ->toArray();

        // Default types - always show all types
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];

        // Merge database types with default types (show all types that exist or default)
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types); // Sort alphabetically

        // Available services - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        // Fallback if no services
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

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

        return view('artwork_list', [
            'artworks' => $artworks,
            'artworksArray' => $artworksArray,
            'types' => $types,
            'availableServices' => $availableServices,
        ]);
    }
}