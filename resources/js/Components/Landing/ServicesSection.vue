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
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900">Soluciones Integrales 3D</h3>
            </div>

            <!-- Grid de Servicios -->
            <div class="grid md:grid-cols-3 gap-8">
                <div v-for="(service, idx) in services" :key="service.id" 
                     class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-[#4cc9f0]/30 hover:shadow-2xl hover:shadow-[#2f4b59]/10 transition-all duration-500 cursor-default relative overflow-hidden flex flex-col">
                    
                    <!-- Fondo gradiente al hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <!-- Icono / Imagen Miniatura -->
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 border border-slate-100 overflow-hidden transition-all duration-300 relative z-10 text-[#2f4b59]">
                        <!-- Si hay imagen en BD, la mostramos -->
                        <img v-if="service.image" :src="service.image" :alt="service.title" class="w-full h-full object-cover" />
                        
                        <!-- Fallback: Icono genérico si no hay imagen -->
                        <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>

                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">{{ service.title }}</h4>
                    <p class="text-slate-500 leading-relaxed mb-6 flex-grow relative z-10 group-hover:text-slate-600 transition-colors line-clamp-3">
                        {{ service.desc }}
                    </p>

                    <!-- Botón "Ver Detalles" (Solicitado) -->
                    <div class="relative z-10 mt-auto">
                        <Link :href="route('landing.services', service.slug)" 
                              class="inline-flex items-center text-sm font-bold text-[#4cc9f0] hover:text-[#2f4b59] transition-colors gap-2">
                            Ver detalles
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </Link>
                    </div>

                    <!-- Flecha decorativa fondo -->
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-10 pointer-events-none transform translate-x-4 group-hover:translate-x-0 transition-all duration-500 text-[#4cc9f0]">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
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