<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';
import type { Appointment } from '@/types';

const authStore = useAuthStore();
const appointments = ref<Appointment[]>([]);
const message = ref('');

const form = reactive({
    practitioner_name: 'Dr. Smith',
    appointment_at: '',
    notes: '',
});

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

const loadAppointments = async () => {
    const { data } = await api.get('/appointments');
    appointments.value = data.data;
};

const book = async () => {
    await api.post('/appointments', form);
    message.value = 'Appointment booked.';
    form.notes = '';
    await loadAppointments();
};

const logout = async () => {
    await authStore.logout();
    window.location.href = '/app/login';
};

onMounted(async () => {
    await ensureAuth();
    await loadAppointments();
});
</script>

<template>
    <Head title="Appointments" />

    <v-container class="py-10">
        <div class="d-flex justify-space-between align-center mb-4">
            <h1>Appointments</h1>
            <div>
                <a href="/app/courses" class="mr-4">Courses</a>
                <v-btn color="secondary" @click="logout">Logout</v-btn>
            </div>
        </div>

        <v-alert v-if="message" type="success" class="mb-4">{{
            message
        }}</v-alert>

        <v-card class="mb-6">
            <v-card-title>Book Appointment</v-card-title>
            <v-card-text>
                <v-form @submit.prevent="book">
                    <v-text-field
                        v-model="form.practitioner_name"
                        label="Practitioner"
                        required
                    />
                    <v-text-field
                        v-model="form.appointment_at"
                        type="datetime-local"
                        label="Date & Time"
                        required
                    />
                    <v-textarea v-model="form.notes" label="Notes" />
                    <v-btn type="submit" color="primary">Book</v-btn>
                </v-form>
            </v-card-text>
        </v-card>

        <v-table>
            <thead>
                <tr>
                    <th>Practitioner</th>
                    <th>Date/Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="appointment in appointments" :key="appointment.id">
                    <td>{{ appointment.practitioner_name }}</td>
                    <td>{{ appointment.appointment_at }}</td>
                    <td>{{ appointment.status }}</td>
                    <td>{{ appointment.notes }}</td>
                </tr>
            </tbody>
        </v-table>
    </v-container>
</template>
