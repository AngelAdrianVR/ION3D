<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head } from '@inertiajs/vue3';
import { NConfigProvider } from 'naive-ui';
import { ref, onMounted, computed, watch } from 'vue';

const props = defineProps({
    dbItems: {
        type: Object,
        default: () => ({})
    },
    initialCategory: {
        type: String,
        default: ''
    }
});

// --- Estado ---
const activeTab = ref(props.initialCategory);
const showModal = ref(false);
const selectedItem = ref(null);
const showContent = ref(false);

// Obtener las categorías disponibles (keys del objeto agrupado)
const categories = computed(() => Object.keys(props.dbItems));

// Items de la pestaña activa
const currentItems = computed(() => {
    return props.dbItems[activeTab.value] || [];
});

// Etiquetas legibles para las pestañas
const getTabLabel = (slug) => {
    const map = {
        'impresion': 'Impresión 3D',
        'escaneo': 'Escaneo Digital',
        'modelado': 'Modelado & Diseño'
    };
    // Si no está en el mapa, capitalizar el slug
    return map[slug] || slug.charAt(0).toUpperCase() + slug.slice(1);
};

// --- Métodos ---
const selectTab = (slug) => {
    activeTab.value = slug;
    // Actualizar URL sin recargar
    const url = `/portafolio/${slug}`;
    window.history.replaceState({ path: url }, '', url);
};

const openLightbox = (item) => {
    selectedItem.value = item;
    showModal.value = true;
};

// --- Ciclo de Vida ---
onMounted(() => {
    setTimeout(() => { showContent.value = true; }, 100);
});

// Tema Clean Tech (Coincide con Servicios)
const themeOverrides = {
    common: { primaryColor: '#0ea5e9' }
};
</script>

<template>
    <AppLayoutLanding>
        <Head title="Portafolio" />

        <NConfigProvider :theme-overrides="themeOverrides">
            <div class="min-h-screen bg-slate-50 relative overflow-hidden font-sans">
                
                <!-- Fondo Decorativo Clean Tech -->
                <div class="absolute inset-0 pointer-events-none">
                     <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 mix-blend-multiply"></div>
                     <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-sky-200/40 opacity-50 blur-[100px] rounded-full animate-pulse-slow"></div>
                     <div class="absolute top-40 -left-20 w-[400px] h-[400px] bg-cyan-200/30 opacity-50 blur-[100px] rounded-full"></div>
                </div>

                <div class="relative z-10 max-w-7xl mx-auto px-6 py-16 md:py-24">
                    
                    <!-- 1. Encabezado Animado -->
                    <div class="text-center mb-16 transition-all duration-1000 transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <h2 class="text-cyan-600 font-bold tracking-[0.2em] uppercase text-xs md:text-sm mb-3">Galería Visual</h2>
                        <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight">
                            NUESTRO <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">PORTAFOLIO</span>
                        </h1>
                        <p class="text-slate-500 text-lg max-w-2xl mx-auto leading-relaxed font-light">
                            Una muestra de precisión, arte y tecnología. Descubre cómo transformamos lo físico en digital.
                        </p>
                    </div>

                    <!-- 2. Tabs Estilizados (Pills Tech) -->
                    <div class="flex flex-wrap justify-center gap-3 mb-12 transition-all duration-1000 delay-200 transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        
                        <button 
                            v-for="slug in categories" 
                            :key="slug"
                            @click="selectTab(slug)"
                            class="px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300 border backdrop-blur-md shadow-sm"
                            :class="activeTab === slug 
                                ? 'bg-slate-800 text-white border-slate-800 shadow-lg scale-105' 
                                : 'bg-white text-slate-500 border-slate-200 hover:border-cyan-400 hover:text-cyan-600'"
                        >
                            {{ getTabLabel(slug) }}
                        </button>
                    </div>

                    <!-- 3. Grid de Imágenes con Transición Suave -->
                    <div class="min-h-[400px]"> <!-- Contenedor con altura mínima para evitar colapsos -->
                        <transition mode="out-in" 
                            enter-active-class="transition duration-500 ease-out" 
                            enter-from-class="opacity-0 translate-y-4 scale-95" 
                            enter-to-class="opacity-100 translate-y-0 scale-100" 
                            leave-active-class="transition duration-300 ease-in" 
                            leave-from-class="opacity-100 translate-y-0 scale-100" 
                            leave-to-class="opacity-0 translate-y-4 scale-95"
                        >
                            <!-- Key es activeTab para forzar re-render completo del grid -->
                            <div :key="activeTab" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                                
                                <div v-for="(item, index) in currentItems" 
                                     :key="item.id" 
                                     class="group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-sm hover:shadow-2xl hover:shadow-cyan-900/10 cursor-zoom-in bg-white border border-slate-100 transition-all duration-500 hover:-translate-y-1"
                                     @click="openLightbox(item)"
                                >
                                    <!-- Imagen -->
                                    <img 
                                        :src="item.img" 
                                        :alt="item.title" 
                                        class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                        loading="lazy"
                                    >
                                    
                                    <!-- Overlay Info -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-6">
                                        
                                        <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-100">
                                            <span class="inline-block px-2 py-1 bg-cyan-500/20 border border-cyan-500/30 text-cyan-300 text-[10px] font-bold uppercase tracking-wider rounded mb-2 backdrop-blur-md">
                                                {{ item.category }}
                                            </span>
                                            
                                            <h3 class="text-white text-xl font-bold leading-tight mb-2">
                                                {{ item.title }}
                                            </h3>

                                            <div v-if="item.is_featured" class="flex items-center gap-1 text-amber-400 text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                Proyecto Destacado
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensaje Vacío -->
                                <div v-if="currentItems.length === 0" class="col-span-full flex flex-col items-center justify-center py-20 text-slate-400 border-2 border-dashed border-slate-200 rounded-3xl">
                                    <svg class="w-12 h-12 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="font-medium">No hay proyectos en esta categoría aún.</p>
                                </div>

                            </div>
                        </transition>
                    </div>

                </div>

                <!-- 4. MODAL LIGHTBOX (Imagen Grande) -->
                <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-xl" @click="showModal = false">
                        
                        <!-- Botón cerrar -->
                        <button class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors p-2 z-50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div v-if="selectedItem" class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center" @click.stop>
                            <img :src="selectedItem.img" :alt="selectedItem.title" class="max-h-[80vh] w-auto rounded-lg shadow-2xl" />
                            
                            <div class="mt-6 text-center">
                                <h3 class="text-2xl font-bold text-white">{{ selectedItem.title }}</h3>
                                <p class="text-cyan-400 font-mono text-sm uppercase tracking-widest mt-1">{{ selectedItem.category }}</p>
                            </div>
                        </div>
                    </div>
                </transition>

            </div>
        </NConfigProvider>
    </AppLayoutLanding>
</template>

<style scoped>
.animate-pulse-slow {
    animation: pulse-slow 6s infinite ease-in-out;
}
@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}
</style>