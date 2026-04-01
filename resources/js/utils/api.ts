import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios';

// Create Base Axios Instance
const api: AxiosInstance = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

// Request Interceptor
api.interceptors.request.use((config) => {
    // You can add auth tokens here if needed (e.g. Sanctum manages this via cookies automatically)
    return config;
}, (error: AxiosError) => {
    return Promise.reject(error);
});

// Response Interceptor
api.interceptors.response.use((response: AxiosResponse) => {
    return response.data; // Return main data to simplify frontend calls
}, (error: AxiosError) => {
    // Handle global errors directly
    if (error.response) {
        const { status } = error.response;
        if (status === 401) {
            // e.g. reload or redirect to login
            window.location.href = '/login';
        } else if (status === 403) {
            // Unauthorized
            console.error('Forbidden');
        } else if (status === 422) {
            // Validation errors
            console.error('Validation errors:', error.response.data);
        }
    }
    return Promise.reject(error);
});

export default api;
