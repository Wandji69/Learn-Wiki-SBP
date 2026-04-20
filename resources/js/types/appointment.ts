export type Appointment = {
    id: number;
    practitioner_name: string;
    appointment_at: string;
    time_slot?: string;
    status: string;
    notes: string | null;
    user_name?: string | null;
};
