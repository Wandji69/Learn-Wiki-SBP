<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import type { AuthUser } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

const authStore = useAuthStore();
const user = computed<AuthUser | null>(() => authStore.user);

onMounted(() => {
    const nextPath = user.value?.roles?.some((role) => role.name === 'admin')
        ? '/admin/dashboard'
        : '/user/dashboard';

    router.visit(nextPath, { replace: true });
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="p-6 text-sm text-zinc-500">
        Redirecting to your dashboard...
    </div>
</template>
