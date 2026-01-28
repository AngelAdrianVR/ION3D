<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: { type: Boolean, default: true }, 
});

const emit = defineEmits(['closeMobile']);

const page = usePage();
const user = computed(() => page.props.auth.user);

// Estado local (Colapsado / Expandido)
const isCollapsed = ref(false);
const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
    // Si colapsamos, cerramos el acordeón de settings para evitar bugs visuales al re-expandir
    if (isCollapsed.value) settingsOpen.value = false;
};

// Estado para submenús (Solo para modo expandido)
const settingsOpen = ref(false);

// --- MENÚ ---
const menuItems = [
    { 
        name: 'Dashboard', 
        route: 'dashboard', 
        icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z', 
        show: true 
    },
    { 
        name: 'Reportes', 
        route: 'dashboard', 
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 
        show: true 
    },
    { 
        name: 'Punto de Venta', 
        route: 'dashboard', 
        icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 
        show: true 
    },
    { 
        name: 'Gestión de Página', 
        route: 'dashboard', 
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9', 
        show: true 
    },
    { 
        name: 'Usuarios', 
        route: 'dashboard', 
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 
        show: true 
    },
];

const configItems = [
    { name: 'Roles y Permisos', route: 'dashboard', show: true }
];

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <!-- 
      SIDENAV CONTAINER 
      Nota: En móvil (md:...) forzamos w-72 para que siempre esté expandido visualmente 
      aunque la variable isCollapsed sea true.
    -->
    <aside 
        class="fixed inset-y-0 left-0 z-50 transition-all duration-300 ease-in-out transform shadow-[0_0_40px_-10px_rgba(0,0,0,0.1)] m-3 md:m-4 rounded-3xl flex flex-col bg-white border border-slate-100"
        :class="[
            isCollapsed ? 'md:w-24' : 'w-72',
            isOpen ? 'translate-x-0' : '-translate-x-[120%] md:translate-x-0',
            // En móvil forzamos el ancho completo siempre
            'w-72'
        ]"
    >
        <!-- 1. HEADER / LOGO -->
        <div class="flex transition-all duration-300 px-6 py-6" 
             :class="isCollapsed ? 'flex-col items-center justify-center gap-4 h-auto' : 'flex-row items-center justify-between h-24'">
            
            <!-- Logo Container -->
            <div class="flex items-center gap-3 animate-fade-in transition-all duration-300">
                <!-- Icono Logo -->
                <div class="relative p-1 bg-gradient-to-br from-[#2f4b59] to-[#1a2e38] rounded-full flex items-center justify-center text-white overflow-hidden shadow-lg group-hover:scale-110 transition-transform duration-300"
                     :class="isCollapsed ? 'size-12' : 'size-12'">
                    <img src="@/../../public/images/orion-logo-blanco.png" alt="" class="object-contain h-full w-full">
                    <!-- Brillo -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-50"></div>
                </div>
                
                <!-- Texto Logo (Solo visible si NO está colapsado) -->
                <div v-if="!isCollapsed" class="flex flex-col leading-none transition-all duration-300">
                    <span class="text-2xl font-bold tracking-tight text-[#2f4b59]">
                        ION<span class="text-slate-400">3D</span>
                    </span>
                </div>
            </div>

            <!-- Botón Colapsar (Solo Desktop) -->
            <!-- Si está colapsado: aparece abajo centrado. Si no: a la derecha. -->
             <button 
                @click="toggleCollapse" 
                class="hidden md:flex text-slate-400 hover:text-[#2f4b59] transition-all p-1.5 rounded-full hover:bg-slate-100 border border-transparent hover:border-slate-200"
                :class="{'rotate-180 mt-2': isCollapsed}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            
            <!-- Botón Cerrar (Solo Móvil - Cierra el sidebar completamente) -->
            <button @click="$emit('closeMobile')" class="md:hidden text-slate-400 hover:text-slate-600 ml-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- 2. NAV LINKS -->
        <!-- Añadimos 'overflow-visible' cuando está colapsado para permitir tooltips/dropdowns saliendo del contenedor -->
        <div class="flex-1 py-2 space-y-2 px-4 custom-scrollbar"
             :class="isCollapsed ? 'overflow-visible' : 'overflow-y-auto'">
            
            <!-- Label Sección -->
            <div v-if="!isCollapsed" class="px-3 mb-2 mt-2 text-xs font-bold text-slate-400 uppercase tracking-wider animate-fade-in">
                Menu Principal
            </div>
            <div v-else class="h-4 border-b border-slate-100 mb-4 mx-2"></div> 

            <template v-for="(item, index) in menuItems" :key="index">
                <Link 
                    v-if="item.show"
                    :href="route(item.route)" 
                    class="group relative flex items-center rounded-xl transition-all duration-300 ease-out"
                    :class="[
                        isCollapsed ? 'justify-center p-3' : 'px-3 py-3 gap-3',
                        route().current(item.route) 
                        ? 'bg-gradient-to-r from-[#2f4b59] to-[#507588] text-white shadow-lg shadow-[#2f4b59]/25' 
                        : 'text-slate-500 hover:bg-gradient-to-r hover:from-slate-200 hover:to-slate-50 hover:text-[#2f4b59]'
                    ]"
                >
                    <!-- Icono -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="transition-colors duration-200" 
                         :class="isCollapsed ? 'h-6 w-6' : 'h-[22px] w-[22px] min-w-[22px]'"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>

                    <!-- Texto (Solo expandido) -->
                    <span v-if="!isCollapsed" class="font-medium whitespace-nowrap text-sm">
                        {{ item.name }}
                    </span>
                    
                    <!-- TOOLTIP FLOTANTE (Solo colapsado + hover) -->
                    <div v-if="isCollapsed" 
                         class="absolute left-full ml-4 px-3 py-2 bg-[#2f4b59] text-white text-sm font-medium rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-all duration-200 translate-x-[-10px] group-hover:translate-x-0 z-50 whitespace-nowrap flex items-center">
                        {{ item.name }}
                        <!-- Flechita del tooltip -->
                        <div class="absolute left-0 top-1/2 -translate-x-1 -translate-y-1/2 w-2 h-2 bg-[#2f4b59] rotate-45"></div>
                    </div>
                </Link>
            </template>

            <!-- Separador -->
            <div class="my-4 border-t border-slate-100 mx-2"></div>

            <!-- 
                CONFIGURACIÓN (GRUPO)
                Comportamiento híbrido: Accordion si expandido, Dropdown si colapsado.
            -->
            <div class="group relative"> <!-- 'group' necesario para el hover en colapsado -->
                
                <!-- Botón Principal -->
                <button 
                    @click="!isCollapsed ? (settingsOpen = !settingsOpen) : null"
                    class="w-full flex items-center rounded-xl transition-all duration-300 text-slate-500 hover:bg-gradient-to-r hover:from-slate-200 hover:to-slate-50 hover:text-[#2f4b59]"
                    :class="[
                        isCollapsed ? 'justify-center p-3' : 'justify-between px-3 py-3',
                        settingsOpen && !isCollapsed ? 'bg-slate-50 text-[#2f4b59]' : ''
                    ]"
                >
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             :class="isCollapsed ? 'h-6 w-6' : 'h-[22px] w-[22px] min-w-[22px]'"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span v-if="!isCollapsed" class="font-medium whitespace-nowrap text-sm">Configuración</span>
                    </div>
                    <!-- Chevron (Solo visible expandido) -->
                    <svg v-if="!isCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{'rotate-90': settingsOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenú: MODO EXPANDIDO (Acordeón) -->
                <div v-show="settingsOpen && !isCollapsed" class="mt-1 space-y-1 overflow-hidden transition-all duration-300">
                    <Link 
                        v-for="(subItem, subIndex) in configItems" 
                        :key="subIndex"
                        :href="route(subItem.route)"
                        class="block pl-12 py-2 text-sm text-slate-400 hover:text-[#2f4b59] font-medium transition-colors hover:translate-x-1 duration-200"
                    >
                        {{ subItem.name }}
                    </Link>
                </div>

                <!-- Submenú: MODO COLAPSADO (Dropdown Flotante al Hover) -->
                <div v-if="isCollapsed" 
                     class="absolute top-0 left-full ml-4 w-48 bg-white rounded-xl shadow-xl border border-slate-100 p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-x-[-10px] group-hover:translate-x-0 z-50">
                    
                    <!-- Header del Dropdown -->
                    <div class="px-3 py-2 text-xs font-bold text-slate-400 uppercase border-b border-slate-100 mb-1">
                        Configuración
                    </div>
                    
                    <Link 
                        v-for="(subItem, subIndex) in configItems" 
                        :key="subIndex"
                        :href="route(subItem.route)"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 hover:text-[#2f4b59] hover:bg-slate-50 rounded-lg transition-colors"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        {{ subItem.name }}
                    </Link>

                    <!-- Flechita del dropdown -->
                    <div class="absolute left-0 top-6 -translate-x-1.5 w-3 h-3 bg-white border-l border-b border-slate-100 rotate-45"></div>
                </div>
            </div>
        </div>

        <!-- 3. FOOTER / USER PROFILE -->
        <div class="p-4 mt-auto border-t border-slate-100 bg-slate-50/50 backdrop-blur-sm">
            
            <Link :href="route('profile.show')" 
                  class="flex items-center gap-3 p-2 rounded-2xl hover:bg-white hover:shadow-md transition-all duration-300 group cursor-pointer" 
                  :class="{'justify-center': isCollapsed}">
                
                <!-- Avatar -->
                <div class="relative shrink-0">
                     <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:border-[#2f4b59] transition-colors" :src="user.profile_photo_url" :alt="user.name">
                     <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>

                <!-- Info -->
                <div v-if="!isCollapsed" class="flex-1 min-w-0 overflow-hidden text-left">
                    <p class="text-sm font-bold text-slate-700 truncate group-hover:text-[#2f4b59]">{{ user.name }}</p>
                    <p class="text-xs text-slate-400 truncate">Ver Perfil</p>
                </div>
                
                <!-- Icono Chevron -->
                <div v-if="!isCollapsed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 group-hover:text-[#2f4b59] group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>

            <!-- Logout -->
            <div v-if="!isCollapsed" class="mt-2">
                 <form @submit.prevent="logout">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-2.5 text-xs font-bold text-slate-400 hover:text-white hover:bg-gradient-to-r hover:from-red-500 hover:to-red-600 rounded-xl transition-all uppercase tracking-wider shadow-sm hover:shadow-red-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

    </aside>
</template>

<style scoped>
/* Scrollbar delgado y discreto */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateX(-5px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>