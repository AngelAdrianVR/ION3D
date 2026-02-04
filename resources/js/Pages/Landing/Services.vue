<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head } from '@inertiajs/vue3';
import { NConfigProvider } from 'naive-ui';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    packages: {
        type: Array,
        default: () => []
    },
    initialCategory: {
        type: String,
        default: null
    }
});

// --- Estado Básico ---
const initialSlug = props.initialCategory && props.packages.find(p => p.slug === props.initialCategory)
    ? props.initialCategory
    : (props.packages.length > 0 ? props.packages[0].slug : null);

const activeSlug = ref(initialSlug);
const showContent = ref(false);

// --- Lógica del Carrusel ---
const currentImageIndex = ref(0);
let carouselInterval = null;

const currentPackage = computed(() => {
    return props.packages.find(p => p.slug === activeSlug.value) || props.packages[0] || null;
});

// Imagen actual basada en el índice y el paquete seleccionado
const activeImage = computed(() => {
    if (!currentPackage.value || !currentPackage.value.images || currentPackage.value.images.length === 0) {
        return currentPackage.value?.image_fallback;
    }
    return currentPackage.value.images[currentImageIndex.value];
});

const startCarousel = () => {
    stopCarousel(); // Limpiar existente
    if (currentPackage.value && currentPackage.value.images && currentPackage.value.images.length > 1) {
        carouselInterval = setInterval(() => {
            currentImageIndex.value = (currentImageIndex.value + 1) % currentPackage.value.images.length;
        }, 3000); // 3 Segundos
    }
};

const stopCarousel = () => {
    if (carouselInterval) clearInterval(carouselInterval);
};

// Reiniciar carrusel al cambiar de paquete
watch(activeSlug, () => {
    currentImageIndex.value = 0;
    startCarousel();
});

// --- Lógica de Navegación ---
const selectPackage = (slug) => {
    activeSlug.value = slug;
    if (slug) {
        const url = `/servicios/${slug}`;
        window.history.replaceState({ path: url }, '', url);
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 0
    }).format(value);
};

// --- Ciclo de Vida ---
onMounted(() => {
    setTimeout(() => { showContent.value = true; }, 100);
    startCarousel();
});

onUnmounted(() => {
    stopCarousel();
});

const themeOverrides = {
    common: { primaryColor: '#0ea5e9' } // Cyan-500/Sky-500 para contrastar en blanco
};
</script>

<template>
    <AppLayoutLanding>
        <Head title="Nuestros Servicios" />

        <NConfigProvider :theme-overrides="themeOverrides">
            <!-- Fondo Claro "Clean Tech" -->
            <div class="min-h-screen bg-slate-50 py-12 md:py-20 relative overflow-hidden font-sans">
                
                <!-- Elementos de Fondo (Grid y Luces Suaves) -->
                <div class="absolute inset-0 pointer-events-none">
                    <!-- Grid sutil grisáceo -->
                    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 mix-blend-multiply"></div>
                    <!-- Luces ambientales (Cyan y Azul) más suaves -->
                    <div class="absolute top-[-20%] right-[-10%] w-[600px] h-[600px] bg-sky-200/40 blur-[100px] rounded-full animate-pulse-slow"></div>
                    <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-cyan-200/30 blur-[100px] rounded-full"></div>
                </div>

                <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                    
                    <!-- ENCABEZADO -->
                    <div class="text-center transition-all duration-1000 ease-out transform mb-10"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <h1 class="text-4xl md:text-6xl font-black text-slate-800 mb-4 tracking-tight drop-shadow-sm">
                            SOLUCIONES <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">ION3D</span>
                        </h1>
                        <p class="text-slate-500 max-w-2xl mx-auto text-lg font-light">
                            Tecnología de escaneo e impresión llevada al siguiente nivel.
                        </p>
                    </div>

                    <!-- NAVEGACIÓN (Pills Tecnológicas Claras) -->
                    <div v-if="packages.length > 0" 
                         class="mb-12 flex overflow-x-auto pb-6 gap-4 no-scrollbar items-center md:justify-center px-2 snap-x transition-all duration-1000 delay-200 ease-out transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        
                        <button 
                            v-for="pkg in packages" 
                            :key="pkg.id"
                            @click="selectPackage(pkg.slug)"
                            class="whitespace-nowrap px-6 py-2.5 rounded-full text-sm font-bold tracking-wide transition-all duration-300 snap-center border shrink-0 backdrop-blur-md shadow-sm"
                            :class="activeSlug === pkg.slug 
                                ? 'bg-slate-800 text-white border-slate-800 shadow-lg shadow-slate-500/20 scale-105' 
                                : 'bg-white text-slate-500 border-slate-200 hover:border-cyan-400 hover:text-cyan-600 hover:bg-white'"
                        >
                            {{ pkg.title }}
                        </button>

                    </div>
                    
                    <!-- ESTADO VACÍO -->
                    <div v-else class="text-center py-20 text-slate-400">
                        <p>Iniciando sistemas...</p>
                    </div>

                    <!-- CONTENIDO DEL PAQUETE (Tarjeta Tech Clean) -->
                    <div v-if="currentPackage" class="transition-all duration-1000 delay-300 ease-out transform perspective-1000"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        
                        <transition mode="out-in" enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-300 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                            
                            <!-- Fondo Blanco con efecto cristal sutil -->
                            <div :key="currentPackage.id" class="grid lg:grid-cols-12 bg-white border border-slate-100 rounded-[2rem] overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] min-h-[600px]">
                                
                                <!-- COLUMNA INFO (Izquierda) -->
                                <div class="lg:col-span-7 p-8 md:p-12 lg:p-14 flex flex-col justify-center relative">
                                    
                                    <!-- Header de la Card -->
                                    <div class="mb-8 border-b border-slate-100 pb-6">
                                        <div v-if="currentPackage.is_promo" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-full mb-4 shadow-md shadow-orange-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            Oferta Limitada
                                        </div>
                                        <h2 class="text-3xl md:text-5xl font-black text-slate-900 mb-3 leading-tight">
                                            {{ currentPackage.title }}
                                        </h2>
                                        <p class="text-cyan-600 font-mono text-sm md:text-base uppercase tracking-widest font-bold">
                                            // {{ currentPackage.subtitle }}
                                        </p>
                                    </div>

                                    <p class="text-slate-600 text-lg leading-relaxed mb-8 font-normal">
                                        {{ currentPackage.description }}
                                    </p>

                                    <!-- Lista de Características (Estilo Check) -->
                                    <div v-if="currentPackage.features && currentPackage.features.length > 0" class="mb-10">
                                        <ul class="space-y-4">
                                            <li v-for="(feature, idx) in currentPackage.features" :key="idx" class="flex items-center gap-4 group">
                                                <!-- Icono Check Tech -->
                                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 border border-emerald-200 group-hover:bg-emerald-200 group-hover:scale-110 transition-all duration-300">
                                                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                                <span class="text-slate-700 text-base md:text-lg border-b border-slate-100 pb-1 w-full group-hover:border-slate-300 transition-colors">
                                                    {{ feature.value || feature }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- PRECIOS (Estilo Sticker/Tag Contraste) -->
                                    <div v-if="currentPackage.prices && currentPackage.prices.length > 0" class="mb-8">
                                        <div class="flex flex-wrap gap-4">
                                            <div v-for="(priceItem, pIdx) in currentPackage.prices" :key="pIdx" 
                                                 class="relative group bg-slate-50 text-slate-800 px-4 py-3 rounded-xl shadow-[3px_3px_0px_#94a3b8] hover:translate-y-[-2px] hover:shadow-[5px_5px_0px_#0ea5e9] transition-all cursor-default border border-slate-200 transform rotate-[-1deg] hover:rotate-0">
                                                
                                                <!-- Label Pequeño -->
                                                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-0.5">
                                                    {{ priceItem.label }}
                                                </div>
                                                <!-- Precio Grande -->
                                                <div class="text-xl md:text-2xl font-black text-slate-900">
                                                    {{ formatCurrency(priceItem.price) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón de Acción -->
                                    <div>
                                        <button @click="$inertia.visit(route('landing.contact'))" class="w-full md:w-auto bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-10 rounded-xl shadow-lg shadow-slate-400/30 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group">
                                            <span>SOLICITAR AHORA</span>
                                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </button>
                                    </div>

                                </div>

                                <!-- COLUMNA IMAGEN (Derecha - Carrusel) -->
                                <div class="lg:col-span-5 relative min-h-[300px] lg:min-h-full bg-slate-100 overflow-hidden group">
                                    
                                    <!-- Transición de imagen (Fade) -->
                                    <transition name="fade-image" mode="in-out">
                                        <img 
                                            :key="activeImage" 
                                            :src="activeImage" 
                                            :alt="currentPackage.title"
                                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-[3000ms] ease-linear scale-100 group-hover:scale-110"
                                            style="transform-origin: center center;"
                                        >
                                    </transition>
                                    
                                    <!-- Overlay Degradado Lateral (Blanco para mezclar con la card) -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-white/30 via-transparent to-transparent lg:bg-gradient-to-l lg:from-white/30 lg:via-white/10 lg:to-transparent"></div>
                                    
                                    <!-- Indicadores de Carrusel -->
                                    <div v-if="currentPackage.images && currentPackage.images.length > 1" class="absolute bottom-6 right-6 flex gap-2 z-20">
                                        <span v-for="(_, idx) in currentPackage.images" :key="idx" 
                                              class="block h-1.5 rounded-full transition-all duration-300 shadow-sm"
                                              :class="idx === currentImageIndex ? 'w-8 bg-cyan-500' : 'w-2 bg-white'">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>

                </div>
            </div>
        </NConfigProvider>
    </AppLayoutLanding>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Animación Carrusel Suave */
.fade-image-enter-active,
.fade-image-leave-active {
  transition: opacity 1s ease;
}
.fade-image-enter-from,
.fade-image-leave-to {
  opacity: 0;
}

.perspective-1000 {
    perspective: 1000px;
}

.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>