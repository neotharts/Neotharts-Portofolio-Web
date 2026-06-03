<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Repositories\ArtworkRepository;

class CommissionController extends Controller
{
    /**
     * Display commission page.
     */
    public function index(ArtworkRepository $artworkRepository)
    {
        // Get active services, ordered by sort_order
        $services = Service::active()
            ->ordered()
            ->get();

        // Get latest artwork image for each service
        $serviceLatestImages = [];
        $serviceLatestArtworks = [];
        foreach ($services as $service) {
            $latestArtwork = $artworkRepository->latestForService($service);
            $serviceLatestImages[$service->id] = $latestArtwork?->image;
            $serviceLatestArtworks[$service->id] = $latestArtwork;
        }

        // Get all published artworks for modal
        $artworks = $artworkRepository->filteredPublished();

        // Convert artworks to array for JavaScript
        $artworksArray = $artworkRepository->toPublicArray($artworks);

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
            'tosContent' => SiteSetting::getValue('tos', ''),
        ]);
    }
}
