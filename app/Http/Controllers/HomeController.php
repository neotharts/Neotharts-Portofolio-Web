<?php

namespace App\Http\Controllers;

use App\Repositories\ArtworkRepository;

class HomeController extends Controller
{
    /**
     * Tampilkan homepage dengan 3 artwork terbaru.
     *
     * @return \Illuminate\View\View
     */
    public function index(ArtworkRepository $artworkRepository)
    {
        $artworks = $artworkRepository->homepageLatest();

        return view('home', compact('artworks'));
    }
}
