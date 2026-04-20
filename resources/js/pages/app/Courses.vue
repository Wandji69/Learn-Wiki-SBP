<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';
import type { Course } from '@/types';

const authStore = useAuthStore();
const courses = ref<Course[]>([]);
const message = ref('');

const canCreateCourse = computed(() => authStore.isAdmin);
const newCourse = ref({ title: '', description: '', duration_minutes: 60 });

const ensureAuth = async () => {
    if (!authStore.isAuthenticated) {
        window.location.href = '/app/login';

        return;
    }

    try {
        await authStore.me();
    } catch {
        window.location.href = '/app/login';
    }
};

const loadCourses = async () => {
    const { data } = await api.get('/courses');
    courses.value = data.data;
};

const enroll = async (courseId: number) => {
    await api.post('/enroll', { course_id: courseId });
    message.value = 'Enrollment successful.';
};

const createCourse = async () => {
    await api.post('/courses', newCourse.value);
    message.value = 'Course created.';
    newCourse.value = { title: '', description: '', duration_minutes: 60 };
    await loadCourses();
};

const logout = async () => {
    await authStore.logout();
    window.location.href = '/app/login';
};

onMounted(async () => {
    await ensureAuth();
    await loadCourses();
});
</script>

<template>
    <Head title="Courses" />

    <v-container class="py-10">
        <div class="d-flex justify-space-between align-center mb-4">
            <h1>Courses</h1>
            <div>
                <a href="/app/appointments" class="mr-4">Appointments</a>
                <v-btn color="secondary" @click="logout">Logout</v-btn>
            </div>
        </div>

        <v-alert v-if="message" type="success" class="mb-4">{{
            message
        }}</v-alert>

        <v-card v-if="canCreateCourse" class="mb-6">
            <v-card-title>Create Course (Admin)</v-card-title>
            <v-card-text>
                <v-form @submit.prevent="createCourse">
                    <v-text-field
                        v-model="newCourse.title"
                        label="Title"
                        required
                    />
                    <v-textarea
                        v-model="newCourse.description"
                        label="Description"
                    />
                    <v-text-field
                        v-model.number="newCourse.duration_minutes"
                        type="number"
                        min="1"
                        label="Duration (minutes)"
                    />
                    <v-btn type="submit" color="primary">Create Course</v-btn>
                </v-form>
            </v-card-text>
        </v-card>

        <v-row>
            <v-col
                v-for="course in courses"
                :key="course.id"
                cols="12"
                md="6"
                lg="4"
            >
                <v-card>
                    <v-card-title>{{ course.title }}</v-card-title>
                    <v-card-text>
                        <p>{{ course.description || 'No description' }}</p>
                        <p>
                            Duration:
                            {{ course.duration_minutes || 'N/A' }} minutes
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" @click="enroll(course.id)"
                            >Enroll</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
