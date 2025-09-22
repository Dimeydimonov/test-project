<div class="artwork-card group">
    <a href="/gallery/{{ $artwork->id }}" class="block relative">
        @if($artwork->is_featured)
            <span class="featured-badge">
                <i class="fas fa-star mr-1"></i> Избранное
            </span>
        @endif

        <button type="button"
                class="like-button"
                data-artwork-id="{{ $artwork->id }}">
            <i class="far fa-heart"></i>
            <span class="like-count ml-1">{{ $artwork->likes_count ?? 0 }}</span>
        </button>

        <div class="artwork-image-container">
            @if($artwork->image_path)
                <img src="{{ Storage::url($artwork->image_path) }}" 
                     alt="{{ $artwork->title }}" 
                     class="artwork-image" 
                     loading="lazy">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
            @endif
        </div>

        @if($artwork->price > 0)
            <div class="price-tag">
                {{ number_format($artwork->price, 0, ',', ' ') }} ₽
            </div>
        @endif
    </a>

    <div class="artwork-info">
        <div class="flex justify-between items-start">
            <div class="pr-2">
                <h3 class="artwork-title" title="{{ $artwork->title }}">
                    {{ Str::limit($artwork->title, 30) }}
                </h3>
                <p class="artwork-artist">
                    {{ $artwork->user->name }}
                </p>
            </div>
            
            <div class="flex space-x-3 text-gray-500 text-sm">
                <span class="flex items-center" title="{{ $artwork->likes_count ?? 0 }} лайков">
                    <i class="fas fa-heart mr-1"></i>
                    <span>{{ $artwork->likes_count ?? 0 }}</span>
                </span>
                <span class="flex items-center" title="{{ $artwork->comments_count ?? 0 }} комментариев">
                    <i class="far fa-comment mr-1"></i>
                    <span>{{ $artwork->comments_count ?? 0 }}</span>
                </span>
            </div>
        </div>
        
        @if($artwork->categories->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($artwork->categories->take(2) as $category)
                    <a href="/gallery/category/{{ $category->id }}" 
                       class="inline-block px-2 py-1 text-xs rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                        {{ $category->name }}
                    </a>
                @endforeach
                @if($artwork->categories->count() > 2)
                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">
                        +{{ $artwork->categories->count() - 2 }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
@endpush
