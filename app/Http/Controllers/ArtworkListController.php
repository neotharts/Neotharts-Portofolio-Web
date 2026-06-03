<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Repositories\ArtworkRepository;

class ArtworkListController extends Controller
{
    /**
     * Display artwork list page.
     */
    public function index(Request $request, ArtworkRepository $artworkRepository)
    {
        $artworks = $artworkRepository->filteredPublished(
            $request->string('type')->toString() ?: null,
            $request->string('service')->toString() ?: null,
        );
        $types = $artworkRepository->publishedTypes();

        // Available services - synced with services table
        $availableServices = Service::active()
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        // Fallback if no services
        $availableServices = !empty($availableServices) ? $availableServices : ['headshot', 'halfbody', 'fullbody', 'chibi'];

        $artworksArray = $artworkRepository->toPublicArray($artworks);

        return view('artwork_list', [
            'artworks' => $artworks,
            'artworksArray' => $artworksArray,
            'types' => $types,
            'availableServices' => $availableServices,
        ]);
    }
}
