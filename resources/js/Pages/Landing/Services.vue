<script>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, Link } from '@inertiajs/vue3';
import { NTabs, NTabPane, NCard } from 'naive-ui';

export default {
    name: 'ServicesView',
    components: {
        AppLayoutLanding,
        Head,
        Link,
        NTabs,
        NTabPane,
        NCard
    },
    props: {
        category: {
            type: String,
            default: 'escaneo-personas'
        }
    },
    data() {
        return {
            activeTab: this.category || 'escaneo-personas',
            services: {
                'escaneo-personas': {
                    title: 'Escaneo 3D de Personas',
                    subtitle: 'Inmortaliza momentos con precisión milimétrica',
                    content: 'Nuestra cabina de fotogrametría captura instantáneamente la geometría y textura de personas y mascotas. Ideal para figuras personalizadas, avatares digitales y recuerdos familiares.',
                    features: ['Captura instantánea (1/60 seg)', 'Texturas 8K', 'Archivos listos para impresión 3D'],
                    image: 'https://images.unsplash.com/photo-1555664424-778a69032054?auto=format&fit=crop&q=80&w=1000'
                },
                'impresion': {
                    title: 'Impresión 3D Full Color',
                    subtitle: 'Tu gemelo digital en el mundo real',
                    content: 'Utilizamos tecnología PolyJet y Binder Jetting para crear figuras a todo color con un realismo impresionante. Desde miniaturas de 10cm hasta bustos a escala real.',
                    features: ['Más de 10 millones de colores', 'Materiales duraderos', 'Acabado profesional'],
                    image: 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'
                },
                'modelado': {
                    title: 'Modelado y Escultura Digital',
                    subtitle: 'Arte digital sin límites',
                    content: 'Nuestro equipo de artistas digitales puede reparar escaneos, modelar personajes desde cero o crear activos optimizados para videojuegos y metaversos.',
                    features: ['Retopología', 'Rigging y Animación', 'Optimización para Web/AR'],
                    image: 'https://images.unsplash.com/photo-1617791160505-6f00504e3519?auto=format&fit=crop&q=80&w=1000'
                },
                'eventos': {
                    title: 'Eventos y Bodas',
                    subtitle: 'Una experiencia tecnológica inolvidable',
                    content: 'Llevamos nuestro escáner móvil a tu boda o evento corporativo. Los invitados pueden ser escaneados y verse en 3D al instante, recibiendo su avatar digital como recuerdo.',
                    features: ['Escáner móvil', 'Visualización en tiempo real', 'Experiencia interactiva'],
                    image: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=1000'
                }
            }
        }
    },
    watch: {
        // Si el prop cambia (navegación por URL), actualizamos el tab activo
        category(newVal) {
            if (newVal && this.services[newVal]) {
                this.activeTab = newVal;
            }
        },
        // Si cambiamos el tab manualmente, actualizamos la URL sin recargar
        activeTab(newVal) {
            if (newVal !== this.category) {
                this.$inertia.visit(`/servicios/${newVal}`, {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true
                });
            }
        }
    }
}
</script>

<template>
    <AppLayoutLanding>
        <Head title="Nuestros Servicios" />

        <div class="max-w-7xl mx-auto px-6 py-12 min-h-[80vh]">
            <h1 class="text-4xl font-bold text-[#2f4b59] mb-8 text-center">Nuestros Servicios</h1>

            <n-card :bordered="false" content-style="padding: 0;" class="bg-transparent shadow-none">
                <n-tabs 
                    v-model:value="activeTab" 
                    type="segment" 
                    animated
                    class="custom-tabs"
                    pane-style="padding-top: 40px;"
                >
                    <n-tab-pane name="escaneo-personas" tab="Escaneo de Personas" />
                    <n-tab-pane name="impresion" tab="Impresión 3D" />
                    <n-tab-pane name="modelado" tab="Modelado Digital" />
                    <n-tab-pane name="eventos" tab="Eventos" />
                </n-tabs>

                <!-- Contenido Dinámico -->
                <div class="mt-8 bg-white rounded-3xl p-8 md:p-12 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row gap-12 items-center">
                    
                    <!-- Texto -->
                    <div class="flex-1 space-y-6">
                        <div class="inline-block px-4 py-1 rounded-full bg-blue-50 text-[#4cc9f0] text-sm font-bold uppercase tracking-wider">
                            {{ services[activeTab].title }}
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-[#2f4b59]">
                            {{ services[activeTab].subtitle }}
                        </h2>
                        <p class="text-lg text-slate-600 leading-relaxed">
                            {{ services[activeTab].content }}
                        </p>
                        
                        <ul class="space-y-3 pt-4">
                            <li v-for="feature in services[activeTab].features" :key="feature" class="flex items-center gap-3 text-slate-700">
                                <svg class="w-5 h-5 text-[#4cc9f0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                {{ feature }}
                            </li>
                        </ul>

                        <div class="pt-6">
                            <button class="bg-[#2f4b59] text-white px-8 py-3 rounded-xl hover:bg-[#3e6070] transition-colors shadow-lg shadow-[#2f4b59]/20">
                                Cotizar Servicio
                            </button>
                        </div>
                    </div>

                    <!-- Imagen Decorativa -->
                    <div class="flex-1 w-full relative group">
                        <div class="absolute inset-0 bg-[#4cc9f0] rounded-3xl rotate-3 opacity-20 group-hover:rotate-6 transition-transform duration-500"></div>
                        <div class="relative rounded-3xl overflow-hidden aspect-[4/3] shadow-lg">
                            <img :src="services[activeTab].image" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Servicio">
                            <!-- Overlay Gradiente -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#2f4b59]/60 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 text-white font-medium">
                                ION3D Studio
                            </div>
                        </div>
                    </div>

                </div>
            </n-card>
        </div>
    </AppLayoutLanding>
</template>

<style scoped>
:deep(.n-tabs-rail) {
    background-color: #f1f5f9;
    border-radius: 9999px;
    padding: 4px;
}
:deep(.n-tabs-tab) {
    border-radius: 9999px;
    font-weight: 600;
    transition: all 0.3s;
}
:deep(.n-tabs-tab--active) {
    background-color: white !important;
    color: #2f4b59 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>