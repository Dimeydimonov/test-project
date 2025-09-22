<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = $path;
        }
        
        $category = Category::create($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Категория успешно создана',
            'data' => $category
        ], 201);
    }

    
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $category->load('artworks')
        ]);
    }

    
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = $path;
        }
        
        $category->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Категория успешно обновлена',
            'data' => $category
        ]);
    }

    
    
    public function destroy(Category $category): JsonResponse
    {
        if ($category->image_url) {
            $image = str_replace('/storage/', '', parse_url($category->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($image);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Категория успешно удалена'
        ]);
    }
}
