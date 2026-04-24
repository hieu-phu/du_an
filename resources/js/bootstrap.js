import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

export const syncCsrfToken = (csrfToken = null) => {
    const token = csrfToken || document.head.querySelector('meta[name="csrf-token"]')?.content;

    if (!token) {
        console.error('CSRF token not found');
        return;
    }

    let meta = document.head.querySelector('meta[name="csrf-token"]');

    if (!meta) {
        meta = document.createElement('meta');
        meta.setAttribute('name', 'csrf-token');
        document.head.appendChild(meta);
    }

    meta.setAttribute('content', token);
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
};

syncCsrfToken();

window.axios = axios;
