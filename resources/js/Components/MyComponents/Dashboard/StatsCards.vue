<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        // Agregamos un objeto por defecto para evitar que falle si stats es undefined
        default: () => ({
            daily_sales: 0,
            monthly_sales: 0,
            pending_appointments: 0,
            new_clients: 0
        })
    }
});

// Usamos encadenamiento opcional (?.) y valores por defecto (|| 0)
const formattedDaily = computed(() => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(props.stats?.daily_sales || 0);
});

const formattedMonthly = computed(() => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(props.stats?.monthly_sales || 0);
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Venta del Día -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 transition hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Venta Hoy</h3>
                <div class="p-2 bg-green-50 text-green-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ formattedDaily }}</p>
            <p class="text-xs text-green-600 mt-2 font-medium flex items-center">
                <span class="mr-1">↑</span> Ingresos brutos
            </p>
        </div>

        <!-- Venta Mensual -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 transition hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Este Mes</h3>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ formattedMonthly }}</p>
            <p class="text-xs text-gray-400 mt-2">Acumulado mensual</p>
        </div>

        <!-- Citas Pendientes -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 transition hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Citas Futuras</h3>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <!-- Accedemos de forma segura a la propiedad -->
            <p class="text-3xl font-bold text-gray-900">{{ props.stats?.pending_appointments || 0 }}</p>
            <p class="text-xs text-gray-400 mt-2">Pendientes de atención</p>
        </div>

        <!-- Nuevos Clientes -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 transition hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Nuevos Clientes</h3>
                <div class="p-2 bg-orange-50 text-orange-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <!-- Accedemos de forma segura a la propiedad -->
            <p class="text-3xl font-bold text-gray-900">{{ props.stats?.new_clients || 0 }}</p>
            <p class="text-xs text-gray-400 mt-2">Este mes</p>
        </div>
    </div>
</template>