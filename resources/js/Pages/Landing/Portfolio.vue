<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, router } from '@inertiajs/vue3';
import { NTabs, NTabPane, NModal, NConfigProvider } from 'naive-ui';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    category: {
        type: String,
        default: 'comercial'
    }
});

// --- Estado ---
const activeTab = ref(props.category || 'comercial');
const showModal = ref(false);
const selectedItem = ref(null);
const showContent = ref(false); // Control para animaciones de entrada

// --- Datos de Galería ---
const galleryItems = {
    'comercial': [
        { id: 1, title: 'Campaña Nike Air', category: 'Publicidad', img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=800' },
        { id: 2, title: 'Botella Eco 360', category: 'Producto', img: 'https://images.unsplash.com/photo-1602143407151-11115cd4e69b?auto=format&fit=crop&q=80&w=800' },
        { id: 3, title: 'Joyería Virtual', category: 'E-commerce', img: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&q=80&w=800' },
        { id: 31, title: 'Cosméticos Luxury', category: 'CGI', img: 'https://images.unsplash.com/photo-1618331835717-801e976710b2?auto=format&fit=crop&q=80&w=800' }
    ],
    'arte': [
        { id: 4, title: 'El Pensador Digital', category: 'Escultura', img: 'https://images.unsplash.com/photo-1558865869-c93f6f8482af?auto=format&fit=crop&q=80&w=800' },
        { id: 5, title: 'Cyberpunk Figure', category: 'Coleccionable', img: 'https://images.unsplash.com/photo-1606041011872-596597976b25?auto=format&fit=crop&q=80&w=800' },
        { id: 6, title: 'Busto Clásico', category: 'Restauración', img: 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=800' },
        { id: 61, title: 'Neon Abstract', category: 'Arte 3D', img: 'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&q=80&w=800' }
    ],
    'casos': [
        { id: 7, title: 'Prótesis Biomimética', category: 'Medicina', img: 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&q=80&w=800' },
        { id: 8, title: 'Templo Maya', category: 'Patrimonio', img: 'https://images.unsplash.com/photo-1599593256050-13f570087c53?auto=format&fit=crop&q=80&w=800' },
        { id: 9, title: 'Ingeniería Inversa', category: 'Industrial', img: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=800' }
    ]
};

// --- Configuración de Tema (Simplificado para Tabs) ---
const themeOverrides = {
    Tabs: {
        // Personalización sutil solo para colores y radios
        tabBorderRadius: '9999px',
        railColorSegment: '#f1f5f9',
        tabColorActiveSegment: '#ffffff',
        tabTextColorActiveSegment: '#2f4b59',
        fontWeightStrong: '700'
    }
};

// --- Métodos ---
const openLightbox = (item) => {
    selectedItem.value = item;
    showModal.value = true;
};

// --- Ciclo de Vida ---
onMounted(() => {
    setTimeout(() => { showContent.value = true; }, 100);
});

// Sincronizar tabs con URL
watch(activeTab, (newVal) => {
    if (newVal !== props.category) {
        router.visit(`/portafolio/${newVal}`, { preserveState: true, replace: true });
    }
});

watch(() => props.category, (newVal) => {
    if (newVal) activeTab.value = newVal;
});
</script>

<template>
    <AppLayoutLanding>
        <Head title="Portafolio" />

        <NConfigProvider :theme-overrides="themeOverrides">
            <div class="min-h-screen bg-slate-50 relative overflow-hidden">
                
                <!-- Fondo Decorativo -->
                <div class="absolute top-0 left-0 w-full h-[50vh] bg-gradient-to-b from-[#e0f2fe] to-slate-50 opacity-60 pointer-events-none"></div>
                <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-[#4cc9f0] opacity-10 blur-[120px] rounded-full pointer-events-none animate-pulse-slow"></div>
                <div class="absolute top-40 -left-20 w-[400px] h-[400px] bg-[#2f4b59] opacity-5 blur-[100px] rounded-full pointer-events-none"></div>

                <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
                    
                    <!-- 1. Encabezado Animado -->
                    <div class="text-center mb-12 transition-all duration-1000 transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <h2 class="text-[#4cc9f0] font-bold tracking-[0.2em] uppercase text-xs md:text-sm mb-3">Galería Visual</h2>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-[#2f4b59] mb-6 tracking-tight">
                            Nuestro Portafolio
                        </h1>
                        <p class="text-slate-500 text-lg max-w-2xl mx-auto leading-relaxed">
                            Una muestra de precisión, arte y tecnología. Descubre cómo transformamos lo físico en digital.
                        </p>
                    </div>

                    <!-- 2. Tabs Estilizados (Simplificados) -->
                    <div class="flex justify-center mb-12 transition-all duration-1000 delay-200 transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <!-- Contenedor simple sin estilos extra, confiando en NTabs -->
                        <n-tabs 
                            v-model:value="activeTab" 
                            type="segment" 
                            animated 
                            class="w-full max-w-md"
                            :pane-style="{ display: 'none' }" 
                        >
                            <n-tab-pane name="comercial" tab="Comercial" />
                            <n-tab-pane name="arte" tab="Arte & Figuras" />
                            <n-tab-pane name="casos" tab="Casos de Éxito" />
                        </n-tabs>
                    </div>

                    <!-- 3. Grid de Imágenes -->
                    <!-- Cambiado: grid-cols-2 por defecto para móvil -->
                    <transition-group 
                        tag="div" 
                        class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-8"
                        enter-active-class="transition duration-700 ease-[cubic-bezier(0.25,0.8,0.25,1)]"
                        enter-from-class="opacity-0 translate-y-20 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                        leave-active-class="absolute opacity-0 scale-90"
                        move-class="transition duration-500"
                    >
                        <div v-for="(item, index) in galleryItems[activeTab]" 
                             :key="item.id" 
                             class="group relative rounded-2xl overflow-hidden aspect-[3/4] shadow-md hover:shadow-xl cursor-pointer bg-slate-200"
                             @click="openLightbox(item)"
                             :style="{ transitionDelay: `${index * 50}ms` }"
                        >
                            <!-- Imagen con transición suave -->
                            <img 
                                :src="item.img" 
                                :alt="item.title" 
                                class="w-full h-full object-cover transition-all duration-700 ease-out group-hover:scale-110 group-hover:brightness-110"
                            >
                            
                            <!-- Overlay Gradiente -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-4 md:p-6">
                                
                                <span class="text-[#4cc9f0] text-[10px] md:text-xs font-bold uppercase tracking-wider translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-75">
                                    {{ item.category }}
                                </span>
                                
                                <h3 class="text-white text-lg md:text-xl font-bold mt-1 translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-100 leading-tight">
                                    {{ item.title }}
                                </h3>
                                
                                <div class="mt-3 flex items-center gap-2 text-white/90 translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-150 group-active:scale-95 origin-left">
                                    <div class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </div>
                                    <span class="text-xs font-medium">Ver imagen</span>
                                </div>
                            </div>
                        </div>
                    </transition-group>

                    <!-- Mensaje si no hay items -->
                    <div v-if="galleryItems[activeTab].length === 0" class="text-center py-20 text-slate-400">
                        <p>No hay proyectos en esta categoría aún.</p>
                    </div>

                </div>

                <!-- 4. MODAL LIGHTBOX (Imagen Grande) -->
                <n-modal v-model:show="showModal" class="bg-transparent shadow-none w-full max-w-6xl mx-auto p-4 outline-none" transform-origin="center">
                    <div class="relative w-full flex flex-col items-center justify-center outline-none" @click.stop>
                        
                        <!-- Botón cerrar flotante -->
                        <button 
                            @click="showModal = false" 
                            class="absolute -top-12 right-0 md:right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md text-white flex items-center justify-center transition-all z-50 focus:outline-none"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div v-if="selectedItem" class="relative rounded-xl overflow-hidden shadow-2xl animate-fade-in-scale max-h-[85vh]">
                            <img :src="selectedItem.img" :alt="selectedItem.title" class="w-auto h-auto max-h-[85vh] max-w-full object-contain bg-black/50" />
                            
                            <!-- Caption opcional en el modal -->
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white backdrop-blur-[2px]">
                                <h3 class="text-xl font-bold">{{ selectedItem.title }}</h3>
                                <p class="text-sm text-slate-300">{{ selectedItem.category }}</p>
                            </div>
                        </div>

                    </div>
                </n-modal>

            </div>
        </NConfigProvider>
    </AppLayoutLanding>
</template>

<style scoped>
.animate-pulse-slow {
    animation: pulse-slow 6s infinite ease-in-out;
}
@keyframes pulse-slow {
    0%, 100% { opacity: 0.1; transform: scale(1); }
    50% { opacity: 0.2; transform: scale(1.1); }
}
/* Animación simple para el modal */
@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in-scale {
    animation: fadeInScale 0.3s ease-out forwards;
}
</style>