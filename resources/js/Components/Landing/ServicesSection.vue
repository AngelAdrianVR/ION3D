<script setup>
import { Link } from '@inertiajs/vue3';

// Recibimos los servicios desde el Controller a través del componente padre
const props = defineProps({
    services: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <section class="py-24 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <h2 class="text-sm font-bold tracking-widest text-[#4cc9f0] uppercase mb-2">Nuestras Capacidades</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900">Soluciones Integrales</h3>
            </div>

            <!-- Grid de Servicios -->
            <div class="grid md:grid-cols-3 gap-8">
                <div v-for="(service, idx) in services" :key="service.id" 
                     class="group relative h-[400px] md:h-[450px] rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl hover:shadow-[#2f4b59]/20 transition-all duration-500 cursor-pointer flex flex-col">
                    
                    <!-- Video de fondo (ocupa toda la tarjeta, se reproduce en bucle) -->
                    <video v-if="service.video_url" :src="service.video_url" autoplay loop muted playsinline
                           class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"></video>
                    
                    <!-- Imagen de fondo (ocupa toda la tarjeta) -->
                    <img v-else-if="service.image" :src="service.image" :alt="service.title"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                    
                    <!-- Fondo fallback si no hay imagen ni video -->
                    <div v-else class="absolute inset-0 bg-gradient-to-br from-slate-700 to-slate-900"></div>

                    <!-- Overlay de oscuridad: invisible en desktop hasta hover, siempre visible en mobile -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent
                                opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-500"></div>

                    <!-- Overlay sutil base (solo desktop, se desvanece al hover) -->
                    <div class="absolute inset-0 bg-slate-900/10 hidden md:block group-hover:opacity-0 transition-opacity duration-500"></div>

                    <!-- Contenido de texto -->
                    <div class="relative z-10 flex flex-col justify-end h-full p-6 md:p-8">
                        
                        <!-- Info que se revela en hover (desktop) o siempre visible (mobile) -->
                        <div class="transform translate-y-0 md:translate-y-8 md:group-hover:translate-y-0 transition-all duration-500
                                    opacity-100 md:opacity-0 md:group-hover:opacity-100">
                            <!-- Icono pequeño decorativo (si no hay imagen ni video) -->
                            <div v-if="!service.image && !service.video_url" class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            
                            <p class="text-white/80 text-sm leading-relaxed mb-4 line-clamp-2">
                                {{ service.desc }}
                            </p>

                            <Link :href="route('landing.services', service.slug)" 
                                  class="inline-flex items-center text-sm font-bold text-[#4cc9f0] hover:text-white transition-colors gap-2">
                                Ver detalles
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </Link>
                        </div>

                        <!-- Título: siempre visible, en desktop sube ligeramente al hover -->
                        <div class="transform md:group-hover:-translate-y-2 transition-all duration-500 mt-4 md:mt-0">
                            <h4 class="text-xl md:text-2xl font-bold text-white drop-shadow-lg">
                                {{ service.title }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Global para ir a todos los servicios -->
            <div class="mt-12 text-center">
                <Link :href="route('landing.services')" class="inline-block px-8 py-3 rounded-full bg-slate-100 text-slate-600 font-bold hover:bg-[#4cc9f0] hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    Explorar todos los servicios
                </Link>
            </div>
        </div>
    </section>
</template>