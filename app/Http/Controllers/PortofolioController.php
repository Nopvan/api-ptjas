<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioVisitor;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PortofolioController extends Controller
{
    // PUBLIC - Guest bisa akses
    public function index()
    {
        $portfolios = Portfolio::with('photos')->paginate(10);
        return $portfolios;
    }

    // PUBLIC - Guest bisa akses
    public function show($id)
    {
        $portfolio = Portfolio::with('photos')->findOrFail($id);
        return response()->json($portfolio);
    }

    // PROTECTED - Harus login (route diapi.php dibatasi sanctum)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'client_name' => 'required|string',
            'date' => 'required|date',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $portfolio = Portfolio::create([
            'title' => $request->title,
            'description' => $request->description,
            'client_name' => $request->client_name,
            'date' => $request->date,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('portfolios', 'public');
                $portfolio->photos()->create([
                    'photo_path' => $path
                ]);
            }
        }

        return response()->json($portfolio->load('photos'), 201);
    }

    // PROTECTED
    public function update(Request $request, $id)
    {
        $portfolio = Portfolio::findOrFail($id);

        $portfolio->update($request->only([
            'title', 'description', 'client_name', 'date'
        ]));

        return response()->json($portfolio);
    }

    // PROTECTED
    public function destroy($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        foreach ($portfolio->photos as $photo) {
            if (Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            } else {
                Log::warning("File not found: " . $photo->photo_path);
            }
            $photo->delete();
        }


        $portfolio->delete();

        return response()->json(['message' => 'Portfolio deleted']);
    }

    public function getPortfolioforIndex()
    {
        try {
            $ip = request()->ip();

            // Cek apakah IP ini sudah pernah akses endpoint ini
            $alreadyVisited = PortfolioVisitor::where('visitor_ip', $ip)->exists();

            if (!$alreadyVisited) {
                try {
                    PortfolioVisitor::create([
                        'visitor_ip' => $ip,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Visitor logging error (index-level): ' . $e->getMessage());
                }
            }

            $photos = Photo::with('portfolio')->get();

            $formatted = $photos->map(function ($photo) {
                $portfolio = $photo->portfolio;

                return [
                    'id' => $photo->id,
                    'photo_path' => $photo->photo_path,
                    'caption' => $photo->caption,
                    'title' => $portfolio->title ?? null,
                    'description' => $portfolio->description ?? null,
                    'client_name' => $portfolio->client_name ?? null,
                    'date' => $portfolio->date ?? null,
                    'views' => $portfolio->views ?? null,
                ];
            })->sortBy('client_name')->values();

            return response()->json([
                'data' => $formatted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function showPortfolioforIndex($id)
    {
        try {
            $photo = Photo::with('portfolio')->findOrFail($id);

            $formatted = [
                'id' => $photo->id,
                'photo_path' => $photo->photo_path,
                'caption' => $photo->caption,
                'title' => $photo->portfolio->title ?? null,
                'description' => $photo->portfolio->description ?? null,
                'client_name' => $photo->portfolio->client_name ?? null,
                'date' => $photo->portfolio->date ?? null,
            ];

            return response()->json([
                'data' => $formatted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Photo not found or something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
