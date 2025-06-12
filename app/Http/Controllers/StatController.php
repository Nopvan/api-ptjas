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
        try {
            $totalPortfolios = Portfolio::count();
            $totalPhotos = Photo::count();
            $totalVisitors = PortfolioVisitor::distinct('visitor_ip')->count('visitor_ip');

            return response()->json([
                'portfolio_count' => $totalPortfolios,
                'photo_count' => $totalPhotos,
                'visitor_count' => $totalVisitors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
