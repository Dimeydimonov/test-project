import axios from 'axios';
window.axios = axios;

// Set default headers for all Axios requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from the meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found. Please ensure the CSRF meta tag is present in your HTML.');
}

// Handle CSRF token mismatch errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // CSRF token mismatch - redirect to login or refresh the page
            console.log('CSRF token mismatch. Refreshing page...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
