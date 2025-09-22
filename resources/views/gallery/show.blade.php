@extends('layouts.gallery')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ url()->previous() === url()->current() ? route('gallery.index') : url()->previous() }}" 
           class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Назад
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <div class="md:w-2/3 relative">
                <div class="relative w-full" style="padding-bottom: 100%;">
                    @php($img = $artwork->main_image_url)
                    @if($img)
                        <img src="{{ $img }}" 
                             alt="{{ $artwork->title }}" 
                             class="absolute inset-0 w-full h-full object-contain p-4">
                    @else
                        <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-100">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                        </div>
                    @endif
                </div>
                
                <div class="absolute top-4 right-4">
                    <button type="button" 
                            class="like-button {{ $artwork->isLikedBy(auth()->id()) ? 'liked' : '' }}"
                            data-artwork-id="{{ $artwork->id }}">
                        @if($artwork->isLikedBy(auth()->id()))
                            <i class="fas fa-heart text-red-500"></i>
                        @else
                            <i class="far fa-heart"></i>
                        @endif
                        <span class="like-count ml-1">{{ $artwork->likes_count ?? 0 }}</span>
                    </button>
                </div>
                
                <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                    <i class="far fa-eye mr-1"></i> {{ $artwork->views }} просмотров
                </div>
            </div>
            
            <div class="p-6 md:w-1/3 border-l border-gray-100">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $artwork->title }}</h1>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $artwork->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $artwork->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    @if($artwork->price > 0)
                        <div class="bg-indigo-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-500 mb-1">Цена</p>
                            <p class="text-2xl font-bold text-indigo-700">{{ number_format($artwork->price, 0, ',', ' ') }} ₽</p>
                            @auth
                                <button class="w-full mt-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Купить сейчас
                                </button>
                                <button class="w-full mt-2 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-medium py-2 px-4 rounded-lg transition-colors">
                                    <i class="far fa-heart mr-2"></i> В избранное
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="block mt-3 text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Войдите для покупки
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
                
                @if($artwork->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Описание</h3>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($artwork->description)) !!}
                        </div>
                    </div>
                @endif
                
                @if($artwork->categories->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Категории</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($artwork->categories as $category)
                                <a href="{{ route('gallery.category', $category) }}" 
                                   class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Поделиться</h3>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-400 text-white flex items-center justify-center hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-pink-500 text-white flex items-center justify-center hover:bg-pink-600 transition-colors">
                            <i class="fab fa-pinterest"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex border-b border-gray-200">
                    <button class="px-6 py-4 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 focus:outline-none">
                        Детали
                    </button>
                    <button class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                        Доставка и возврат
                    </button>
                    <button class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                        Отзывы ({{ $artwork->comments_count }})
                    </button>
                </div>
                
                <div class="py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Детали работы</h4>
                            <dl class="space-y-3">
                                <div class="flex">
                                    <dt class="w-1/3 text-sm text-gray-500">Размеры</dt>
                                    <dd class="text-sm text-gray-900">{{ $artwork->width }} × {{ $artwork->height }} см</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm text-gray-500">Материалы</dt>
                                    <dd class="text-sm text-gray-900">{{ $artwork->materials ?? 'Не указаны' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm text-gray-500">Год создания</dt>
                                    <dd class="text-sm text-gray-900">{{ $artwork->year_created ?? 'Не указан' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-1/3 text-sm text-gray-500">Стиль</dt>
                                    <dd class="text-sm text-gray-900">{{ $artwork->style ?? 'Не указан' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">О художнике</h4>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900">{{ $artwork->user->name }}</h5>
                                    <p class="text-sm text-gray-500">Присоединился {{ $artwork->user->created_at->format('d.m.Y') }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('artists.show', $artwork->user) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Смотреть все работы художника <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Отзывы ({{ $artwork->comments_count }})</h2>
            @auth
                <button onclick="document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth' })" 
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Оставить отзыв
                </button>
            @else
                <a href="{{ route('login', ['redirect' => url()->current() . '#comment-form']) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Войдите, чтобы оставить отзыв
                </a>
            @endauth
        </div>
        
        @if($artwork->comments->count() > 0)
            <div class="space-y-6">
                @foreach($artwork->comments as $comment)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</h4>
                                        <div class="flex items-center mt-1">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $comment->rating)
                                                        <i class="fas fa-star text-yellow-400"></i>
                                                    @else
                                                        <i class="far fa-star text-gray-300"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="mx-2 text-gray-300">•</span>
                                            <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    @if(auth()->id() === $comment->user_id)
                                        <div class="flex space-x-2">
                                            <button class="text-gray-400 hover:text-indigo-600">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <button class="text-gray-400 hover:text-red-600">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-gray-700">{{ $comment->content }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="far fa-comment-alt text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Пока нет отзывов. Будьте первым, кто оставит отзыв!</p>
            </div>
        @endif
        
        @auth
            <div id="comment-form" class="mt-12 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Оставить отзыв</h3>
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="artwork_id" value="{{ $artwork->id }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ваша оценка</label>
                        <div class="flex items-center">
                            <div class="rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="hidden" {{ $i == 5 ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" class="text-2xl cursor-pointer">
                                        <i class="far fa-star text-yellow-400"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Ваш отзыв</label>
                        <textarea id="content" name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Отправить отзыв
                        </button>
                    </div>
                </form>
            </div>
        @endauth
    </div>
    
    @if($relatedArtworks->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Похожие работы</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedArtworks as $relatedArtwork)
                    @include('gallery.partials.artwork-card', ['artwork' => $relatedArtwork])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    
    .rating input {
        display: none;
    }
    
    .rating input:checked ~ label i,
    .rating:not(:checked) > label:hover i,
    .rating:not(:checked) > label:hover ~ label i {
        font-weight: 900;
    }
    
    .like-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white;
        color: #9CA3AF;
        border: 1px solid #E5E7EB;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .like-button:hover {
        background-color: #F3F4F6;
        color: #EF4444;
    }
    
    .like-button.liked {
        background-color: #FEE2E2;
        color: #EF4444;
        border-color: #FECACA;
    }
    
    .like-button i {
        font-size: 18px;
    }
    
    .like-count {
        font-size: 14px;
        margin-left: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleLike(artworkId) {
        const button = document.querySelector(`.like-button[data-artwork-id="${artworkId}"]`);
        const isLiked = button.getAttribute('data-liked') === 'true';
        const url = isLiked ? `/api/artworks/${artworkId}/unlike` : `/api/artworks/${artworkId}/like`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likeCount = button.querySelector('.like-count');
                const heartIcon = button.querySelector('i');
                
                if (isLiked) {
                    button.classList.remove('liked');
                    button.setAttribute('data-liked', 'false');
                    if (likeCount) {
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                } else {
                    button.classList.add('liked');
                    button.setAttribute('data-liked', 'true');
                    if (likeCount) {
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
    document.querySelectorAll('.rating input').forEach(input => {
        input.addEventListener('change', function() {
            const rating = this.value;
            const stars = this.parentElement.querySelectorAll('label i');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        });
    });
</script>
@endpush
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="font-medium">Дата создания:</span>
                                <span>{{ $artwork->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Размеры:</span>
                                <span>{{ $artwork->width }} × {{ $artwork->height }} см</span>
                            </div>
                            <div>
                                <span class="font-medium">Просмотры:</span>
                                <span>{{ number_format($artwork->views, 0, ',', ' ') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Лайки:</span>
                                <span>{{ number_format($artwork->likes_count ?? 0, 0, ',', ' ') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <button class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Скачать
                        </button>
                        <button class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center justify-center dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            Поделиться
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Комментарии ({{ $artwork->comments->count() }})</h2>
                
                @auth
                    <form action="{{ route('comments.store', $artwork) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="mb-4">
                            <label for="comment" class="sr-only">Ваш комментарий</label>
                            <textarea id="comment" name="content" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" 
                                      placeholder="Оставьте комментарий..." required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Отправить
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">Войдите</a> 
                            или 
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">зарегистрируйтесь</a>,
                            чтобы оставить комментарий.
                        </p>
                    </div>
                @endauth

                @if($artwork->comments->count() > 0)
                    <div class="space-y-6">
                        @foreach($artwork->comments as $comment)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-start">
                                    <img src="{{ $comment->user->getFirstMediaUrl('avatar', 'thumb') }}" 
                                         alt="{{ $comment->user->name }}" 
                                         class="w-10 h-10 rounded-full mr-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</h4>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Пока нет комментариев. Будьте первым!</p>
                @endif
            </div>
        </div>

        @if($artwork->categories->count() > 0)
            <div class="mt-12">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Похожие работы</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($artwork->categories->first()->artworks()->where('id', '!=', $artwork->id)->take(6)->get() as $relatedArtwork)
                        <a href="{{ route('gallery.show', $relatedArtwork->slug) }}" class="block group">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                <img src="{{ $relatedArtwork->getFirstMediaUrl('images', 'thumb') }}" 
                                     alt="{{ $relatedArtwork->title }}" 
                                     class="h-full w-full object-cover object-center group-hover:opacity-75">
                            </div>
                            <div class="mt-2">
                                <h3 class="text-sm text-gray-700 dark:text-gray-300 font-medium truncate">{{ $relatedArtwork->title }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $relatedArtwork->user->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
