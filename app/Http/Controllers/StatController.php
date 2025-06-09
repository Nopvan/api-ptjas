<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Photo;
use App\Models\PortfolioVisitor;
use Carbon\Carbon;

class StatController extends Controller
{
    public function index()
    {
        $portfolioCount = Portfolio::count(); // total semua portfolio
        $photoCount = Photo::count(); // total semua foto

        // Ambil tanggal 30 hari lalu dari sekarang
        $startDate = Carbon::now()->subDays(30);

        // Hitung pengunjung unik dalam 30 hari terakhir
        $visitorCount = PortfolioVisitor::where('created_at', '>=', $startDate)
            ->distinct('visitor_ip')
            ->count('visitor_ip');

        return response()->json([
            'portfolio_count' => $portfolioCount,
            'photo_count' => $photoCount,
            'visitor_count' => $visitorCount
        ]);
    }
}
