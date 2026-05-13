<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
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

        $artworks = $query->orderByDesc('published_at')->get();

        // Get unique types from database for filter
        $types = Artwork::where('is_published', true)
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values()
            ->toArray();

        // Default types if no artworks exist
        $defaultTypes = ['personal', 'fanart', 'commission', 'organisation'];
        $types = !empty($types) ? $types : $defaultTypes;

        return view('artwork_list', compact('artworks', 'types'));
    }
}