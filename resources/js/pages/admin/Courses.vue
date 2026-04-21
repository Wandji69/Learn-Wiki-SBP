<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, getCurrentInstance, nextTick, onMounted, ref, watch } from 'vue';
import api from '@/services/api';
import type { Course } from '@/types';

const courses = ref<Course[]>([]);
const appointmentCount = ref(0);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const isLoadingCourses = ref(false);
const isCreatingCourse = ref(false);

const newCourse = ref({ title: '', description: '', duration_minutes: 60 });
const showCreateDialog = ref(false);
const expandedCourseIds = ref<number[]>([]);
const debugRunId = 'courses-initial';

const totalCourses = computed(() => courses.value.length);
const totalDuration = computed(() =>
    courses.value.reduce((sum, course) => sum + (course.duration_minutes ?? 0), 0),
);
const totalEnrolledStudents = computed(() =>
    courses.value.reduce(
        (sum, course) => sum + (course.enrolled_students_count ?? 0),
        0,
    ),
);

const toggleCourseContent = (courseId: number) => {
    if (expandedCourseIds.value.includes(courseId)) {
        expandedCourseIds.value = expandedCourseIds.value.filter(
            (id) => id !== courseId,
        );

        return;
    }

    expandedCourseIds.value.push(courseId);
};

const isCourseExpanded = (courseId: number) =>
    expandedCourseIds.value.includes(courseId);

const countLessons = (course: Course) =>
    (course.modules ?? []).reduce(
        (sum, module) => sum + module.lessons.length,
        0,
    );

const countTopics = (course: Course) =>
    (course.modules ?? []).reduce(
        (sum, module) =>
            sum +
            module.lessons.reduce(
                (lessonSum, lesson) => lessonSum + lesson.topics.length,
                0,
            ),
        0,
    );

const countContents = (course: Course) =>
    (course.modules ?? []).reduce(
        (sum, module) =>
            sum +
            module.lessons.reduce(
                (lessonSum, lesson) =>
                    lessonSum +
                    lesson.topics.reduce(
                        (topicSum, topic) => topicSum + topic.contents.length,
                        0,
                    ),
                0,
            ),
        0,
    );

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

const loadAppointmentsCount = async () => {
    try {
        const { data } = await api.get('/appointments');
        appointmentCount.value = Array.isArray(data.data) ? data.data.length : 0;
    } catch {
        appointmentCount.value = 0;
    }
};

const createCourse = async () => {
    isCreatingCourse.value = true;

    try {
        await api.post('/courses', newCourse.value);
        messageType.value = 'success';
        message.value = 'Course created.';
        newCourse.value = { title: '', description: '', duration_minutes: 60 };
        showCreateDialog.value = false;
        await loadCourses();
    } catch {
        messageType.value = 'error';
        message.value = 'Unable to create course.';
    } finally {
        isCreatingCourse.value = false;
    }
};

const openCreateDialog = () => {
    showCreateDialog.value = true;
};


watch(showCreateDialog, async (value) => {
    if (!value) {
        return;
    }
    await nextTick();
});

onMounted(async () => {
    await Promise.all([loadCourses(), loadAppointmentsCount()]);
});
</script>

<template>
    <Head title="Admin Courses" />

    <div class="mx-auto max-w-7xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Course Management</h1>
                <p class="text-sm text-zinc-500">
                    Create and monitor learning content.
                </p>
            </div>
            <button
                data-debug-create-course-btn
                type="button"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                @click="openCreateDialog"
            >
                Create Course
            </button>
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

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div
                    class="text-xs font-semibold tracking-wider text-zinc-500 uppercase"
                >
                    Total Courses
                </div>
                <div class="mt-2 text-3xl font-bold">{{ totalCourses }}</div>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div
                    class="text-xs font-semibold tracking-wider text-zinc-500 uppercase"
                >
                    Total Duration
                </div>
                <div class="mt-2 text-3xl font-bold">
                    {{ totalDuration }}
                    <span class="text-base font-medium">mins</span>
                </div>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div
                    class="text-xs font-semibold tracking-wider text-zinc-500 uppercase"
                >
                    Total Enrolled Students
                </div>
                <div class="mt-2 text-3xl font-bold">
                    {{ totalEnrolledStudents }}
                </div>
            </div>
        </div>

        <!-- <div
            class="rounded-xl border border-zinc-200 bg-white p-4 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <span class="font-semibold">Platform snapshot:</span>
            {{ appointmentCount }} appointments booked across the platform.
        </div> -->

        <div
            class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <h2 class="mb-4 text-lg font-semibold">Published Courses</h2>
            <div
                v-if="courses.length === 0"
                class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-zinc-500 dark:border-zinc-700"
            >
                <span v-if="isLoadingCourses">Loading courses...</span>
                <span v-else>No courses yet.</span>
            </div>
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950"
                >
                    <h3 class="text-base font-bold">{{ course.title }}</h3>
                    <p
                        class="mt-2 line-clamp-3 text-sm text-zinc-600 dark:text-zinc-300"
                    >
                        {{ course.description || 'No description' }}
                    </p>
                    <div class="mt-3 text-xs font-semibold text-zinc-500 uppercase">
                        {{ course.duration_minutes || 'N/A' }} minutes
                    </div>
                    <div class="mt-2 text-xs font-semibold text-zinc-500 uppercase">
                        {{ course.enrolled_students_count ?? 0 }} enrolled students
                    </div>
                    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-300">
                        {{ course.modules?.length ?? 0 }} modules,
                        {{ countLessons(course) }} lessons,
                        {{ countTopics(course) }} topics,
                        {{ countContents(course) }} contents
                    </div>

                    <div class="mt-3 flex gap-2">
                        <button
                            type="button"
                            class="rounded-md border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                            @click="toggleCourseContent(course.id)"
                        >
                            {{
                                isCourseExpanded(course.id)
                                    ? 'Hide Content'
                                    : 'View Course Content'
                            }}
                        </button>
                    </div>

                    <div
                        v-if="isCourseExpanded(course.id)"
                        class="mt-4 space-y-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900"
                    >
                        <div
                            v-if="!course.modules || course.modules.length === 0"
                            class="text-sm text-zinc-500"
                        >
                            No module structure added yet.
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="module in course.modules"
                                :key="module.id"
                                class="rounded border border-zinc-200 p-3 dark:border-zinc-700"
                            >
                                <p class="text-sm font-semibold">
                                    Module {{ module.order ?? '-' }}: {{ module.title }}
                                </p>
                                <div
                                    v-if="module.lessons.length === 0"
                                    class="mt-1 text-xs text-zinc-500"
                                >
                                    No lessons.
                                </div>
                                <div v-else class="mt-2 space-y-2">
                                    <div
                                        v-for="lesson in module.lessons"
                                        :key="lesson.id"
                                        class="rounded border border-dashed border-zinc-300 p-2 text-sm dark:border-zinc-600"
                                    >
                                        <p class="font-medium">
                                            Lesson {{ lesson.order ?? '-' }}:
                                            {{ lesson.title }}
                                        </p>
                                        <div
                                            v-if="lesson.topics.length === 0"
                                            class="mt-1 text-xs text-zinc-500"
                                        >
                                            No topics.
                                        </div>
                                        <div v-else class="mt-1 space-y-1">
                                            <div
                                                v-for="topic in lesson.topics"
                                                :key="topic.id"
                                                class="rounded bg-zinc-100 p-2 text-xs dark:bg-zinc-800"
                                            >
                                                <p class="font-semibold">
                                                    Topic {{ topic.order ?? '-' }}:
                                                    {{ topic.title }}
                                                </p>
                                                <ul class="mt-1 list-disc pl-4">
                                                    <li
                                                        v-for="content in topic.contents"
                                                        :key="content.id"
                                                        class="text-zinc-700 dark:text-zinc-300"
                                                    >
                                                        <span class="font-medium uppercase">
                                                            {{ content.type }}:
                                                        </span>
                                                        {{ content.body }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showCreateDialog"
            data-debug-course-modal
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div
                class="w-full max-w-2xl rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
            >
                <h2 data-debug-course-modal-title class="text-lg font-bold">Create New Course</h2>
                <form @submit.prevent="createCourse" class="mt-4 space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Title
                        </label>
                        <input
                            v-model="newCourse.title"
                            type="text"
                            required
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Description
                        </label>
                        <textarea
                            v-model="newCourse.description"
                            rows="4"
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Duration (minutes)
                        </label>
                        <input
                            v-model.number="newCourse.duration_minutes"
                            type="number"
                            min="1"
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            :disabled="isCreatingCourse"
                            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                            @click="showCreateDialog = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="isCreatingCourse"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ isCreatingCourse ? 'Creating...' : 'Create Course' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
