<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, router } from '@inertiajs/vue3';
import { NTabs, NTabPane, NConfigProvider } from 'naive-ui';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    category: {
        type: String,
        default: 'impresion'
    }
});

const activeTab = ref(props.category || 'impresion');
const showContent = ref(false); // Control para animaciones de entrada

// --- Datos Actualizados ---
const services = {
    'impresion': {
        title: 'Paquete Impresión 3D',
        subtitle: 'Tu gemelo digital en el mundo real',
        description: 'Obtén una réplica digital exacta de ti, lista para convertirla en una miniatura personalizada, un regalo único o una pieza decorativa.',
        includes: [
            'Escaneo 3D de Cuerpo Completo (360°)',
            'Modelo optimizado y limpio',
            'Archivo digital (OBJ/STL)',
            'Impresión física en resina/filamento'
        ],
        prices: [
            { label: '10 cm', price: '$2,000.00' },
            { label: '15 cm', price: '$3,000.00' },
            { label: '20 cm', price: '$4,500.00' }
        ],
        image: 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000',
        badge: 'Más Vendido'
    },
    'escaneo-personas': {
        title: 'Paquete Escaneo 3D',
        subtitle: 'Digitalización profesional',
        description: 'Escaneo 3D de cuerpo completo con captura de proporciones, postura y detalles físicos. Ideal si solo necesitas el archivo digital.',
        includes: [
            'Captura de proporciones y detalles',
            'Archivo digital estándar listo para usar',
            'Ajuste básico (limpieza y mallado)',
            'Entrega por enlace privado'
        ],
        prices: [
            { label: 'Busto / Rostro', price: '$1,250.00' },
            { label: 'Medio Cuerpo', price: '$1,500.00' },
            { label: 'Cuerpo Completo', price: '$2,500.00' }
        ],
        image: 'https://images.unsplash.com/photo-1555664424-778a69032054?auto=format&fit=crop&q=80&w=1000'
    },
    'modelo': {
        title: 'Paquete Modelo 3D (Personalizado)',
        subtitle: 'Lleva tu figura al siguiente nivel',
        description: 'Además de tu réplica, podrás personalizarla con nombre, fecha, base especial o accesorios únicos que reflejen tu estilo.',
        includes: [
            'Bases personalizadas (nombre/fecha)',
            'Accesorios adicionales (lentes, balones, etc)',
            'Escenarios temáticos (Navidad, Gamer, etc)',
            'Opción de iluminación LED'
        ],
        prices: [
            { label: 'Figura 10cm + Custom', price: '$2,500.00' },
            { label: 'Figura 15cm + Custom', price: '$3,500.00' },
            { label: 'Figura 20cm + Custom', price: '$5,000.00' }
        ],
        image: 'https://images.unsplash.com/photo-1617791160505-6f00504e3519?auto=format&fit=crop&q=80&w=1000'
    },
    'head-pro': {
        title: 'Paquete Head Pro',
        subtitle: 'Impresión Funcional',
        description: 'Escaneo 3D de alta precisión del rostro convertido en un accesorio práctico (soporte para lentes, audífonos, gorras). Útil y original.',
        includes: [
            'Escaneo de alta precisión de cabeza',
            'Archivo digital optimizado',
            'Diseño funcional para soporte',
            'Impresión resistente'
        ],
        prices: [
            { label: 'Tamaño Real', price: '$2,500.00' }
        ],
        image: 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=1000'
    },
    'escenario': {
        title: 'Paquete Escenarios',
        subtitle: 'Ambientaciones Temáticas',
        description: 'Complementa tu figura con escenarios 3D: Navidad, Halloween, Deportes, Gamer o cualquier concepto especial.',
        includes: [
            'Diseño de entorno temático',
            'Integración con tu figura',
            'Detalles pintados a mano (opcional)',
            'Base ambientada'
        ],
        prices: [
            { label: 'Set Completo', price: '$5,000.00' }
        ],
        image: 'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&q=80&w=1000'
    },
    'eventos': {
        title: 'Paquete Eventos',
        subtitle: 'Experiencia en vivo',
        description: 'Llevamos la estación de escaneo a tu evento. Los asistentes obtienen su modelo 3D de manera rápida y divertida.',
        includes: [
            'Estación profesional móvil',
            'Recuerdos personalizados',
            'Visualización en tiempo real',
            'Opción de escaneos previos'
        ],
        prices: [
            { label: 'Servicio por Evento', price: '$30,000.00' }
        ],
        image: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=1000'
    },
    'recreacion': {
        title: 'Diseño & Modelado (Sin Escáner)',
        subtitle: 'Recreación 3D desde Foto',
        description: '¿No puedes venir al estudio? Transformamos una fotografía clara en un modelo 3D detallado listo para imprimir.',
        includes: [
            'Modelado artístico desde referencia 2D',
            'Optimización para impresión',
            'Archivo digital',
            'Impresión 3D opcional'
        ],
        prices: [
            { label: 'Proyecto desde', price: '$20,000.00' }
        ],
        image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=1000'
    }
};

// --- Configuración de Tema (Corregido: Fondo Blanco, Texto Oscuro) ---
const themeOverrides = {
    Tabs: {
        tabBorderRadius: '9999px',
        railColorSegment: '#f1f5f9',
        // Estado Activo: Fondo Blanco, Texto Azul Oscuro (Alto Contraste)
        tabColorActiveSegment: '#ffffff',
        tabTextColorActiveSegment: '#2f4b59',
        // Estado Inactivo
        tabTextColorSegment: '#64748b',
        fontWeightStrong: '700',
        fontSizeMedium: '14px'
    }
};

// --- Ciclo de Vida ---
onMounted(() => {
    // Activar animaciones al montar
    setTimeout(() => { showContent.value = true; }, 100);
});

// --- Lógica de Navegación ---
watch(activeTab, (newVal) => {
    if (newVal !== props.category) {
        router.visit(`/servicios/${newVal}`, { preserveScroll: true, replace: true });
    }
});

watch(() => props.category, (newVal) => {
    if (newVal && services[newVal]) activeTab.value = newVal;
});
</script>

<template>
    <AppLayoutLanding>
        <Head title="Nuestros Servicios" />

        <NConfigProvider :theme-overrides="themeOverrides">
            <div class="min-h-screen bg-slate-50 py-12 md:py-20 relative overflow-hidden">
                
                <!-- Fondo Decorativo Sutil -->
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-40">
                    <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-[#4cc9f0]/10 blur-[100px] rounded-full"></div>
                    <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-[#2f4b59]/5 blur-[100px] rounded-full"></div>
                </div>

                <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                    
                    <!-- ENCABEZADO: Animación de Entrada -->
                    <div class="text-center transition-all duration-1000 ease-out transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <h1 class="text-3xl md:text-5xl font-bold text-[#2f4b59] mb-8 tracking-tight">
                            Soluciones <span class="text-[#4cc9f0]">ION3D</span>
                        </h1>
                    </div>

                    <!-- TABS: Animación de Entrada (Delay) -->
                    <div class="mb-10 overflow-x-auto pb-4 no-scrollbar flex justify-center transition-all duration-1000 delay-200 ease-out transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <n-tabs 
                            v-model:value="activeTab" 
                            type="segment" 
                            animated
                            class="min-w-[700px] md:min-w-0 md:w-full md:max-w-4xl shadow-sm rounded-full p-1 bg-slate-100" 
                        >
                            <n-tab-pane name="impresion" tab="Impresión 3D" />
                            <n-tab-pane name="escaneo-personas" tab="Escaneo" />
                            <n-tab-pane name="modelo" tab="Personalizado" />
                            <n-tab-pane name="head-pro" tab="Head Pro" />
                            <n-tab-pane name="escenario" tab="Escenarios" />
                            <n-tab-pane name="eventos" tab="Eventos" />
                            <n-tab-pane name="recreacion" tab="Diseño 3D" />
                        </n-tabs>
                    </div>

                    <!-- CONTENIDO DEL SERVICIO: Animación de Entrada (Delay Mayor) -->
                    <div class="transition-all duration-1000 delay-300 ease-out transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        
                        <transition 
                            mode="out-in" 
                            enter-active-class="transition duration-500 ease-out" 
                            enter-from-class="opacity-0 translate-y-4" 
                            enter-to-class="opacity-100 translate-y-0" 
                            leave-active-class="transition duration-300 ease-in" 
                            leave-from-class="opacity-100 translate-y-0" 
                            leave-to-class="opacity-0 -translate-y-4"
                        >
                            <div :key="activeTab" class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100">
                                
                                <div class="grid lg:grid-cols-2 gap-0">
                                    
                                    <!-- Columna Izquierda: Información -->
                                    <div class="p-8 md:p-12 lg:p-16 flex flex-col justify-center order-2 lg:order-1">
                                        
                                        <div class="mb-6">
                                            <div v-if="services[activeTab].badge" class="inline-block px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-full mb-3 animate-fade-in">
                                                {{ services[activeTab].badge }}
                                            </div>
                                            <h2 class="text-3xl md:text-4xl font-extrabold text-[#2f4b59] mb-2 leading-tight">
                                                {{ services[activeTab].title }}
                                            </h2>
                                            <p class="text-[#4cc9f0] font-medium text-lg">{{ services[activeTab].subtitle }}</p>
                                        </div>

                                        <p class="text-slate-600 text-lg leading-relaxed mb-8">
                                            {{ services[activeTab].description }}
                                        </p>

                                        <!-- Lista de Incluidos -->
                                        <div class="mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                            <h4 class="font-bold text-[#2f4b59] mb-4 text-sm uppercase tracking-wide">¿Qué incluye este paquete?</h4>
                                            <ul class="space-y-3">
                                                <li v-for="(feature, idx) in services[activeTab].includes" :key="idx" class="flex items-start gap-3 text-slate-700">
                                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center shrink-0 mt-0.5">
                                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    </div>
                                                    <span class="text-sm md:text-base">{{ feature }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- TABLA DE PRECIOS -->
                                        <div class="space-y-3">
                                            <h4 class="font-bold text-[#2f4b59] mb-2 text-sm uppercase tracking-wide">Opciones y Precios</h4>
                                            <div v-for="(priceItem, pIdx) in services[activeTab].prices" :key="pIdx" 
                                                 class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:border-[#4cc9f0] hover:bg-blue-50/30 transition-colors group cursor-default">
                                                <span class="font-medium text-slate-700">{{ priceItem.label }}</span>
                                                <span class="font-bold text-[#2f4b59] text-lg">{{ priceItem.price }}</span>
                                            </div>
                                        </div>

                                        <!-- Botón de Acción -->
                                        <div class="mt-8 pt-4 border-t border-slate-100">
                                            <button class="w-full md:w-auto bg-[#2f4b59] hover:bg-[#3e6070] text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-[#2f4b59]/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                                <span>Solicitar Cotización</span>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </button>
                                        </div>

                                    </div>

                                    <!-- Columna Derecha: Imagen -->
                                    <div class="relative min-h-[300px] lg:min-h-full order-1 lg:order-2 bg-slate-200 overflow-hidden group">
                                        <img 
                                            :src="services[activeTab].image" 
                                            :alt="services[activeTab].title"
                                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                        >
                                        <!-- Gradiente Decorativo -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#2f4b59]/80 via-transparent to-transparent lg:bg-gradient-to-l lg:from-[#2f4b59]/50"></div>
                                        
                                        <!-- Texto Flotante en Imagen -->
                                        <div class="absolute bottom-6 left-6 right-6 text-white lg:hidden">
                                            <p class="text-sm font-medium opacity-90 uppercase tracking-widest mb-1">ION3D Studio</p>
                                            <h3 class="text-2xl font-bold">{{ services[activeTab].title }}</h3>
                                        </div>
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
/* Ocultar scrollbar en contenedor de tabs móvil */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
</style>