<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidenav from '@/Components/MyComponents/Sidenav.vue';
import NotificationsDropdown from '@/Components/MyComponents/NotificationsDropdown.vue';

defineProps({
    title: String,
});

// Control del menú móvil
const mobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};
</script>

<template>
    <div class="bg-blue-50 min-h-screen font-sans text-slate-900">
        <Head :title="title" />
        <Banner />

        <!-- LAYOUT GRID -->
        <div class="flex h-screen overflow-hidden">
            
            <!-- SIDENAV (Móvil y Desktop) -->
            <Sidenav 
                :is-open="mobileMenuOpen" 
                @close-mobile="mobileMenuOpen = false"
                class="md:relative" 
            />

            <!-- CONTENIDO PRINCIPAL -->
            <div class="flex-1 flex flex-col min-w-0 bg-blue-50 h-full relative">
                
                <!-- HEADER MINIMALISTA (Móvil y Desktop) -->
                <header class="flex items-center justify-between rounded-l-2xl px-6 py-5 bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-slate-100">
                    
                    <!-- Trigger Móvil -->
                    <div class="flex items-center gap-4">
                        <button @click="toggleMobileMenu" class="md:hidden text-slate-500 hover:text-[#2f4b59] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>

                        <!-- Título de Página (Slot Header) -->
                        <div v-if="$slots.header">
                            <slot name="header" />
                        </div>
                        <div v-else>
                            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard</h2>
                        </div>
                    </div>

                    <!-- Acciones Rápidas Header -->
                    <div class="flex items-center gap-4">
                         <!-- COMPONENTE DE NOTIFICACIONES IMPORTADO -->
                         <NotificationsDropdown />
                    </div>
                </header>

                <!-- AREA DE CONTENIDO -->
                <main class="flex-1 overflow-y-auto p-4 md:p-8 custom-content-scroll">
                    <!-- Overlay para cerrar menú móvil al hacer click fuera -->
                    <div 
                        v-if="mobileMenuOpen" 
                        @click="mobileMenuOpen = false"
                        class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm transition-opacity"
                    ></div>

                    <slot />
                </main>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scroll del contenido principal */
.custom-content-scroll::-webkit-scrollbar {
    width: 8px;
}
.custom-content-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.custom-content-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-content-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>