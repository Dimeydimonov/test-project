

document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.like-button').forEach(button => {
        
        if (!button.classList.contains('relative')) {
            button.classList.add('relative');
        }
        
        
        button.addEventListener('click', handleLikeClick);
        
        
        if (!button.querySelector('.like-spinner')) {
            const spinner = document.createElement('span');
            spinner.className = 'like-spinner hidden absolute inset-0 flex items-center justify-center';
            spinner.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
            button.appendChild(spinner);
        }
    });
    
    
    document.querySelectorAll('[data-artwork-id]').forEach(artworkElement => {
        const artworkId = artworkElement.getAttribute('data-artwork-id');
        if (artworkId) {
            checkLikeStatus(artworkId);
        }
    });
});

function showLoading(button) {
    if (!button) return;
    
    button.disabled = true;
    const spinner = button.querySelector('.like-spinner');
    const icon = button.querySelector('i:not(.like-spinner i)');
    const likeCount = button.querySelector('.like-count');
    
    if (spinner) spinner.classList.remove('hidden');
    if (icon) icon.classList.add('opacity-0');
    if (likeCount) likeCount.classList.add('opacity-0');
}

function hideLoading(button) {
    if (!button) return;
    
    button.disabled = false;
    const spinner = button.querySelector('.like-spinner');
    const icon = button.querySelector('i:not(.like-spinner i)');
    const likeCount = button.querySelector('.like-count');
    
    if (spinner) spinner.classList.add('hidden');
    if (icon) icon.classList.remove('opacity-0');
    if (likeCount) likeCount.classList.remove('opacity-0');
}

function handleLikeClick(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    if (!button) return;
    
    const artworkId = button.getAttribute('data-artwork-id');
    if (!artworkId) {
        console.error('No artwork ID found on like button');
        return;
    }
    
    const isLiked = button.classList.contains('liked');
    
    
    showLoading(button);
    
    if (isLiked) {
        
        unlikeArtwork(artworkId, button);
    } else {
        
        likeArtwork(artworkId, button);
    }
}

async function likeArtwork(artworkId, button) {
    try {
        const response = await axios.post(`/api/artworks/${artworkId}/like`);
        
        if (response.data.status === 'success') {
            
            button.classList.add('liked');
            
            
            const icon = button.querySelector('i:not(.like-spinner i)');
            if (icon) {
                icon.className = 'fas fa-heart text-red-500';
            }
            
            
            updateLikeCount(artworkId, response.data.likes_count);
            
            
            showToast('Лайк добавлен', 'success');
        } else {
            showToast(response.data.message || 'Ошибка при добавлении лайка', 'error');
        }
    } catch (error) {
        console.error('Error liking artwork:', error);
        const errorMessage = error.response?.data?.message || 'Произошла ошибка';
        showToast(errorMessage, 'error');
    } finally {
        hideLoading(button);
    }
}

async function unlikeArtwork(artworkId, button) {
    try {
        const response = await axios.delete(`/api/artworks/${artworkId}/like`);
        
        if (response.data.status === 'success') {
            
            button.classList.remove('liked');
            
            
            const icon = button.querySelector('i:not(.like-spinner i)');
            if (icon) {
                icon.className = 'far fa-heart';
            }
            
            
            updateLikeCount(artworkId, response.data.likes_count);
            
            
            showToast('Лайк удален', 'info');
        } else {
            showToast(response.data.message || 'Ошибка при удалении лайка', 'error');
        }
    } catch (error) {
        console.error('Error unliking artwork:', error);
        const errorMessage = error.response?.data?.message || 'Произошла ошибка';
        showToast(errorMessage, 'error');
    } finally {
        hideLoading(button);
    }
}

async function checkLikeStatus(artworkId) {
    try {
        const response = await axios.get(`/api/artworks/${artworkId}/check-like`);
        
        if (response.data.status === 'success') {
            const buttons = document.querySelectorAll(`.like-button[data-artwork-id="${artworkId}"]`);
            
            buttons.forEach(button => {
                if (response.data.is_liked) {
                    button.classList.add('liked');
                    const icon = button.querySelector('i:not(.like-spinner i)');
                    if (icon) {
                        icon.className = 'fas fa-heart text-red-500';
                    }
                } else {
                    button.classList.remove('liked');
                    const icon = button.querySelector('i:not(.like-spinner i)');
                    if (icon) {
                        icon.className = 'far fa-heart';
                    }
                }
                
                
                updateLikeCount(artworkId, response.data.likes_count);
            });
        }
    } catch (error) {
        console.error('Error checking like status:', error);
    }
}

function updateLikeCount(artworkId, count) {
    
    const likeButton = document.querySelector(`.like-button[data-artwork-id="${artworkId}"]`);
    if (likeButton) {
        const countElement = likeButton.querySelector('.like-count');
        if (countElement) {
            countElement.textContent = count;
        }
    }
    
    
    document.querySelectorAll(`[data-artwork-id="${artworkId}"] .like-count`).forEach(element => {
        element.textContent = count;
    });
}

function showToast(message, type = 'info') {
    
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    
    const toast = document.createElement('div');
    toast.className = `px-4 py-2 rounded-md shadow-md text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    
    toastContainer.appendChild(toast);
    
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => {
            toast.remove();
            
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        }, 300);
    }, 3000);
}

window.likeArtwork = likeArtwork;
window.unlikeArtwork = unlikeArtwork;
window.checkLikeStatus = checkLikeStatus;
