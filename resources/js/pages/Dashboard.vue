<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
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

const page = usePage();
const user = computed<AuthUser | null>(
    () => (page.props.auth?.user as AuthUser) ?? null,
);

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
