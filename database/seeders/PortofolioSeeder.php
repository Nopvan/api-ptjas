<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Portfolio;
use App\Models\Photo;

class PortofolioSeeder extends Seeder
{
    public function run()
    {
        // contoh portfolio 1
        $portfolio = Portfolio::create([
            'title' => 'Website Company A',
            'description' => 'Website company A with responsive design.',
            'client_name' => 'Company A',
            'date' => '2024-05-01',
        ]);

        // contoh photo portfolio 1
        Photo::create([
            'portfolio_id' => $portfolio->id,
            'photo_path' => 'portfolios/sample1.jpg',
            'caption' => 'Landing page design',
        ]);

        Photo::create([
            'portfolio_id' => $portfolio->id,
            'photo_path' => 'portfolios/sample2.png',
            'caption' => 'Homepage design',
        ]);

        // contoh portfolio 2
        $portfolio2 = Portfolio::create([
            'title' => 'Mobile App B',
            'description' => 'Mobile app for client B with smooth UX.',
            'client_name' => 'Client B',
            'date' => '2024-06-15',
        ]);

        Photo::create([
            'portfolio_id' => $portfolio2->id,
            'photo_path' => 'portfolios/sample3.png',
            'caption' => 'Login screen design',
        ]);
    }
}
