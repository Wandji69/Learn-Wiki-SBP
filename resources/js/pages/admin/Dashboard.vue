<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Dashboard',
                href: '/admin/dashboard',
            },
        ],
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="p-6">
        <h1 class="mb-4 text-2xl font-bold">Admin Dashboard</h1>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900"
            >
                <h2 class="mb-2 text-lg font-semibold">Manage Courses</h2>
                <p class="text-zinc-500">Create and review learning content.</p>
                <Link
                    href="/admin/courses"
                    class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Open Courses
                </Link>
            </div>

            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900"
            >
                <h2 class="mb-2 text-lg font-semibold">Manage Appointments</h2>
                <p class="text-zinc-500">
                    Review and coordinate appointment bookings.
                </p>
                <Link
                    href="/admin/appointments"
                    class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                >
                    Open Appointments
                </Link>
            </div>
        </div>
    </div>
</template>
