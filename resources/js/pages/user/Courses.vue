<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import api from '@/services/api';
import type { Course } from '@/types';

const courses = ref<Course[]>([]);
const message = ref('');

const loadCourses = async () => {
    try {
        const { data } = await api.get('/courses');
        courses.value = data.data;
    } catch {
        message.value = 'Unable to load courses right now.';
    }
};

const enroll = async (courseId: number) => {
    await api.post('/enroll', { course_id: courseId });
    message.value = 'Enrollment successful.';
};

onMounted(async () => {
    await loadCourses();
});
</script>

<template>
    <Head title="Courses" />

    <v-container class="py-10">
        <div class="d-flex justify-space-between align-center mb-4">
            <h1>Courses</h1>
            <div>
                <a href="/user/appointments" class="mr-4">Appointments</a>
            </div>
        </div>

        <v-alert v-if="message" type="success" class="mb-4">{{
            message
        }}</v-alert>

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
