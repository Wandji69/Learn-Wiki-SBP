<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, getCurrentInstance, nextTick, onMounted, reactive, ref, watch } from 'vue';
import api from '@/services/api';
import type { Appointment } from '@/types';

const appointments = ref<Appointment[]>([]);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const showCreateDialog = ref(false);
const isLoadingAppointments = ref(false);
const isCreatingAppointment = ref(false);
const debugRunId = 'appointments-initial';

const form = reactive({
    practitioner_name: 'Dr. Smith',
    appointment_at: '',
    notes: '',
});

const totalAppointments = computed(() => appointments.value.length);
const bookedAppointments = computed(
    () => appointments.value.filter((appointment) => appointment.status === 'booked').length,
);
const completedAppointments = computed(
    () => appointments.value.filter((appointment) => appointment.status === 'completed').length,
);
const cancelledAppointments = computed(
    () => appointments.value.filter((appointment) => appointment.status === 'cancelled').length,
);

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

const createAppointment = async () => {
    isCreatingAppointment.value = true;
    
    try {
        await api.post('/appointments', form);
        messageType.value = 'success';
        message.value = 'Appointment booked.';
        form.practitioner_name = 'Dr. Smith';
        form.appointment_at = '';
        form.notes = '';
        showCreateDialog.value = false;
        await loadAppointments();
    } catch {
        messageType.value = 'error';
        message.value = 'Unable to book appointment.';
    } finally {
        isCreatingAppointment.value = false;
    }
};

const openCreateDialog = () => {
    // #region agent log
    fetch('http://127.0.0.1:7695/ingest/2a848e96-f1b8-418a-8aec-1656ee1b9cbe', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': '86a769' }, body: JSON.stringify({ sessionId: '86a769', runId: debugRunId, hypothesisId: 'H1', location: 'resources/js/pages/admin/Appointments.vue:openCreateDialog', message: 'Create Appointment button handler fired', data: { before: showCreateDialog.value }, timestamp: Date.now() }) }).catch(() => {});
    // #endregion
    showCreateDialog.value = true;
};

watch(showCreateDialog, (value) => {
    // #region agent log
    fetch('http://127.0.0.1:7695/ingest/2a848e96-f1b8-418a-8aec-1656ee1b9cbe', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': '86a769' }, body: JSON.stringify({ sessionId: '86a769', runId: debugRunId, hypothesisId: 'H2', location: 'resources/js/pages/admin/Appointments.vue:watch(showCreateDialog)', message: 'showCreateDialog changed', data: { value }, timestamp: Date.now() }) }).catch(() => {});
    // #endregion
});

watch(showCreateDialog, async (value) => {
    if (!value) {
        return;
    }

    await nextTick();
    // #region agent log
    fetch('http://127.0.0.1:7695/ingest/2a848e96-f1b8-418a-8aec-1656ee1b9cbe', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': '86a769' }, body: JSON.stringify({ sessionId: '86a769', runId: debugRunId, hypothesisId: 'H4', location: 'resources/js/pages/admin/Appointments.vue:watch(showCreateDialog,nextTick)', message: 'Appointment dialog DOM rendered check', data: { dialogExists: Boolean(document.querySelector('[data-debug-appointment-dialog]')), titleExists: Boolean(document.querySelector('[data-debug-appointment-title]')), inputCount: document.querySelectorAll('[data-debug-appointment-dialog] input').length, textareaCount: document.querySelectorAll('[data-debug-appointment-dialog] textarea').length }, timestamp: Date.now() }) }).catch(() => {});
    // #endregion
});

onMounted(async () => {
    const instance = getCurrentInstance();
    // #region agent log
    fetch('http://127.0.0.1:7695/ingest/2a848e96-f1b8-418a-8aec-1656ee1b9cbe', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': '86a769' }, body: JSON.stringify({ sessionId: '86a769', runId: debugRunId, hypothesisId: 'H3', location: 'resources/js/pages/admin/Appointments.vue:onMounted', message: 'Admin appointments mounted; initial UI state', data: { showCreateDialog: showCreateDialog.value, createButtonExists: Boolean(document.querySelector('[data-debug-create-appointment-btn]')), dialogContentExistsAtMount: Boolean(document.querySelector('[data-debug-appointment-dialog]')) }, timestamp: Date.now() }) }).catch(() => {});
    // #endregion
    // #region agent log
    fetch('http://127.0.0.1:7695/ingest/2a848e96-f1b8-418a-8aec-1656ee1b9cbe', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': '86a769' }, body: JSON.stringify({ sessionId: '86a769', runId: debugRunId, hypothesisId: 'H5', location: 'resources/js/pages/admin/Appointments.vue:onMounted', message: 'Vuetify component/runtime presence check', data: { hasVuetifyGlobal: Boolean(instance?.appContext.config.globalProperties?.$vuetify), unresolvedVBtnTagExists: Boolean(document.querySelector('v-btn[data-debug-create-appointment-btn]')), resolvedNativeButtonExists: Boolean(document.querySelector('button[data-debug-create-appointment-btn]')) }, timestamp: Date.now() }) }).catch(() => {});
    // #endregion
    await loadAppointments();
});
</script>

<template>
    <Head title="Admin Appointments" />

    <div class="mx-auto max-w-7xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Appointment Management</h1>
                <p class="text-sm text-zinc-500">
                    Track and coordinate bookings across the platform.
                </p>
            </div>
            <button
                data-debug-create-appointment-btn
                type="button"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                @click="openCreateDialog"
            >
                Create Appointment
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
                    Total Appointments
                </div>
                <div class="mt-2 text-3xl font-bold">{{ totalAppointments }}</div>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div
                    class="text-xs font-semibold tracking-wider text-zinc-500 uppercase"
                >
                    Booked
                </div>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ bookedAppointments }}</div>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div
                    class="text-xs font-semibold tracking-wider text-zinc-500 uppercase"
                >
                    Completed
                </div>
                <div class="mt-2 text-3xl font-bold text-green-600">{{ completedAppointments }}</div>
            </div>
        </div>

        <div
            class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <h2 class="mb-4 text-lg font-semibold">All Appointments</h2>

            <div
                v-if="appointments.length === 0"
                class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-zinc-500 dark:border-zinc-700"
            >
                <span v-if="isLoadingAppointments">Loading appointments...</span>
                <span v-else>No appointments yet.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-2">Practitioner</th>
                            <th class="pb-2">Date/Time</th>
                            <th class="pb-2">Time Slot</th>
                            <th class="pb-2">Booked By</th>
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
                            <td class="py-3 font-medium">{{ appointment.practitioner_name }}</td>
                            <td class="py-3 text-zinc-600 dark:text-zinc-300">
                                {{ new Date(appointment.appointment_at).toLocaleString() }}
                            </td>
                            <td class="py-3 text-zinc-600 dark:text-zinc-300">
                                {{ appointment.time_slot || '-' }}
                            </td>
                            <td class="py-3 text-zinc-600 dark:text-zinc-300">
                                {{ appointment.user_name || '-' }}
                            </td>
                            <td class="py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                    :class="
                                        appointment.status === 'booked'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
                                            : appointment.status === 'completed'
                                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200'
                                              : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
                                    "
                                >
                                    {{ appointment.status }}
                                </span>
                            </td>
                            <td class="py-3 text-zinc-600 dark:text-zinc-300">
                                {{ appointment.notes || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="rounded-xl border border-zinc-200 bg-white p-4 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <span class="font-semibold">Time slot overview:</span>
            {{ bookedAppointments }} booked, {{ completedAppointments }} completed,
            {{ cancelledAppointments }} cancelled.
        </div>

        <div
            v-if="showCreateDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div
                data-debug-appointment-dialog
                class="w-full max-w-2xl rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
            >
                <h2 data-debug-appointment-title class="text-lg font-bold">
                    Create Appointment
                </h2>
                <form @submit.prevent="createAppointment" class="mt-4 space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
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
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
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
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            :disabled="isCreatingAppointment"
                            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                            @click="showCreateDialog = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="isCreatingAppointment"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{
                                isCreatingAppointment
                                    ? 'Creating...'
                                    : 'Create Appointment'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
