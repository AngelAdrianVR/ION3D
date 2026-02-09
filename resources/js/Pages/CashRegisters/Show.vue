<template>
  <AppLayout :title="`Detalle - ${register.name}`">
    <template #header>
        <div class="flex items-center gap-4">
            <div class="transform transition-transform active:scale-95">
                <Back :route="route('cash-registers.index')" />
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ register.name }}
                </h2>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ register.location || 'Sin ubicación registrada' }}
                </p>
            </div>
            
            <!-- Badge de Estado Actual -->
            <div class="ml-auto px-4 py-2 rounded-xl flex items-center gap-2 border shadow-sm"
                :class="activeSession ? 'bg-green-50 border-green-200 text-green-700' : 'bg-gray-50 border-gray-200 text-gray-500'"
            >
                <span class="relative flex h-3 w-3">
                  <span v-if="activeSession" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3" :class="activeSession ? 'bg-green-500' : 'bg-gray-400'"></span>
                </span>
                <span class="font-bold text-sm">
                    {{ activeSession ? `En uso por: ${activeSession.user.name}` : 'Caja Disponible' }}
                </span>
            </div>
        </div>
    </template>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <n-config-provider :theme-overrides="iosThemeOverrides">
                
                <!-- FILTROS Y ESTADÍSTICAS -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Tarjeta de Filtros -->
                    <div class="bg-white p-5 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex flex-col justify-center gap-4">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Filtrar Historial</h3>
                        <div class="flex gap-2">
                            <n-select 
                                v-model:value="selectedMonth" 
                                :options="months" 
                                placeholder="Mes" 
                                @update:value="applyFilters"
                            />
                            <n-select 
                                v-model:value="selectedYear" 
                                :options="years" 
                                placeholder="Año" 
                                class="w-32"
                                @update:value="applyFilters"
                            />
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Cortes Realizados</p>
                            <p class="text-3xl font-bold text-indigo-600">{{ stats.total_sessions }}</p>
                            <p class="text-xs text-gray-400 mt-1">En el periodo seleccionado</p>
                        </div>
                        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Dinero Reportado</p>
                            <p class="text-3xl font-bold text-gray-800">{{ formatCurrency(stats.total_cash_in) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Suma de cierres de caja</p>
                        </div>
                        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100" 
                             :class="stats.discrepancies < 0 ? 'border-red-200 bg-red-50' : (stats.discrepancies > 0 ? 'border-green-200 bg-green-50' : '')">
                            <p class="text-xs font-bold uppercase mb-1" :class="stats.discrepancies !== 0 ? 'text-gray-600' : 'text-gray-400'">Discrepancias Totales</p>
                            <p class="text-3xl font-bold" :class="stats.discrepancies < 0 ? 'text-red-600' : (stats.discrepancies > 0 ? 'text-green-600' : 'text-gray-800')">
                                {{ stats.discrepancies > 0 ? '+' : '' }}{{ formatCurrency(stats.discrepancies) }}
                            </p>
                            <p class="text-xs mt-1 opacity-70">Diferencia (Real vs Sistema)</p>
                        </div>
                    </div>
                </div>

                <!-- TIMELINE DE SESIONES -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-900">Historial de Cortes</h3>
                    </div>

                    <div v-if="sessions.length === 0" class="p-12 text-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p>No hay registros para la fecha seleccionada.</p>
                    </div>

                    <div v-else class="p-6">
                        <div class="relative pl-8 border-l-2 border-gray-100 space-y-10">
                            
                            <div v-for="session in sessions" :key="session.id" class="relative group">
                                <!-- Punto en la línea de tiempo -->
                                <div class="absolute -left-[39px] top-1 h-5 w-5 rounded-full border-2 border-white shadow-sm"
                                    :class="session.status === 'Abierta' ? 'bg-green-500 animate-pulse' : 'bg-gray-300'"
                                ></div>

                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 p-5 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-lg transition-all border border-transparent hover:border-blue-100">
                                    
                                    <!-- Info Usuario y Fechas -->
                                    <div class="flex gap-4">
                                        <div class="h-12 w-12 rounded-xl bg-gray-200 overflow-hidden flex-shrink-0">
                                            <img v-if="session.user_avatar" :src="session.user_avatar" class="h-full w-full object-cover">
                                            <div v-else class="h-full w-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                                {{ getInitials(session.user_name) }}
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-lg">
                                                {{ session.status === 'Abierta' ? 'Turno en curso' : `Corte #${session.id}` }}
                                            </p>
                                            <p class="text-sm text-gray-600 font-medium">{{ session.user_name }}</p>
                                            <div class="text-xs text-gray-400 mt-1 flex flex-col sm:flex-row sm:gap-3">
                                                <span class="flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    {{ session.opened_at }}
                                                </span>
                                                <span v-if="session.closed_at" class="flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    {{ session.duration }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detalles Financieros -->
                                    <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm bg-white p-3 rounded-xl border border-gray-100/50">
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase font-bold">Apertura</p>
                                            <p class="font-bold text-gray-700">{{ formatCurrency(session.opening_amount) }}</p>
                                        </div>
                                        <div v-if="session.status !== 'Abierta'">
                                            <p class="text-xs text-gray-400 uppercase font-bold">Calculado</p>
                                            <p class="font-bold text-gray-700">{{ formatCurrency(session.calculated_amount) }}</p>
                                        </div>
                                        <div v-if="session.status !== 'Abierta'">
                                            <p class="text-xs text-gray-400 uppercase font-bold">Reportado</p>
                                            <p class="font-bold text-indigo-600">{{ formatCurrency(session.closing_amount) }}</p>
                                        </div>
                                        <div v-if="session.status !== 'Abierta'">
                                            <p class="text-xs text-gray-400 uppercase font-bold">Diferencia</p>
                                            <span 
                                                class="px-2 py-0.5 rounded text-xs font-bold inline-block"
                                                :class="Math.abs(session.difference) < 1 ? 'bg-gray-100 text-gray-500' : (session.difference < 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600')"
                                            >
                                                {{ session.difference > 0 ? '+' : '' }}{{ formatCurrency(session.difference) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Notas (Tooltip o expandible) -->
                                    <div v-if="session.notes" class="md:w-48 text-xs text-gray-500 italic bg-yellow-50/50 p-2 rounded-lg border border-yellow-100 self-stretch">
                                        "{{ session.notes }}"
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </n-config-provider>
        </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { NConfigProvider, NSelect } from 'naive-ui';

const props = defineProps({
    register: Object,
    sessions: Array,
    activeSession: Object,
    filters: Object,
    stats: Object
});

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

const months = [
    { label: 'Enero', value: 1 }, { label: 'Febrero', value: 2 }, { label: 'Marzo', value: 3 },
    { label: 'Abril', value: 4 }, { label: 'Mayo', value: 5 }, { label: 'Junio', value: 6 },
    { label: 'Julio', value: 7 }, { label: 'Agosto', value: 8 }, { label: 'Septiembre', value: 9 },
    { label: 'Octubre', value: 10 }, { label: 'Noviembre', value: 11 }, { label: 'Diciembre', value: 12 }
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 5 }, (_, i) => ({ label: String(currentYear - i), value: currentYear - i }));

const applyFilters = () => {
    router.get(
        route('cash-registers.show', props.register.id),
        { month: selectedMonth.value, year: selectedYear.value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
};

const formatCurrency = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val);

const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??';

const iosThemeOverrides = {
  common: { primaryColor: '#007AFF', borderRadius: '12px' },
  Select: { peers: { InternalSelection: { borderRadius: '10px' } } }
};
</script>