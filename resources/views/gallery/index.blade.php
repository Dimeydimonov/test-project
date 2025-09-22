@extends('layouts.gallery')

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title fade-in">Добро пожаловать в ArtGallery</h1>
        <p class="hero-subtitle">
            Откройте для себя удивительные произведения искусства от талантливых художников со всего мира.
        </p>
        <div class="hero-buttons">
            <a href="#featured" class="btn-primary">
                <i class="fas fa-paint-brush"></i> Смотреть галерею
            </a>
            <a href="#categories" class="btn-secondary">
                <i class="fas fa-tags"></i> Категории
            </a>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    @if($featuredArtist)
    <section class="mb-16 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="md:flex">
            <div class="md:flex-shrink-0">
                <div class="h-48 md:h-full md:w-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                    <i class="fas fa-user-astronaut text-6xl text-indigo-400"></i>
                </div>
            </div>
            <div class="p-8">
                <div class="uppercase tracking-wide text-sm text-indigo-600 font-semibold">Художник недели</div>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $featuredArtist->name }}</h2>
                <p class="mt-3 text-gray-600">
                    {{ $featuredArtist->bio ?? 'Талантливый художник с уникальным стилем и видением.' }}
                </p>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">{{ $featuredArtist->artworks_count }} работ в галерее</span>
                </div>
                <div class="mt-6">
                    <a href="/gallery" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center">
                        Смотреть портфолио <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section id="featured" class="mb-16 scroll-mt-20">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Избранные работы</h2>
                <p class="text-gray-600 mt-2">Самые популярные произведения в нашей галерее</p>
            </div>
            <a href="/gallery" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center mt-4 sm:mt-0">
                Смотреть все работы <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        @if($featuredArtworks->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($featuredArtworks as $artwork)
                    @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <i class="fas fa-palette text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Пока нет избранных работ.</p>
            </div>
        @endif
    </section>

    <section id="categories" class="mb-16 scroll-mt-20">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Категории</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Исследуйте нашу коллекцию по категориям</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="/gallery/category/{{ $category->id }}" 
                   class="category-card group">
                    <div class="relative overflow-hidden rounded-t-lg">
                        <div class="h-40 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                            <i class="fas fa-palette text-5xl text-indigo-400 group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Категория</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mb-16">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Последние поступления</h2>
                <p class="text-gray-600 mt-2">Самые свежие работы в нашей коллекции</p>
            </div>
            <a href="/gallery" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center mt-4 sm:mt-0">
                Смотреть все работы <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        @if($recentArtworks->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($recentArtworks as $artwork)
                    @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <i class="fas fa-palette text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Пока нет недавно добавленных работ.</p>
            </div>
        @endif
    </section>

    @if($popularArtworks->count() > 0)
    <section class="mb-16">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Популярные работы</h2>
                <p class="text-gray-600 mt-2">Самые популярные работы по количеству лайков</p>
            </div>
            <a href="{{ route('gallery.all', ['sort' => 'popular']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center mt-4 sm:mt-0">
                Смотреть все <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($popularArtworks as $artwork)
                @include('gallery.partials.artwork-card', ['artwork' => $artwork])
            @endforeach
        </div>
    </section>
    @endif
</div>

<div class="bg-gradient-to-r from-indigo-700 to-purple-700 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Хотите выставить свои работы?</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">Присоединяйтесь к нашему сообществу художников и демонстрируйте свои произведения тысячам ценителей искусства</p>
        @guest
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-3 rounded-lg text-lg font-semibold transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-user-plus mr-2"></i> Зарегистрироваться
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white hover:bg-white hover:bg-opacity-10 px-8 py-3 rounded-lg text-lg font-medium transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Войти в аккаунт
                </a>
            </div>
        @else
            <a href="{{ route('admin.dashboard') }}" class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-3 rounded-lg text-lg font-semibold transition-colors duration-200 inline-flex items-center">
                <i class="fas fa-tachometer-alt mr-2"></i> Панель управления
            </a>
        @endguest
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Adjust for fixed header
                    behavior: 'smooth'
                });
                
                history.pushState(null, '', targetId);
            }
        });
    });
    
    window.addEventListener('popstate', function() {
        const hash = window.location.hash;
        if (hash) {
            const target = document.querySelector(hash);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
</script>
@endpush
@endsection
