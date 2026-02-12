<script setup>
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
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="">
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    Panel de Control
                </h2>
                <p class="text-sm text-gray-500 font-medium">
                    {{ new Date().toLocaleDateString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </p>
            </div>
        </template>

        <div class="py-8 bg-gray-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Sección 1: KPIs (Tarjetas Superiores) -->
                <section>
                    <StatsCards :stats="stats" />
                </section>

                <!-- Sección 2: Contenido Principal (Grid 2 columnas asimétricas) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Columna Izquierda (Principal - Ocupa 2 espacios) -->
                    <div class="lg:col-span-2 space-y-8">
                        <RecentOrders :orders="recentOrders" />
                        
                        <!-- Aquí podrías poner una gráfica de ventas en el futuro -->
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