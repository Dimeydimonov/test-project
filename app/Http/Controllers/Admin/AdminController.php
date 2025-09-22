<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function __construct()
    {
    }

    public function dashboard()
    {
        $stats = [
            'total_artworks' => Artwork::count(),
            'available_artworks' => Artwork::where('is_available', true)->count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
        ];

        $recentArtworks = Artwork::with(['user', 'categories'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->take(5)
            ->get();

        $popularArtworks = Artwork::with(['user'])
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentArtworks',
            'popularArtworks',
            'recentUsers'
        ));
    }

    public function analytics()
    {
        $monthlyStats = DB::table('artworks')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $popularCategories = Category::withCount('artworks')
            ->orderBy('artworks_count', 'desc')
            ->take(10)
            ->get();

        $userActivity = User::withCount(['artworks', 'comments', 'likes'])
            ->orderBy('artworks_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.analytics', compact(
            'monthlyStats',
            'popularCategories',
            'userActivity'
        ));
    }
}
