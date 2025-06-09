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
            $portfolios = Portfolio::with('photos')->paginate(10);
            $ip = request()->ip();

            foreach ($portfolios as $portfolio) {
                $alreadyVisited = PortfolioVisitor::where('portfolio_id', $portfolio->id)
                    ->where('visitor_ip', $ip)
                    ->exists();

                if (!$alreadyVisited) {
                    PortfolioVisitor::create([
                        'portfolio_id' => $portfolio->id,
                        'visitor_ip' => $ip,
                    ]);
                    // GAK PERLU INI:
                    // $portfolio->increment('views');
                }
            }

            return response()->json($portfolios);
        } catch (\Exception $e) {
            dd($e->getMessage()); // buat debug kalau masih error
        }
    }
}
