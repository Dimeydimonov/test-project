@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Все работы</h1>
        <p class="text-gray-600 dark:text-gray-300">Просмотрите все произведения искусства в нашей галерее</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="lg:w-1/4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Фильтры</h2>
                
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Категории</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input id="all-categories" type="radio" name="category" value="all" checked 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <label for="all-categories" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Все категории
                            </label>
                        </div>
                        @foreach($categories as $category)
                            <div class="flex items-center">
                                <input id="category-{{ $category->id }}" type="radio" name="category" value="{{ $category->slug }}"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <label for="category-{{ $category->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $category->name }} ({{ $category->artworks_count }})
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Сортировать по</h3>
                    <select id="sort-by" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="newest">Сначала новые</option>
                        <option value="oldest">Сначала старые</option>
                        <option value="popular">Самые популярные</option>
                        <option value="likes">Больше лайков</option>
                    </select>
                </div>

                <button type="button" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Сбросить фильтры
                </button>
            </div>
        </div>

        <div class="lg:w-3/4">
            @if($artworks->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($artworks as $artwork)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <a href="{{ route('gallery.show', $artwork->slug) }}" class="block">
                                <div class="aspect-w-4 aspect-h-3 bg-gray-100 dark:bg-gray-700">
                                    <img src="{{ $artwork->getFirstMediaUrl('images', 'medium') }}" 
                                         alt="{{ $artwork->title }}" 
                                         class="w-full h-64 object-cover">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-1">{{ $artwork->title }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $artwork->user->name }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $artwork->created_at->diffForHumans() }}
                                        </span>
                                        <div class="flex items-center">
                                            <span class="text-yellow-500 mr-1">★</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $artwork->likes_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $artworks->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Работ не найдено</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Попробуйте изменить параметры фильтрации.</p>
                    <div class="mt-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Сбросить фильтры
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryRadios = document.querySelectorAll('input[name="category"]');
        categoryRadios.forEach(radio => {
            radio.addEventListener('change', applyFilters);
        });
        const sortBySelect = document.getElementById('sort-by');
        if (sortBySelect) {
            sortBySelect.addEventListener('change', applyFilters);
        }
        const resetButton = document.querySelector('button[type="button"]');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                document.getElementById('all-categories').checked = true;
                sortBySelect.value = 'newest';
                applyFilters();
            });
        }
        function applyFilters() {
            const selectedCategory = document.querySelector('input[name="category"]:checked').value;
            const sortBy = sortBySelect ? sortBySelect.value : 'newest';
            console.log('Category:', selectedCategory);
            console.log('Sort by:', sortBy);
        }
    });
</script>
@endpush
@endsection
