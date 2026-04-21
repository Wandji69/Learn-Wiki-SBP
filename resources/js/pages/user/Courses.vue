<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import api from '@/services/api';
import type { Course } from '@/types';

const courses = ref<Course[]>([]);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const isLoadingCourses = ref(false);
const isEnrolling = ref<Record<number, boolean>>({});

const loadCourses = async () => {
    isLoadingCourses.value = true;

    try {
        const { data } = await api.get('/courses');
        courses.value = data.data;
    } catch {
        messageType.value = 'error';
        message.value = 'Unable to load courses right now.';
    } finally {
        isLoadingCourses.value = false;
    }
};

const enroll = async (courseId: number) => {
    isEnrolling.value[courseId] = true;

    try {
        await api.post('/enroll', { course_id: courseId });
        messageType.value = 'success';
        message.value = 'Enrollment successful.';
        // Optionally reload to update enroll count
        await loadCourses();
    } catch (e: any) {
        messageType.value = 'error';

        if (e.response?.status === 409) {
            message.value = 'You are already enrolled in this course.';
        } else {
            message.value = 'Unable to enroll.';
        }
    } finally {
        isEnrolling.value[courseId] = false;
    }
};

onMounted(async () => {
    await loadCourses();
});
</script>

<template>
    <Head title="Available Courses" />

    <div class="mx-auto max-w-7xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Courses Catalog</h1>
                <p class="text-sm text-zinc-500">
                    Browse and enroll in available learning paths.
                </p>
            </div>
        </div>

        <div
            v-if="message"
            class="mb-2 rounded-md border px-4 py-3 text-sm"
            :class="
                messageType === 'success'
                    ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                    : 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-950 dark:text-red-300'
            "
        >
            {{ message }}
        </div>

        <div
            class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <h2 class="mb-4 text-lg font-semibold">Available Courses</h2>
            <div
                v-if="courses.length === 0"
                class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-zinc-500 dark:border-zinc-700"
            >
                <span v-if="isLoadingCourses">Loading courses...</span>
                <span v-else>No courses available.</span>
            </div>
            <div
                v-else
                class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="flex flex-col justify-between rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950"
                >
                    <div>
                        <h3 class="text-base font-bold">{{ course.title }}</h3>
                        <p
                            class="mt-2 line-clamp-3 text-sm text-zinc-600 dark:text-zinc-300"
                        >
                            {{ course.description || 'No description' }}
                        </p>
                        <div
                            class="mt-3 text-xs font-semibold text-zinc-500 uppercase"
                        >
                            {{ course.duration_minutes || 'N/A' }} minutes
                        </div>
                    </div>
                    <div
                        class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-800"
                    >
                        <button
                            type="button"
                            @click="enroll(course.id)"
                            :disabled="isEnrolling[course.id]"
                            class="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{
                                isEnrolling[course.id]
                                    ? 'Enrolling...'
                                    : 'Enroll Now'
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
