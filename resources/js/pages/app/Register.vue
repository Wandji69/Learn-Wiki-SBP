<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const error = ref('');
const loading = ref(false);

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = async () => {
    loading.value = true;
    error.value = '';

    try {
        await authStore.register(form);
        window.location.href = '/app/courses';
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'Registration failed.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="API Register" />

    <v-container class="py-10" style="max-width: 520px">
        <v-card>
            <v-card-title>Register</v-card-title>
            <v-card-text>
                <v-alert v-if="error" type="error" class="mb-4">{{
                    error
                }}</v-alert>
                <v-form @submit.prevent="submit">
                    <v-text-field v-model="form.name" label="Name" required />
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
                    <v-text-field
                        v-model="form.password_confirmation"
                        label="Confirm Password"
                        type="password"
                        required
                    />
                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="loading"
                        block
                        >Create account</v-btn
                    >
                </v-form>
                <p class="mt-4 text-sm">
                    Already have an account? <a href="/app/login">Login</a>
                </p>
            </v-card-text>
        </v-card>
    </v-container>
</template>
