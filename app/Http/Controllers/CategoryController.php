<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::with('items')->get();
            return response()->json([
                'success' => true,
                'message' => 'List of categories',
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $category = Category::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show(Category $category)
    {
        try {
            $category->load('items');
            return response()->json([
                'success' => true,
                'message' => 'Category detail',
                'data' => $category,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $category->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
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
}
