<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const error = ref('');
const loading = ref(false);

const form = reactive({
    email: '',
    password: '',
});

const submit = async () => {
    loading.value = true;
    error.value = '';

    try {
        await authStore.login(form);
        window.location.href = '/app/courses';
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Login failed.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="API Login" />

    <v-container class="py-10" style="max-width: 520px">
        <v-card>
            <v-card-title>Login</v-card-title>
            <v-card-text>
                <v-alert v-if="error" type="error" class="mb-4">{{
                    error
                }}</v-alert>
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.email"
                        label="Email"
                        type="email"
                        required
                    />
                    <v-text-field
                        v-model="form.password"
                        label="Password"
                        type="password"
                        required
                    />
                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="loading"
                        block
                        >Sign in</v-btn
                    >
                </v-form>
                <p class="mt-4 text-sm">
                    No account? <a href="/app/register">Register</a>
                </p>
            </v-card-text>
        </v-card>
    </v-container>
</template>
