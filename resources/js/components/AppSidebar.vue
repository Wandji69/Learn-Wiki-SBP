<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, CalendarDays, LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
// import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import * as adminRoutes from '@/routes/admin';
import * as userRoutes from '@/routes/user';
import { useAuthStore } from '@/stores/auth';
import type { AuthUser, NavItem } from '@/types';

const authStore = useAuthStore();
const user = computed<AuthUser | null>(() => authStore.user ?? null);
const isAdmin = computed(() =>
    user.value?.roles?.some((role) => role.name === 'admin'),
);

const mainNavItems = computed<NavItem[]>(() => {
    const dashboardHref = isAdmin.value
        ? adminRoutes.dashboard()
        : userRoutes.dashboard();
    const coursesHref = isAdmin.value
        ? adminRoutes.courses()
        : userRoutes.courses();
    const appointmentsHref = isAdmin.value
        ? adminRoutes.appointments()
        : userRoutes.appointments();

    return [
        {
            title: 'Dashboard',
            href: dashboardHref,
            icon: LayoutGrid,
        },
        {
            title: isAdmin.value ? 'Course Management' : 'Courses',
            href: coursesHref,
            icon: BookOpen,
        },
        {
            title: isAdmin.value ? 'Appointment Management' : 'Appointments',
            href: appointmentsHref,
            icon: CalendarDays,
        },
    ];
});

const homeHref = computed(() =>
    isAdmin.value ? adminRoutes.dashboard() : userRoutes.dashboard(),
);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
