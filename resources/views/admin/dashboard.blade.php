@extends('layouts.admin')

@section('content')
    <div class="admin-header">
        <h1>Панель управления</h1>
    </div>

    <div class="admin-stats">
        <div class="stat-card primary">
            <div class="stat-info">
                <div class="stat-label">Всего произведений</div>
                <div class="stat-value">{{ $stats['total_artworks'] ?? 0 }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-images"></i></div>
        </div>
        <div class="stat-card success">
            <div class="stat-info">
                <div class="stat-label">Опубликованных</div>
                <div class="stat-value">{{ $stats['published_artworks'] ?? 0 }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="stat-card info">
            <div class="stat-info">
                <div class="stat-label">Пользователей</div>
                <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-card warning">
            <div class="stat-info">
                <div class="stat-label">Комментарии</div>
                <div class="stat-value">{{ $stats['total_comments'] ?? 0 }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-comments"></i></div>
        </div>
    </div>

    <div class="admin-artworks">
        <div class="artwork-section">
            <h2>Последние произведения</h2>
            @if($recentArtworks->count())
                @foreach($recentArtworks as $artwork)
                    <div class="artwork-item">
                        <div class="artwork-thumb">
                            @if($artwork->image_path)
                                <img src="{{ asset('storage/' . $artwork->image_path) }}" alt="{{ $artwork->title }}">
                            @else
                                <div class="placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="artwork-info">
                            <h3>{{ $artwork->title }}</h3>
                            <small>{{ $artwork->user->name }} • {{ $artwork->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="artwork-status {{ $artwork->is_available ? 'published' : 'draft' }}">
                            {{ $artwork->is_available ? 'Опубликовано' : 'Черновик' }}
                        </div>
                    </div>
                @endforeach
            @else
                <p>Нет произведений для отображения.</p>
            @endif
        </div>

        <div class="artwork-section">
            <h2>Популярные произведения</h2>
            @if($popularArtworks->count())
                @foreach($popularArtworks as $artwork)
                    <div class="artwork-item">
                        <div class="artwork-thumb">
                            @if($artwork->image_path)
                                <img src="{{ asset('storage/' . $artwork->image_path) }}" alt="{{ $artwork->title }}">
                            @else
                                <div class="placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="artwork-info">
                            <h3>{{ $artwork->title }}</h3>
                            <small>
                                <i class="fas fa-heart text-danger"></i> {{ $artwork->likes_count }}
                                <i class="fas fa-eye text-info"></i> {{ $artwork->views }}
                            </small>
                        </div>
                    </div>
                @endforeach
            @else
                <p>Нет произведений для отображения.</p>
            @endif
        </div>
    </div>

    <div class="admin-actions">
        <a href="/admin/artworks/create" class="btn primary"><i class="fas fa-plus"></i> Добавить произведение</a>
        <a href="/admin/users" class="btn info"><i class="fas fa-users"></i> Управление пользователями</a>
        <a href="/admin/artworks" class="btn secondary"><i class="fas fa-list"></i> Все произведения</a>
        <a href="/admin/analytics" class="btn warning"><i class="fas fa-chart-bar"></i> Аналитика</a>
    </div>
@endsection
