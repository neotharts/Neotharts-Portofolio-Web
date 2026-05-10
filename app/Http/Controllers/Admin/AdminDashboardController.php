<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Artwork;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Hanya admin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    /**
     * Tampilkan halaman dashboard utama
     */
    public function index()
    {
        // Hitung statistik
        $totalArtworks = Artwork::count();
        $totalPublished = Artwork::published()->count();
        $totalArtists = User::where('is_admin', false)->count();
        $totalVisitors = Visitor::count();
        $todayVisitors = Visitor::countToday();
        $uniqueVisitors = Visitor::countUniqueToday();

        // Data untuk grafik visitor 7 hari terakhir
        $visitorChartData = Visitor::getChartData(7);

        // Format data untuk chart
        $chartLabels = $visitorChartData->pluck('date');
        $chartData = $visitorChartData->pluck('count');

        // Artwork terbaru
        $recentArtworks = Artwork::with('user')
                                  ->latest()
                                  ->take(5)
                                  ->get();

        return view('admin.dashboard', [
            'totalArtworks' => $totalArtworks,
            'totalPublished' => $totalPublished,
            'totalArtists' => $totalArtists,
            'totalVisitors' => $totalVisitors,
            'todayVisitors' => $todayVisitors,
            'uniqueVisitors' => $uniqueVisitors,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'recentArtworks' => $recentArtworks,
        ]);
    }
}
