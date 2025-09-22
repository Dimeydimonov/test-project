
class ImageManager {
    constructor(artworkId) {
        this.artworkId = artworkId;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initSortable();
    }

    setupEventListeners() {
        
        const uploadBtn = document.getElementById('upload-images-btn');
        const fileInput = document.getElementById('images-input');
        
        if (uploadBtn && fileInput) {
            uploadBtn.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', (e) => this.handleFileUpload(e));
        }

        
        const dropZone = document.getElementById('images-drop-zone');
        if (dropZone) {
            dropZone.addEventListener('dragover', this.handleDragOver.bind(this));
            dropZone.addEventListener('drop', this.handleDrop.bind(this));
        }

        
        document.addEventListener('click', (e) => {
            if (e.target.matches('.delete-image-btn')) {
                this.deleteImage(e.target.dataset.imageId);
            }
            if (e.target.matches('.set-primary-btn')) {
                this.setPrimaryImage(e.target.dataset.imageId);
            }
        });
    }

    initSortable() {
        const imagesList = document.getElementById('images-list');
        if (imagesList && typeof Sortable !== 'undefined') {
            new Sortable(imagesList, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: (evt) => {
                    this.updateImagesOrder();
                }
            });
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.add('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files);
        this.uploadFiles(files);
    }

    handleFileUpload(e) {
        const files = Array.from(e.target.files);
        this.uploadFiles(files);
        e.target.value = ''; 
    }

    async uploadFiles(files) {
        if (!files.length) return;

        
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        
        if (imageFiles.length === 0) {
            this.showNotification('Выберите файлы изображений', 'error');
            return;
        }

        if (imageFiles.length > 10) {
            this.showNotification('Можно загрузить максимум 10 изображений за раз', 'error');
            return;
        }

        const formData = new FormData();
        imageFiles.forEach(file => {
            formData.append('images[]', file);
        });

        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }

        try {
            this.showLoading(true);
            
            const response = await fetch(`/admin/artworks/${this.artworkId}/images`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.refreshImagesList();
            } else {
                this.showNotification(data.errors ? data.errors.join(', ') : 'Ошибка загрузки', 'error');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification('Ошибка при загрузке изображений', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    async deleteImage(imageId) {
        if (!confirm('Вы уверены, что хотите удалить это изображение?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                document.querySelector(`[data-image-id="${imageId}"]`).closest('.image-item').remove();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showNotification('Ошибка при удалении изображения', 'error');
        }
    }

    async setPrimaryImage(imageId) {
        try {
            const response = await fetch(`/admin/artworks/${this.artworkId}/images/primary`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ image_id: imageId })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updatePrimaryImageUI(imageId);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Set primary error:', error);
            this.showNotification('Ошибка при установке главного изображения', 'error');
        }
    }

    async updateImagesOrder() {
        const imageItems = document.querySelectorAll('#images-list .image-item');
        const imageIds = Array.from(imageItems).map(item => 
            parseInt(item.querySelector('[data-image-id]').dataset.imageId)
        );

        try {
            const response = await fetch(`/admin/artworks/${this.artworkId}/images/order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ images: imageIds })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Порядок изображений обновлен', 'success');
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Update order error:', error);
            this.showNotification('Ошибка при обновлении порядка', 'error');
        }
    }

    updatePrimaryImageUI(primaryImageId) {
        
        document.querySelectorAll('.image-item').forEach(item => {
            item.classList.remove('primary-image');
            const primaryBadge = item.querySelector('.primary-badge');
            if (primaryBadge) primaryBadge.remove();
        });

        
        const primaryItem = document.querySelector(`[data-image-id="${primaryImageId}"]`).closest('.image-item');
        primaryItem.classList.add('primary-image');
        
        
        const badge = document.createElement('span');
        badge.className = 'primary-badge badge bg-primary position-absolute top-0 start-0 m-2';
        badge.textContent = 'Главное';
        primaryItem.querySelector('.image-container').appendChild(badge);
    }

    refreshImagesList() {
        
        window.location.reload();
    }

    showLoading(show) {
        const loadingEl = document.getElementById('upload-loading');
        if (loadingEl) {
            loadingEl.style.display = show ? 'block' : 'none';
        }
    }

    showNotification(message, type = 'info') {
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const artworkIdElement = document.querySelector('[data-artwork-id]');
    if (artworkIdElement) {
        const artworkId = artworkIdElement.dataset.artworkId;
        new ImageManager(artworkId);
    }
});
