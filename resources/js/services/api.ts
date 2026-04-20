import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const hadToken = Boolean(localStorage.getItem('auth_token'));
            const isAppRoute = window.location.pathname.startsWith('/app');

            if (hadToken) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_user');
            }

            if (
                hadToken &&
                isAppRoute &&
                window.location.pathname !== '/app/login'
            ) {
                window.location.href = '/app/login';
            }
        }

        return Promise.reject(error);
    },
);

export default api;
