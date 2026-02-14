<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatsCards from '@/Components/MyComponents/Dashboard/StatsCards.vue';
import RecentOrders from '@/Components/MyComponents/Dashboard/RecentOrders.vue';
import AppointmentList from '@/Components/MyComponents/Dashboard/AppointmentList.vue';
import LowStockAlert from '@/Components/MyComponents/Dashboard/LowStockAlert.vue';

defineProps({
    stats: Object,
    recentOrders: Array,
    todayAppointments: Array,
    lowStockProducts: Array,
});

const page = usePage();
const can = (permission) => page.props.auth?.permissions?.includes(permission);

// Definición de botones de acceso rápido con sus permisos
const quickActions = computed(() => [
    { 
        name: 'Terminal PV', 
        route: 'pos.index', 
        icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        color: 'bg-blue-600',
        permission: 'pos.access'
    },
    { 
        name: 'Gestionar Cajas', 
        route: 'cash-registers.index', 
        icon: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        color: 'bg-emerald-600',
        permission: 'pos.access'
    },
    { 
        name: 'Historial de Ventas', 
        route: 'orders.index', 
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        color: 'bg-purple-600',
        permission: 'orders.index'
    }
].filter(action => can(action.permission)));
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                        Panel de Control
                    </h2>
                    <p class="text-sm text-gray-500 font-medium">
                        {{ new Date().toLocaleDateString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Sección 1: KPIs (Tarjetas Superiores) -->
                <section>
                    <StatsCards :stats="stats" />
                </section>

                <!-- Sección: Acciones Rápidas (Condicional) -->
                <section v-if="quickActions.length > 0">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Accesos Directos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link 
                            v-for="action in quickActions" 
                            :key="action.name"
                            :href="route(action.route)"
                            class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group"
                        >
                            <div :class="['p-3 rounded-xl text-white shadow-lg transition-transform group-hover:scale-110', action.color]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ action.name }}</p>
                                <p class="text-xs text-gray-400">Ir al módulo ahora</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </section>

                <!-- Sección 2: Contenido Principal (Grid 2 columnas asimétricas) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Columna Izquierda (Principal - Ocupa 2 espacios) -->
                    <div class="lg:col-span-2 space-y-8">
                        <RecentOrders :orders="recentOrders" />
                    </div>

                    <!-- Columna Derecha (Lateral - Ocupa 1 espacio) -->
                    <div class="space-y-8">
                        <AppointmentList :appointments="todayAppointments" />
                        <LowStockAlert :products="lowStockProducts" />
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>