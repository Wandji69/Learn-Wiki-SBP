import { defineStore } from 'pinia';
import api from '@/services/api';
import type {
    AuthResponse,
    AuthUser,
    LoginPayload,
    RegisterPayload,
} from '@/types';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('auth_token') ?? '',
        user:
            (JSON.parse(
                localStorage.getItem('auth_user') ?? 'null',
            ) as AuthUser | null) ?? null,
    }),
    getters: {
        isAuthenticated: (state) => Boolean(state.token),
        isAdmin: (state) =>
            Boolean(state.user?.roles?.some((role) => role.name === 'admin')),
    },
    actions: {
        persist(auth: AuthResponse) {
            this.token = auth.token;
            this.user = auth.user;
            localStorage.setItem('auth_token', auth.token);
            localStorage.setItem('auth_user', JSON.stringify(auth.user));
        },
        async login(payload: LoginPayload) {
            const { data } = await api.post<AuthResponse>(
                '/auth/login',
                payload,
            );
            this.persist(data);

            return data;
        },
        async register(payload: RegisterPayload) {
            const { data } = await api.post<AuthResponse>(
                '/auth/register',
                payload,
            );
            this.persist(data);

            return data;
        },
        async me() {
            const { data } = await api.get<{ user: AuthUser }>('/auth/me');
            this.user = data.user;
            localStorage.setItem('auth_user', JSON.stringify(data.user));

            return data.user;
        },
        async logout() {
            try {
                await api.post('/auth/logout');
            } finally {
                this.token = '';
                this.user = null;
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_user');
            }
        },
    },
});
