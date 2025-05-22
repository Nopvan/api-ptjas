<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // ✅ Hanya bisa digunakan oleh admin (middleware auth:sanctum nanti di routes)
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

        return response()->json($photo, 201);
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
