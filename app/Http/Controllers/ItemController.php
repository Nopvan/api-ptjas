<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        try {
            $items = Item::with('category')->get();

            $items->transform(function ($item) {
                $item->url = asset('storage/' . $item->photo);
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'List of items',
                'data' => $items,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $path = $request->file('image')->store('items', 'public');

            $item = Item::create([
                'name' => $request->name,
                'photo' => $path,
                'description' => $request->description,
                'category_id' => $request->category_id,
            ]);

            $item->url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item,
            ], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show(Item $item)
    {
        try {
            $item->load('category');
            return response()->json([
                'success' => true,
                'message' => 'Item detail',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->hasFile('image')) {
                // Hapus photo lama
                if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                    Storage::disk('public')->delete($item->photo);
                }

                // Simpan photo baru
                $path = $request->file('image')->store('items', 'public');
                $item->photo = $path;
            }

            $item->update($request->except(['image']));
            $item->url = asset('storage/' . $item->photo);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }


    public function destroy(Item $item)
    {
        try {
            // Hapus file photo
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Server Error: ' . $e->getMessage(),
        ], 500);
    }

    public function publicIndex(Request $request)
    {
        try {
            $query = Item::with('category');

            // Search by item name
            if ($request->has('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            // Filter by category_id
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $items = $query->get();

            // Tambahkan URL foto
            $items->transform(function ($item) {
                $item->url = $item->photo ? asset('storage/' . $item->photo) : null;
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'List of items',
                'data' => $items,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
