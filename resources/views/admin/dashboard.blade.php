@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Панель управления</h1>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Всего произведений
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_artworks'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-images fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Опубликованных
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published_artworks'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Пользователей
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Комментариев
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_comments'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Последние произведения</h6>
            </div>
            <div class="card-body">
                @if($recentArtworks->count() > 0)
                    @foreach($recentArtworks as $artwork)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                @if($artwork->image_path)
                                    <img src="{{ asset('storage/' . $artwork->image_path) }}" 
                                         alt="{{ $artwork->title }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $artwork->title }}</h6>
                                <small class="text-muted">
                                    {{ $artwork->user->name }} • {{ $artwork->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-{{ $artwork->is_available ? 'success' : 'secondary' }}">
                                    {{ $artwork->is_available ? 'Опубликовано' : 'Черновик' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Нет произведений для отображения.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Популярные произведения</h6>
            </div>
            <div class="card-body">
                @if($popularArtworks->count() > 0)
                    @foreach($popularArtworks as $artwork)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                @if($artwork->image_path)
                                    <img src="{{ asset('storage/' . $artwork->image_path) }}" 
                                         alt="{{ $artwork->title }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $artwork->title }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-heart text-danger"></i> {{ $artwork->likes_count }}
                                    <i class="fas fa-eye text-info ms-2"></i> {{ $artwork->views }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Нет произведений для отображения.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Быстрые действия</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="/admin/artworks/create" class="btn btn-primary btn-block">
                            <i class="fas fa-plus me-2"></i>
                            Добавить произведение
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/users" class="btn btn-info btn-block">
                            <i class="fas fa-users me-2"></i>
                            Управление пользователями
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/artworks" class="btn btn-secondary btn-block">
                            <i class="fas fa-list me-2"></i>
                            Все произведения
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/analytics" class="btn btn-warning btn-block">
                            <i class="fas fa-chart-bar me-2"></i>
                            Аналитика
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
