<script setup>
defineProps({
    appointments: {
        type: Array,
        default: () => [] // Valor por defecto para evitar errores si es undefined
    }
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-lg text-gray-900">Agenda de Hoy</h3>
        </div>
        <div class="divide-y divide-gray-50">
            <div v-for="appt in appointments" :key="appt.id" class="p-4 flex items-center hover:bg-gray-50/50 transition">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm">
                    {{ appt.time }}
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900">{{ appt.client }}</p>
                    <p class="text-xs text-gray-500">{{ appt.service }}</p>
                </div>
                <div>
                    <span v-if="appt.status === 'Confirmada'" class="w-2 h-2 rounded-full bg-green-500 block"></span>
                    <span v-else class="w-2 h-2 rounded-full bg-yellow-500 block"></span>
                </div>
            </div>
            
            <div v-if="appointments.length === 0" class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 text-gray-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-sm text-gray-500">Sin citas para hoy</p>
            </div>
        </div>
    </div>
</template>