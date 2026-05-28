<?php

namespace App\Http\Controllers;

class ThreeDController extends Controller
{
    /**
     * Tampilkan halaman 3D character.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('three_d');
    }
}