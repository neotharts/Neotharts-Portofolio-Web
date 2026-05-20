<?php

namespace App\Http\Controllers;

use App\Models\Artwork;

class HomeController extends Controller
{
    /**
     * Tampilkan homepage dengan 3 artwork terbaru.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $artworks = Artwork::where('is_published', true)
            ->where('form', '!=', 'chibi')
            ->whereJsonDoesntContain('list_service', 'Chibi')
            ->whereJsonDoesntContain('list_service', 'chibi')
            ->ordered()
            ->take(3)
            ->get();

        return view('home', compact('artworks'));
    }
}
