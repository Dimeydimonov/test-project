<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artwork\StoreArtworkRequest;
use App\Http\Requests\Artwork\UpdateArtworkRequest;
use App\Http\Resources\ArtworkResource;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $artworks = Artwork::with(['category', 'user', 'likes'])
            ->latest()
            ->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $artworks
        ]);
    }

    /**
     *
     * @param  \App\Http\Requests\Artwork\StoreArtworkRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreArtworkRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('artworks', 'public');
            $validated['image_path'] = $path;
            
            if (empty($validated['image_alt'])) {
                $validated['image_alt'] = $validated['title'] . ' - произведение искусства';
            }
        }
        
        $artwork = Artwork::create($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Произведение успешно создано',
            'data' => $artwork->load(['category', 'user'])
        ], 201);
    }

    /**
     *
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Artwork $artwork): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $artwork->load(['category', 'user', 'comments.user', 'likes.user'])
        ]);
    }

    /**
     *
     * @param  \App\Http\Requests\Artwork\UpdateArtworkRequest  $request
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateArtworkRequest $request, Artwork $artwork): JsonResponse
    {
        $this->authorize('update', $artwork);
        
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            if ($artwork->image_path) {
                Storage::disk('public')->delete($artwork->image_path);
            }
            
            $path = $request->file('image')->store('artworks', 'public');
            $validated['image_path'] = $path;
        }
        
        $artwork->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Произведение успешно обновлено',
            'data' => $artwork->load(['category', 'user'])
        ]);
    }

    /**
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Artwork $artwork): JsonResponse
    {
        $this->authorize('delete', $artwork);

        if ($artwork->image_url) {
            $image = str_replace('/storage/', '', parse_url($artwork->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($image);
        }

        $artwork->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Произведение успешно удалено'
        ]);
    }

    /**
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCategory(Category $category): JsonResponse
    {
        $artworks = $category->artworks()
            ->with(['category', 'user', 'likes'])
            ->latest()
            ->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $artworks,
            'category' => $category
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured(): JsonResponse
    {
        $artworks = Artwork::with(['category', 'user', 'likes'])
            ->where('is_featured', true)
            ->inRandomOrder()
            ->take(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $artworks
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ]);

        $query = $request->input('query');
        
        $artworks = Artwork::with(['category', 'user', 'likes'])
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->latest()
            ->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $artworks,
            'search_query' => $query
        ]);
    }

    /**
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(Artwork $artwork): JsonResponse
    {
        $user = Auth::user();
        
        $existingLike = $artwork->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            return response()->json([
                'status' => 'error',
                'message' => 'Вы уже поставили лайк этому произведению'
            ], 422);
        }
        
        $like = new Like(['user_id' => $user->id]);
        $artwork->likes()->save($like);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Лайк успешно поставлен',
            'likes_count' => $artwork->likes()->count()
        ]);
    }

    /**
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike(Artwork $artwork): JsonResponse
    {
        $user = Auth::user();
        
        $like = $artwork->likes()->where('user_id', $user->id)->first();
        
        if (!$like) {
            return response()->json([
                'status' => 'error',
                'message' => 'Вы еще не ставили лайк этому произведению'
            ], 422);
        }
        
        $like->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Лайк успешно удален',
            'likes_count' => $artwork->likes()->count()
        ]);
    }
}
