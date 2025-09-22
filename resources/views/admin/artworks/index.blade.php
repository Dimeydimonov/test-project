@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Управление произведениями</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.artworks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Добавить произведение
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.artworks.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Поиск</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Название, описание, автор...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Статус</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Все статусы</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                        Опубликовано
                    </option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                        Черновик
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Категория</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.artworks.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($artworks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Автор</th>
                            <th>Категории</th>
                            <th>Статистика</th>
                            <th>Статус</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($artworks as $artwork)
                            <tr>
                                <td>
                                    @if($artwork->image_path)
                                        <img src="{{ asset('storage/' . $artwork->image_path) }}" 
                                             alt="{{ $artwork->title }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $artwork->title }}</strong>
                                        @if($artwork->year)
                                            <small class="text-muted">({{ $artwork->year }})</small>
                                        @endif
                                    </div>
                                    @if($artwork->description)
                                        <small class="text-muted">
                                            {{ Str::limit($artwork->description, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $artwork->user->photo_url }}" 
                                             alt="{{ $artwork->user->name }}" 
                                             class="rounded-circle me-2" 
                                             width="24" height="24">
                                        {{ $artwork->user->name }}
                                    </div>
                                </td>
                                <td>
                                    @if($artwork->categories->count() > 0)
                                        @foreach($artwork->categories as $category)
                                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Без категории</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-heart text-danger"></i> {{ $artwork->likes_count }}
                                        <br>
                                        <i class="fas fa-comment text-info"></i> {{ $artwork->comments_count }}
                                        <br>
                                        <i class="fas fa-eye text-secondary"></i> {{ $artwork->views ?? 0 }}
                                    </small>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-toggle-status {{ $artwork->is_available ? 'btn-success' : 'btn-secondary' }}"
                                            data-artwork-id="{{ $artwork->id }}"
                                            data-current-status="{{ $artwork->is_available ? 'true' : 'false' }}">
                                        <i class="fas fa-{{ $artwork->is_available ? 'check' : 'times' }}"></i>
                                        {{ $artwork->is_available ? 'Опубликовано' : 'Черновик' }}
                                    </button>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $artwork->created_at->format('d.m.Y') }}
                                        <br>
                                        {{ $artwork->created_at->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.artworks.show', $artwork) }}" 
                                           class="btn btn-sm btn-outline-info" title="Просмотр">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.artworks.edit', $artwork) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger btn-delete" 
                                                data-artwork-id="{{ $artwork->id }}"
                                                data-artwork-title="{{ $artwork->title }}"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Показано {{ $artworks->firstItem() }}-{{ $artworks->lastItem() }} из {{ $artworks->total() }} результатов
                </div>
                {{ $artworks->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Произведения не найдены</h5>
                <p class="text-muted">Попробуйте изменить параметры поиска или добавьте новое произведение.</p>
                <a href="{{ route('admin.artworks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Добавить произведение
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить произведение <strong id="artwork-title"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Это действие нельзя отменить!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.dataset.artworkId;
            const currentStatus = this.dataset.currentStatus === 'true';
            
            fetch(`/admin/artworks/${artworkId}/toggle-published`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newStatus = data.is_published;
                    this.dataset.currentStatus = newStatus.toString();
                    
                    if (newStatus) {
                        this.className = 'btn btn-sm btn-toggle-status btn-success';
                        this.innerHTML = '<i class="fas fa-check"></i> Опубликовано';
                    } else {
                        this.className = 'btn btn-sm btn-toggle-status btn-secondary';
                        this.innerHTML = '<i class="fas fa-times"></i> Черновик';
                    }
                    
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Произошла ошибка при изменении статуса', 'error');
            });
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const artworkId = this.dataset.artworkId;
            const artworkTitle = this.dataset.artworkTitle;
            
            document.getElementById('artwork-title').textContent = artworkTitle;
            document.getElementById('delete-form').action = `/admin/artworks/${artworkId}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    container.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endpush
