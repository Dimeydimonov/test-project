/**
 * Handle like/unlike functionality for artworks
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize like buttons
    document.querySelectorAll('.like-button').forEach(button => {
        // Add loading class if not already present
        if (!button.classList.contains('relative')) {
            button.classList.add('relative');
        }
        
        // Add click event listener
        button.addEventListener('click', handleLikeClick);
        
        // Add loading indicator if not already present
        if (!button.querySelector('.like-spinner')) {
            const spinner = document.createElement('span');
            spinner.className = 'like-spinner hidden absolute inset-0 flex items-center justify-center';
            spinner.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
            button.appendChild(spinner);
        }
    });
    
    // Check initial like status for all artworks on the page
    document.querySelectorAll('[data-artwork-id]').forEach(artworkElement => {
        const artworkId = artworkElement.getAttribute('data-artwork-id');
        if (artworkId) {
            checkLikeStatus(artworkId);
        }
    });
});

/**
 * Show a loading state on the like button
 */
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

/**
 * Hide the loading state on the like button
 */
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

/**
 * Handle click on like button
 */
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
    
    // Show loading state
    showLoading(button);
    
    if (isLiked) {
        // Unlike the artwork
        unlikeArtwork(artworkId, button);
    } else {
        // Like the artwork
        likeArtwork(artworkId, button);
    }
}

/**
 * Like an artwork
 */
async function likeArtwork(artworkId, button) {
    try {
        const response = await axios.post(`/api/artworks/${artworkId}/like`);
        
        if (response.data.status === 'success') {
            // Update button state
            button.classList.add('liked');
            
            // Update icon
            const icon = button.querySelector('i:not(.like-spinner i)');
            if (icon) {
                icon.className = 'fas fa-heart text-red-500';
            }
            
            // Update like count
            updateLikeCount(artworkId, response.data.likes_count);
            
            // Show success message
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

/**
 * Unlike an artwork
 */
async function unlikeArtwork(artworkId, button) {
    try {
        const response = await axios.delete(`/api/artworks/${artworkId}/like`);
        
        if (response.data.status === 'success') {
            // Update button state
            button.classList.remove('liked');
            
            // Update icon
            const icon = button.querySelector('i:not(.like-spinner i)');
            if (icon) {
                icon.className = 'far fa-heart';
            }
            
            // Update like count
            updateLikeCount(artworkId, response.data.likes_count);
            
            // Show success message
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

/**
 * Check if the current user has liked an artwork
 */
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
                
                // Update like count
                updateLikeCount(artworkId, response.data.likes_count);
            });
        }
    } catch (error) {
        console.error('Error checking like status:', error);
    }
}

/**
 * Update the like count display for an artwork
 */
function updateLikeCount(artworkId, count) {
    // Update like count in the main like button
    const likeButton = document.querySelector(`.like-button[data-artwork-id="${artworkId}"]`);
    if (likeButton) {
        const countElement = likeButton.querySelector('.like-count');
        if (countElement) {
            countElement.textContent = count;
        }
    }
    
    // Update like count in other places (e.g., artwork card)
    document.querySelectorAll(`[data-artwork-id="${artworkId}"] .like-count`).forEach(element => {
        element.textContent = count;
    });
}

/**
 * Show a toast notification
 */
function showToast(message, type = 'info') {
    // Check if toast container exists, if not create it
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `px-4 py-2 rounded-md shadow-md text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Auto-remove toast after delay
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        }, 300);
    }, 3000);
}

// Make functions available globally
window.likeArtwork = likeArtwork;
window.unlikeArtwork = unlikeArtwork;
window.checkLikeStatus = checkLikeStatus;
