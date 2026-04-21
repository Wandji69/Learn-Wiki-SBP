<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { useAuthStore } from '@/stores/auth';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

onMounted(() => {
    const authStore = useAuthStore();

    if (!authStore.isAuthenticated) {
        router.visit('/login');
        return;
    }

    if (window.location.pathname.startsWith('/admin') && !authStore.isAdmin) {
        router.visit('/user/dashboard');
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
