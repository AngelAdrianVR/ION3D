<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Estado
const isOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const selectionMode = ref(false);
const selectedIds = ref([]);

// Referencias DOM
const dropdownRef = ref(null);

// Formateo de fecha relativo simple
const timeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Hace un momento';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `Hace ${minutes} min`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `Hace ${hours} h`;
    const days = Math.floor(hours / 24);
    return `Hace ${days} días`;
};

// Cargar notificaciones
const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error('Error cargando notificaciones', error);
    }
};

// Toggle del dropdown
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    } else {
        selectionMode.value = false;
        selectedIds.value = [];
    }
};

// Cerrar al hacer clic fuera
const closeOnClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
        selectionMode.value = false;
        selectedIds.value = [];
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
    // Carga inicial solo del conteo si quisieras optimizar, 
    // pero aquí cargamos todo para simplificar
    fetchNotifications(); 
});

onUnmounted(() => document.removeEventListener('click', closeOnClickOutside));

// Acciones
const handleNotificationClick = async (notification) => {
    if (selectionMode.value) {
        toggleSelection(notification.id);
        return;
    }

    // Marcar como leída visualmente inmediato
    if (!notification.read_at) {
        notification.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
        await axios.put(route('notifications.read', notification.id));
    }

    // Navegar si tiene URL (Asumimos que la data tiene 'url')
    // Estructura esperada en BD data: {"url": "...", "message": "..."}
    if (notification.data.url) {
        isOpen.value = false;
        router.visit(notification.data.url);
    }
};

const markAllRead = async () => {
    loading.value = true;
    try {
        await axios.post(route('notifications.read-all'));
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
    } finally {
        loading.value = false;
    }
};

// Lógica de Selección
const toggleSelection = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(itemId => itemId !== id);
    } else {
        selectedIds.value.push(id);
    }
};

const executeBulkAction = async (action) => {
    if (selectedIds.value.length === 0) return;
    loading.value = true;
    
    try {
        await axios.post(route('notifications.bulk'), {
            ids: selectedIds.value,
            action: action
        });
        
        // Actualizar UI localmente
        if (action === 'delete') {
            notifications.value = notifications.value.filter(n => !selectedIds.value.includes(n.id));
        } else if (action === 'read') {
            notifications.value.forEach(n => {
                if (selectedIds.value.includes(n.id)) n.read_at = new Date().toISOString();
            });
        }
        
        // Recalcular conteo no leídos
        const unread = notifications.value.filter(n => !n.read_at).length;
        unreadCount.value = unread;
        
        // Reset
        selectionMode.value = false;
        selectedIds.value = [];
        
    } finally {
        loading.value = false;
    }
};

</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Trigger Button -->
        <button 
            @click.stop="toggleDropdown"
            class="relative p-2 text-slate-400 hover:text-blue-500 transition-colors rounded-full hover:bg-slate-100 focus:outline-none active:bg-blue-50"
            :class="{ 'text-blue-500 bg-blue-50': isOpen }"
        >
            <!-- Badge -->
            <span v-if="unreadCount > 0" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-white"></span>
            </span>
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>

        <!-- Dropdown Panel -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95 translate-y-2"
            enter-to-class="transform opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="transform opacity-100 scale-100 translate-y-0"
            leave-to-class="transform opacity-0 scale-95 translate-y-2"
        >
            <div 
                v-if="isOpen" 
                class="absolute right-0 mt-3 w-80 md:w-96 bg-white/90 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl z-50 overflow-hidden ring-1 ring-black/5 origin-top-right"
            >
                <!-- Header -->
                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-white/50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Notificaciones</h3>
                        <p v-if="selectionMode" class="text-xs text-blue-600 font-medium">
                            {{ selectedIds.length }} seleccionadas
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                         <button 
                            @click="selectionMode = !selectionMode; selectedIds = []"
                            class="text-xs font-semibold px-2 py-1 rounded-md transition-colors"
                            :class="selectionMode ? 'bg-blue-100 text-blue-700' : 'text-slate-500 hover:bg-slate-100'"
                        >
                            {{ selectionMode ? 'Cancelar' : 'Seleccionar' }}
                        </button>
                        
                        <button 
                            v-if="!selectionMode && unreadCount > 0"
                            @click="markAllRead" 
                            class="text-xs text-slate-400 hover:text-blue-600 transition-colors"
                            :disabled="loading"
                        >
                            Leer todas
                        </button>
                    </div>
                </div>

                <!-- List -->
                <div class="max-h-[28rem] overflow-y-auto custom-scroll">
                    <div v-if="loading && notifications.length === 0" class="p-8 text-center text-slate-400">
                        <svg class="animate-spin h-6 w-6 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Cargando...
                    </div>

                    <div v-else-if="notifications.length === 0" class="p-8 text-center">
                        <div class="bg-slate-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500">No tienes notificaciones</p>
                    </div>

                    <ul v-else class="divide-y divide-slate-50">
                        <li 
                            v-for="notification in notifications" 
                            :key="notification.id"
                            @click="handleNotificationClick(notification)"
                            class="relative p-4 hover:bg-slate-50/80 transition-colors cursor-pointer group"
                            :class="{'bg-blue-50/40': selectionMode && selectedIds.includes(notification.id)}"
                        >
                            <div class="flex items-start gap-3">
                                <!-- Checkbox Selection Mode -->
                                <div v-if="selectionMode" class="pt-1">
                                    <div 
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="selectedIds.includes(notification.id) ? 'border-blue-500 bg-blue-500' : 'border-slate-300'"
                                    >
                                        <svg v-if="selectedIds.includes(notification.id)" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Icono según tipo (Genérico por ahora) -->
                                <div v-else class="relative shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                        <!-- Puedes personalizar iconos según notification.type -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <!-- Punto azul si no leída -->
                                    <span v-if="!notification.read_at" class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-blue-500 ring-2 ring-white"></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 leading-snug" :class="{'opacity-60': notification.read_at}">
                                        {{ notification.data.title || 'Nueva notificación' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                        {{ notification.data.message || 'Tienes un nuevo mensaje del sistema.' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">
                                        {{ timeAgo(notification.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Footer / Acciones Bulk -->
                <div v-if="selectionMode" class="bg-slate-50 border-t border-slate-100 p-2 flex gap-2">
                    <button 
                        @click="executeBulkAction('read')"
                        :disabled="selectedIds.length === 0 || loading"
                        class="flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors disabled:opacity-50"
                    >
                        Marcar leídos
                    </button>
                    <button 
                        @click="executeBulkAction('delete')"
                        :disabled="selectedIds.length === 0 || loading"
                        class="flex-1 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50"
                    >
                        Eliminar
                    </button>
                </div>
                <div v-else class="bg-slate-50 border-t border-slate-100 p-2 text-center">
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ver historial completo</a>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 5px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
</style>