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

    <div class="main-content">
        @if($featuredArtist)
            <section class="artist-week">
                <div class="artist-card">
                    <div class="artist-avatar">
                        <i class="fas fa-user-astronaut"></i>
                    </div>
                    <div class="artist-info">
                        <div class="artist-label">Художник недели</div>
                        <h2 class="artist-name">{{ $featuredArtist->name }}</h2>
                        <p class="artist-bio">
                            {{ $featuredArtist->bio ?? 'Талантливый художник с уникальным стилем и видением.' }}
                        </p>
                        <div class="artist-meta">
                            <span>{{ $featuredArtist->artworks_count }} работ в галерее</span>
                        </div>
                        <div class="artist-link">
                            <a href="/gallery" class="link-inline">
                                Смотреть портфолио <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section id="featured" class="featured-section">
            <div class="section-header">
                <div class="section-header-left">
                    <h2 class="section-title-main">Избранные работы</h2>
                    <p class="section-sub">Самые популярные произведения в нашей галерее</p>
                </div>
                <a href="/gallery" class="link-inline">
                    Смотреть все работы <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            @if($featuredArtworks->count() > 0)
                <div class="gallery-grid">
                    @foreach($featuredArtworks as $artwork)
                        @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                    @endforeach
                </div>
            @else
                <div class="empty-block">
                    <i class="fas fa-palette empty-icon"></i>
                    <p>Пока нет избранных работ.</p>
                </div>
            @endif
        </section>

        <section id="categories" class="categories-section">
            <div class="section-title-center">
                <h2>Категории</h2>
                <p>Исследуйте нашу коллекцию по категориям</p>
            </div>

            <div class="categories-grid">
                @foreach($categories as $category)
                    <a href="/gallery/category/{{ $category->id }}" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="category-info">
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <p class="category-sub">Категория</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="recent-section">
            <div class="section-header">
                <div class="section-header-left">
                    <h2 class="section-title-main">Последние поступления</h2>
                    <p class="section-sub">Самые свежие работы в нашей коллекции</p>
                </div>
                <a href="/gallery" class="link-inline">
                    Смотреть все работы <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            @if($recentArtworks->count() > 0)
                <div class="gallery-grid">
                    @foreach($recentArtworks as $artwork)
                        @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                    @endforeach
                </div>
            @else
                <div class="empty-block">
                    <i class="fas fa-palette empty-icon"></i>
                    <p>Пока нет недавно добавленных работ.</p>
                </div>
            @endif
        </section>

        @if($popularArtworks->count() > 0)
            <section class="popular-section">
                <div class="section-header">
                    <div class="section-header-left">
                        <h2 class="section-title-main">Популярные работы</h2>
                        <p class="section-sub">Самые популярные работы по количеству лайков</p>
                    </div>
                    <a href="{{ route('gallery.all', ['sort' => 'popular']) }}" class="link-inline">
                        Смотреть все <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="gallery-grid">
                    @foreach($popularArtworks as $artwork)
                        @include('gallery.partials.artwork-card', ['artwork' => $artwork])
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <div class="cta-section">
        <div class="container text-center">
            <h2 class="cta-title">Хотите выставить свои работы?</h2>
            <p class="cta-sub">Присоединяйтесь к нашему сообществу художников и демонстрируйте свои произведения тысячам ценителей искусства</p>
            @guest
                <div class="cta-buttons">
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Зарегистрироваться
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Войти в аккаунт
                    </a>
                </div>
            @else
                <a href="{{ route('admin.dashboard') }}" class="btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Панель управления
                </a>
            @endguest
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/gallery.js') }}"></script>
    @endpush
@endsection
