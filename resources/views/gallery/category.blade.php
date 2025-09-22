@extends('layouts.gallery')

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-home mr-2"></i>
                    Главная
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <a href="{{ route('gallery.index') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Галерея</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                <p class="text-gray-600">
                    {{ $category->artworks_count }} {{ trans_choice('работа|работы|работ', $category->artworks_count) }}
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="relative">
                    <select id="sort" class="block appearance-none w-full md:w-64 bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="newest">Сначала новые</option>
                        <option value="oldest">Сначала старые</option>
                        <option value="popular">По популярности</option>
                        <option value="price_asc">По цене (низкая > высокая)</option>
                        <option value="price_desc">По цене (высокая > низкая)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </div>
        
        @if($category->description)
            <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
                <p class="text-indigo-800">{{ $category->description }}</p>
            </div>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Категории</h2>
                <div class="space-y-2">
                    <a href="{{ route('gallery.index') }}" 
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('gallery') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-th-large mr-2 text-indigo-500"></i>
                        Все категории
                        <span class="ml-auto bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ App\Models\Artwork::where('is_available', true)->count() }}
                        </span>
                    </a>
                    
                    @foreach($categories as $cat)
                        <a href="{{ route('gallery.category', $cat->slug) }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ $cat->id === $category->id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <i class="fas fa-{{ $cat->icon ?? 'image' }} mr-2 text-indigo-500"></i>
                            {{ $cat->name }}
                            <span class="ml-auto bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $cat->artworks_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Фильтры</h3>
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium text-gray-700">Цена</label>
                            <span class="text-sm text-gray-500" id="price-range-value">0 - 100 000 ₽</span>
                        </div>
                        <div class="px-1">
                            <div id="price-range" class="h-2 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-xs text-gray-500">0 ₽</span>
                            <span class="text-xs text-gray-500">100 000+ ₽</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Материалы</h4>
                        <div class="space-y-2">
                            @php
                                $materials = ['Холст', 'Бумага', 'Дерево', 'Металл', 'Стекло'];
                            @endphp
                            @foreach($materials as $material)
                                <div class="flex items-center">
                                    <input id="material-{{ $loop->index }}" name="materials[]" type="checkbox" 
                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="material-{{ $loop->index }}" class="ml-2 text-sm text-gray-700">
                                        {{ $material }}
                                    </label>
                                    <span class="ml-auto text-xs text-gray-500">({{ rand(5, 50) }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Размер</h4>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $sizes = ['S', 'M', 'L', 'XL'];
                            @endphp
                            @foreach($sizes as $size)
                                <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="w-full mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Сбросить фильтры
                    </button>
                </div>
            </div>
        </div>
        <div class="lg:w-3/4">
            @if($artworks->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($artworks as $artwork)
                        @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $artworks->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-indigo-100">
                        <i class="fas fa-palette text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Работы не найдены</h3>
                    <p class="mt-1 text-gray-500">Попробуйте изменить параметры фильтрации или загляните позже.</p>
                    <div class="mt-6">
                        <a href="{{ route('gallery.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Вернуться в галерею
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.css">
<style>
    .pagination {
        @apply flex justify-center items-center space-x-1;
    }
    
    .pagination .page-item {
        @apply inline-flex;
    }
    
    .pagination .page-link {
        @apply px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
    }
    
    .pagination .active .page-link {
        @apply bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700;
    }
    
    .pagination .disabled .page-link {
        @apply text-gray-400 bg-gray-100 cursor-not-allowed;
    }
    
    .noUi-connect {
        @apply bg-indigo-600;
    }
    
    .noUi-horizontal {
        @apply h-1;
    }
    
    .noUi-handle {
        @apply w-4 h-4 -top-1.5 -right-2 rounded-full bg-white border-2 border-indigo-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
    }
    
    .noUi-handle:before,
    .noUi-handle:after {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceRange = document.getElementById('price-range');
        const priceRangeValue = document.getElementById('price-range-value');
        
        if (priceRange) {
            noUiSlider.create(priceRange, {
                start: [0, 100000],
                connect: true,
                range: {
                    'min': 0,
                    'max': 100000
                },
                step: 1000,
                format: {
                    to: function(value) {
                        return Math.round(value);
                    },
                    from: function(value) {
                        return Number(value);
                    }
                }
            });
            
            priceRange.noUiSlider.on('update', function(values, handle) {
                const min = parseInt(values[0]);
                const max = parseInt(values[1]);
                const minFormatted = min.toLocaleString('ru-RU');
                const maxFormatted = max >= 100000 ? '100 000+' : max.toLocaleString('ru-RU');
                priceRangeValue.textContent = `${minFormatted} - ${maxFormatted} ₽`;

            });
        }
        
        const sortSelect = document.getElementById('sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                console.log('Sort by:', sortValue);
            });
        }
        document.querySelectorAll('input[name="materials[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedMaterials = Array.from(document.querySelectorAll('input[name="materials[]"]:checked'))
                    .map(checkbox => checkbox.nextElementSibling.textContent.trim());

                console.log('Selected materials:', selectedMaterials);
            });
        });
        
        document.querySelectorAll('button[data-size]').forEach(button => {
            button.addEventListener('click', function() {
                const size = this.getAttribute('data-size');
                this.classList.toggle('bg-indigo-50');
                this.classList.toggle('border-indigo-500');
                this.classList.toggle('text-indigo-700');
                
                const isActive = this.classList.contains('bg-indigo-50');

                console.log('Size filter:', isActive ? size : 'none');
            });
        });

        const resetFiltersBtn = document.querySelector('button[data-reset-filters]');
        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', function() {
                document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                document.querySelectorAll('button[data-size]').forEach(button => {
                    button.classList.remove('bg-indigo-50', 'border-indigo-500', 'text-indigo-700');
                });
                
                if (priceRange.noUiSlider) {
                    priceRange.noUiSlider.set([0, 100000]);
                }
                
                if (sortSelect) {
                    sortSelect.value = 'newest';
                }
                console.log('Filters reset');
            });
        }
    });
    function updateFilters(filters) {
        console.log('Updating filters:', filters);

    }
    function updateSorting(sortBy) {
        console.log('Updating sort:', sortBy);

    }
    function loadResults(params) {
        console.log('Loading results with params:', params);
    }
    function updateResults(data) {
        console.log('Updating results:', data);
    }
</script>
@endpush
