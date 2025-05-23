<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::with('portfolio')->paginate(10);

        $photos->transform(function ($photo) {
            $photo->url = asset('storage/' . $photo->photo_path);
            return $photo;
        });

        return response()->json($photos);
    }

    // ✅ Hanya bisa digunakan oleh admin (pastikan middleware auth:sanctum di route)
    public function store(Request $request)
    {
        $request->validate([
            'portfolio_id' => 'required|exists:portfolios,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('portfolios', 'public');

        $photo = Photo::create([
            'portfolio_id' => $request->portfolio_id,
            'photo_path' => $path,
        ]);

        return response()->json([
            'id' => $photo->id,
            'portfolio_id' => $photo->portfolio_id,
            'photo_path' => $photo->photo_path,
            'url' => asset('storage/' . $photo->photo_path), // ✅ kirim URL lengkap
        ], 201);
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        // Hapus dari storage
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted']);
    }
}
