<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import api from '@/services/api';
import type { Appointment } from '@/types';

const appointments = ref<Appointment[]>([]);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const isLoadingAppointments = ref(false);
const isBooking = ref(false);

const form = reactive({
    practitioner_name: 'Dr. Smith',
    appointment_at: '',
    notes: '',
});

const loadAppointments = async () => {
    isLoadingAppointments.value = true;

    try {
        const { data } = await api.get('/appointments');
        appointments.value = data.data;
    } catch {
        messageType.value = 'error';
        message.value = 'Unable to load appointments right now.';
    } finally {
        isLoadingAppointments.value = false;
    }
};

const book = async () => {
    isBooking.value = true;

    try {
        await api.post('/appointments', form);
        messageType.value = 'success';
        message.value = 'Appointment booked.';
        form.notes = '';
        form.appointment_at = '';
        await loadAppointments();
    } catch {
        messageType.value = 'error';
        message.value = 'Unable to book appointment.';
    } finally {
        isBooking.value = false;
    }
};

onMounted(async () => {
    await loadAppointments();
});
</script>

<template>
    <Head title="My Appointments" />

    <div class="mx-auto max-w-7xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">My Appointments</h1>
                <p class="text-sm text-zinc-500">
                    Book and view your upcoming appointments.
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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="md:col-span-1">
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <h2 class="mb-4 text-lg font-semibold">Book Appointment</h2>
                    <form @submit.prevent="book" class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200"
                            >
                                Practitioner
                            </label>
                            <input
                                v-model="form.practitioner_name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200"
                            >
                                Date & Time
                            </label>
                            <input
                                v-model="form.appointment_at"
                                type="datetime-local"
                                required
                                class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200"
                            >
                                Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="isBooking"
                            class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ isBooking ? 'Booking...' : 'Book Appointment' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <h2 class="mb-4 text-lg font-semibold">
                        Your Appointments
                    </h2>

                    <div
                        v-if="appointments.length === 0"
                        class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-zinc-500 dark:border-zinc-700"
                    >
                        <span v-if="isLoadingAppointments"
                            >Loading appointments...</span
                        >
                        <span v-else>You have no appointments.</span>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr
                                    class="border-b border-zinc-200 dark:border-zinc-700"
                                >
                                    <th class="pb-2">Practitioner</th>
                                    <th class="pb-2">Date/Time</th>
                                    <th class="pb-2">Status</th>
                                    <th class="pb-2">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="appointment in appointments"
                                    :key="appointment.id"
                                    class="border-b border-zinc-100 dark:border-zinc-800"
                                >
                                    <td class="py-3 font-medium">
                                        {{ appointment.practitioner_name }}
                                    </td>
                                    <td
                                        class="py-3 text-zinc-600 dark:text-zinc-300"
                                    >
                                        {{
                                            new Date(
                                                appointment.appointment_at,
                                            ).toLocaleString()
                                        }}
                                    </td>
                                    <td class="py-3">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="
                                                appointment.status === 'booked'
                                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
                                                    : appointment.status ===
                                                        'completed'
                                                      ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200'
                                                      : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
                                            "
                                        >
                                            {{ appointment.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="py-3 text-zinc-600 dark:text-zinc-300 max-w-[200px] truncate"
                                        :title="appointment.notes ?? undefined"
                                    >
                                        {{ appointment.notes || '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
