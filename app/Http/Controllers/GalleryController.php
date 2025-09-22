<?php

namespace App\Http\Controllers;

use App\Models\Models\Artwork;
use App\Models\Models\Category;
use App\Models\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    /**
     * @return \Illuminate\View\View
     */
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $featuredArtworks = collect(); // Пустая коллекция пока нет данных
            $recentArtworks = collect();
            $popularArtworks = collect();
            $categories = collect();
            $featuredArtist = null;

            try {
                $featuredArtworks = Artwork::with(['user', 'categories'])
                    ->where('is_available', true)
                    ->take(8)
                    ->get();
                    
                $categories = Category::take(10)->get();
                
                $recentArtworks = Artwork::with(['user', 'categories'])
                    ->where('is_available', true)
                    ->latest()
                    ->take(8)
                    ->get();
            } catch (\Exception $dbError) {
                \Log::info('Database not ready yet: ' . $dbError->getMessage());
            }

            return view('gallery.index', compact(
                'featuredArtworks',
                'recentArtworks', 
                'popularArtworks',
                'featuredArtist',
                'categories'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in GalleryController@index: ' . $e->getMessage());
            return response()->view('gallery.index', [
                'featuredArtworks' => collect(),
                'recentArtworks' => collect(),
                'popularArtworks' => collect(),
                'featuredArtist' => null,
                'categories' => collect()
            ]);
        }
    }

    /**
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    /**
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        try {
            $artwork = Artwork::with([
                'user',
                'categories',
                'comments' => function($query) {
                    $query->with('user')
                          ->where('is_approved', true)
                          ->latest();
                },
                'likes'
            ])
            ->where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

            $artwork->increment('views');

            $relatedArtworks = Artwork::whereHas('categories', function($query) use ($artwork) {
                $query->whereIn('categories.id', $artwork->categories->pluck('id'));
            })
            ->where('id', '!=', $artwork->id)
            ->where('is_available', true)
            ->with(['user', 'categories'])
            ->withCount('likes')
            ->inRandomOrder()
            ->take(6)
            ->get();

            return view('gallery.show', [
                'artwork' => $artwork,
                'relatedArtworks' => $relatedArtworks,
                'title' => $artwork->title . ' | ' . config('app.name'),
                'description' => $artwork->description ?: 'Произведение искусства от ' . $artwork->user->name
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in GalleryController@show: ' . $e->getMessage());
            return back()->with('error', 'Произведение не найдено или временно недоступно.');
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function all(Request $request)
    {
        try {
            $query = Artwork::with(['user', 'categories'])
                ->where('is_available', true)
                ->withCount(['likes', 'comments'])
                ->latest();

            if ($request->has('category')) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'likes':
                    $query->orderBy('likes_count', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }

            $artworks = $query->paginate(12);
            $categories = Category::withCount('artworks')->orderBy('name')->get();

            return view('gallery.artworks', [
                'artworks' => $artworks,
                'categories' => $categories,
                'title' => 'Все работы',
                'description' => 'Просмотр всех произведений искусства в галерее.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in GalleryController@all: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при загрузке работ.');
        }
    }

    /**
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function category($slug, Request $request)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            
            $query = $category->artworks()
                ->with(['user', 'categories'])
                ->where('is_available', true)
                ->withCount('likes')
                ->latest();

            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'likes':
                    $query->withCount('likes')
                          ->orderBy('likes_count', 'desc');
                    break;
                default:
                    $query->latest();
            }

            $artworks = $query->paginate(24);

            $categories = Category::withCount(['artworks' => function($query) {
                $query->where('is_available', true);
            }])
            ->orderBy('name')
            ->get();

            return view('gallery.category', [
                'artworks' => $artworks,
                'category' => $category,
                'categories' => $categories,
                'title' => $category->name . ' | ' . config('app.name'),
                'description' => $category->description ?: 'Работы в категории ' . $category->name,
                'sortBy' => $sortBy
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in GalleryController@category: ' . $e->getMessage());
            return back()->with('error', 'Категория не найдена или временно недоступна.');
        }
    }
}
