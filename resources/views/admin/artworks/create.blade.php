@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Добавить произведение</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.artworks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Назад к списку
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Пожалуйста, исправьте следующие ошибки:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="/admin/artworks" method="POST" enctype="multipart/form-data" id="artwork-form">
                    <input type="hidden" name="debug" value="1">
                    
                    <!-- Title -->
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">Название произведения <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control form-textarea @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="5" 
                                  placeholder="Опишите произведение искусства...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Изображения произведения</label>
                        <div class="upload-zone border border-2 border-dashed rounded p-4 text-center" id="image-upload-area">
                            <input type="file" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*" 
                                   class="form-control @error('images') is-invalid @enderror" 
                                   style="display: none;">
                            
                            <div class="upload-content">
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Загрузите изображения</h5>
                                    <p class="text-muted mb-3">
                                        Перетащите файлы сюда или нажмите для выбора файлов
                                    </p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <small class="text-muted">
                                            Поддерживаются: JPEG, PNG, GIF, WebP | Макс. размер: 10 МБ | До 10 файлов
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="preview-content" style="display: none;">
                                <div id="images-preview" class="row g-2"></div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="change-images">
                                        <i class="fas fa-edit me-1"></i>Изменить изображения
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('images')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="year" class="form-label">Год создания</label>
                                <input type="number" 
                                       class="form-control @error('year') is-invalid @enderror" 
                                       id="year" 
                                       name="year" 
                                       value="{{ old('year') }}" 
                                       min="1000" 
                                       max="{{ date('Y') + 1 }}">
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="width" class="form-label">Ширина (см)</label>
                                <input type="number" 
                                       class="form-control @error('width') is-invalid @enderror" 
                                       id="width" 
                                       name="width" 
                                       value="{{ old('width') }}" 
                                       min="0" 
                                       step="0.1"
                                       placeholder="50">
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="height" class="form-label">Высота (см)</label>
                                <input type="number" 
                                       class="form-control @error('height') is-invalid @enderror" 
                                       id="height" 
                                       name="height" 
                                       value="{{ old('height') }}" 
                                       min="0" 
                                       step="0.1"
                                       placeholder="70">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="price" class="form-label">Цена ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price') }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="1000.00">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="materials" class="form-label">Материалы и техника</label>
                        <input type="text" 
                               class="form-control @error('materials') is-invalid @enderror" 
                               id="materials" 
                               name="materials" 
                               value="{{ old('materials') }}" 
                               placeholder="например: холст, масло">
                        @error('materials')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Категории</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="categories[]" 
                                               value="{{ $category->id }}" 
                                               id="category-{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('categories')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" name="is_published" value="1" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>
                                Опубликовать
                            </button>
                            <button type="submit" name="is_published" value="0" class="btn btn-secondary">
                                <i class="fas fa-save me-2"></i>
                                Сохранить как черновик
                            </button>
                        </div>
                        <a href="{{ route('admin.artworks.index') }}" class="btn btn-outline-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Советы по добавлению произведений
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Качественные изображения</h6>
                    <p class="small text-muted">
                        Загружайте изображения высокого качества (минимум 800x600 пикселей) 
                        для лучшего отображения в галерее.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary">Подробное описание</h6>
                    <p class="small text-muted">
                        Добавьте подробное описание произведения, включая историю создания, 
                        вдохновение и особенности техники.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary">Правильные категории</h6>
                    <p class="small text-muted">
                        Выберите подходящие категории для лучшей организации 
                        и поиска произведений в галерее.
                    </p>
                </div>
                
                <div>
                    <h6 class="text-primary">Статус публикации</h6>
                    <p class="small text-muted">
                        Используйте "Черновик" для неготовых произведений 
                        и "Опубликовать" для готовых к показу работ.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imagesInput = document.getElementById('images');
    const uploadArea = document.getElementById('image-upload-area');
    const uploadContent = uploadArea.querySelector('.upload-content');
    const previewContent = uploadArea.querySelector('.preview-content');
    const imagesPreview = document.getElementById('images-preview');
    const changeImagesBtn = document.getElementById('change-images');

    uploadArea.addEventListener('click', function() {
        if (!previewContent.style.display || previewContent.style.display === 'none') {
            imagesInput.click();
        }
    });

    if (changeImagesBtn) {
        changeImagesBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imagesInput.click();
        });
    }

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFiles(files);
        }
    });

    imagesInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleImageFiles(e.target.files);
        }
    });

    function handleImageFiles(files) {
        if (files.length > 10) {
            alert('Можно загрузить максимум 10 изображений.');
            return;
        }

        imagesPreview.innerHTML = '';
        
        let validFiles = [];
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if (!file.type.startsWith('image/')) {
                alert(`Файл "${file.name}" не является изображением.`);
                continue;
            }

            if (file.size > 10 * 1024 * 1024) {
                alert(`Файл "${file.name}" превышает максимальный размер 10 МБ.`);
                continue;
            }
            
            validFiles.push(file);
        }
        
        if (validFiles.length === 0) {
            return;
        }
        
        const dataTransfer = new DataTransfer();
        validFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        imagesInput.files = dataTransfer.files;
        
        validFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                        ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Главное</span>' : ''}
                    </div>
                `;
                
                imagesPreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        
        uploadContent.style.display = 'none';
        previewContent.style.display = 'block';
    }

    document.getElementById('artwork-form').addEventListener('submit', function(e) {
        console.log('🚀 FORM SUBMIT EVENT TRIGGERED!');
        
        const title = document.getElementById('title').value.trim();
        console.log('📝 Title value:', title);
        
        const fileInput = document.getElementById('images');
        console.log('📁 File input files count:', fileInput.files.length);
        
        if (fileInput.files.length > 0) {
            console.log('📸 Files selected:', Array.from(fileInput.files).map(f => f.name));
        }
        
        console.log('✅ Form validation passed, submitting...');
        
        return true;
    });
});
</script>

<style>
.upload-zone {
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.05);
}

.upload-zone.drag-over {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.1);
    transform: scale(1.02);
}

.image-item {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: move;
}

.image-item:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-item.primary-image {
    border-color: #198754;
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
}

.image-container {
    position: relative;
    width: 100%;
    height: 150px;
    overflow: hidden;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-controls {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    gap: 5px;
}

.image-controls .btn {
    padding: 4px 8px;
    font-size: 12px;
    opacity: 0.9;
}

.primary-badge {
    font-size: 10px;
    padding: 2px 6px;
}

.sortable-ghost {
    opacity: 0.5;
}

#images-preview-container {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>

<div data-artwork-id="new" style="display: none;"></div>
@endpush
