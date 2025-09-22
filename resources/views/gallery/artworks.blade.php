@extends('layouts.gallery')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Все работы</h1>
    
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Категории</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('gallery.index') }}" 
               class="px-4 py-2 rounded-full {{ request()->is('gallery') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-indigo-700 hover:text-white transition-colors">
                Все работы
            </a>
            @foreach($categories as $category)
                <a href="{{ route('gallery.category', $category) }}" 
                   class="px-4 py-2 rounded-full {{ request()->is('gallery/category/'.$category->slug) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-indigo-700 hover:text-white transition-colors">
                    {{ $category->name }} ({{ $category->artworks_count }})
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($artworks as $artwork)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <a href="{{ route('gallery.show', $artwork) }}" class="block">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden">
                        <img src="{{ $artwork->getFirstMediaUrl('images') }}" 
                             alt="{{ $artwork->title }}" 
                             class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $artwork->title }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $artwork->user->name }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-heart text-red-500"></i> {{ $artwork->likes_count }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="far fa-comment"></i> {{ $artwork->comments_count ?? 0 }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">Работы не найдены</p>
            </div>
        @endforelse
    </div>

    @if($artworks->hasPages())
        <div class="mt-8">
            {{ $artworks->links() }}
        </div>
    @endif
</div>
@endsection
