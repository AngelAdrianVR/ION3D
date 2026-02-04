<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// Datos sincronizados con la nueva definición del proceso
const steps = [
  { 
    id: 1, 
    title: 'Agendar Cita', 
    subtitle: 'El primer paso',
    duration: 'Online • 5 min', 
    description: 'Selecciona el paquete ideal para ti y elige el horario que mejor te convenga en nuestro calendario en línea. Recibirás una confirmación inmediata con guía de vestimenta.',
    specs: ['Reserva 24/7', 'Asesoría Previa', 'Confirmación Instantánea'],
    image: 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 2, 
    title: 'Sesión de Escaneo', 
    subtitle: 'En el estudio',
    duration: 'Presencial • 30 min', 
    description: 'Ven a nuestra cabina fotogramétrica. 120 cámaras sincronizadas capturarán tu geometría en una fracción de segundo. Recomendamos usar ropa con texturas y evitar el negro absoluto.',
    specs: ['Sin Ropa Negra', 'Sin Materiales Brillantes', 'Captura en 1/1000s'],
    image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 3, 
    title: 'Impresión y Acabado', 
    subtitle: 'Laboratorio 3D',
    duration: 'Proceso • 3-5 Días', 
    description: 'Nuestros algoritmos de IA reconstruyen tu gemelo digital. Luego, lo imprimimos capa por capa en Full Color y nuestros artesanos realizan el curado y sellado manual para garantizar durabilidad.',
    specs: ['Impresión CJP/SLA', 'Millones de Colores', 'Post-proceso Manual'],
    image: 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 4, 
    title: 'Entrega Final', 
    subtitle: 'En tus manos',
    duration: 'Envío • Inmediata', 
    description: 'Tu figura está lista para ser exhibida. Puedes recogerla en nuestro estudio o solicitar el envío asegurado a tu domicilio en un empaque de protección premium.',
    specs: ['Empaque Seguro', 'Envío Nacional', 'Certificado Digital'],
    image: 'https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&q=80&w=1000'
  },
];

// Lógica de intersección para animaciones al hacer scroll
const stepRefs = ref([]);

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.2 });

    stepRefs.value.forEach((el) => {
        if (el) observer.observe(el);
    });
});
</script>

<template>
    <AppLayoutLanding>
        <Head title="Nuestro Proceso - ION3D" />

        <!-- Contenedor Principal Dark Tech -->
        <div class="min-h-screen bg-slate-900 text-white relative overflow-hidden font-sans selection:bg-[#4cc9f0] selection:text-slate-900">
            
            <!-- Fondos Ambientales Animados -->
            <div class="fixed inset-0 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[800px] h-[800px] bg-purple-600/10 rounded-full blur-[120px] animate-pulse-slow"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-[#4cc9f0]/10 rounded-full blur-[100px] animate-pulse-slow delay-1000"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-20 relative z-10">
                
                <!-- Encabezado Hero -->
                <div class="text-center mb-24 space-y-4 animate-fade-in-down">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#4cc9f0]/30 bg-[#4cc9f0]/10 text-[#4cc9f0] text-xs font-bold uppercase tracking-widest backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#4cc9f0] animate-ping"></span>
                        Workflow Digital
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-6">
                        De la Realidad <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#4cc9f0] to-purple-500">al Metaverso</span>
                    </h1>
                    <p class="text-xl text-slate-400 max-w-2xl mx-auto font-light leading-relaxed">
                        Un proceso de 4 pasos diseñado para capturar la esencia del momento con precisión milimétrica.
                    </p>
                </div>

                <!-- Timeline Vertical -->
                <div class="relative">
                    
                    <!-- Línea Central Conectora (Neon) -->
                    <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-800 transform -translate-x-1/2 hidden md:block">
                        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-[#4cc9f0] via-purple-500 to-transparent opacity-50"></div>
                    </div>

                    <div class="space-y-24 md:space-y-32">
                        <div v-for="(step, index) in steps" :key="step.id" 
                            ref="stepRefs"
                            class="group relative flex flex-col md:flex-row items-center gap-12 opacity-0 translate-y-20 transition-all duration-1000 ease-out"
                            :class="[
                                index % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse',
                                'step-item' 
                            ]">
                            
                            <!-- COLUMNA 1: Contenido Texto -->
                            <div class="flex-1 w-full text-center md:text-left" :class="index % 2 === 0 ? 'md:text-right' : 'md:text-left'">
                                
                                <div class="mb-4 inline-block">
                                    <span class="text-[#4cc9f0] font-mono text-sm tracking-widest uppercase border-b border-[#4cc9f0]/30 pb-1">
                                        {{ step.duration }}
                                    </span>
                                </div>
                                
                                <h3 class="text-4xl font-bold text-white mb-2 group-hover:text-[#4cc9f0] transition-colors duration-300">
                                    {{ step.title }}
                                </h3>
                                <p class="text-lg text-slate-500 mb-6 font-medium">{{ step.subtitle }}</p>
                                
                                <p class="text-slate-400 leading-relaxed text-lg mb-8">
                                    {{ step.description }}
                                </p>

                                <!-- Specs Pills -->
                                <div class="flex flex-wrap gap-2" :class="index % 2 === 0 ? 'justify-center md:justify-end' : 'justify-center md:justify-start'">
                                    <span v-for="spec in step.specs" :key="spec" 
                                          class="px-3 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs font-mono text-slate-300 group-hover:border-[#4cc9f0]/30 transition-colors">
                                        {{ spec }}
                                    </span>
                                </div>
                            </div>

                            <!-- COLUMNA CENTRAL: Nodo del Timeline -->
                            <div class="relative z-10 shrink-0 hidden md:flex items-center justify-center w-24">
                                <div class="w-16 h-16 rounded-full bg-slate-900 border-2 border-slate-700 flex items-center justify-center relative group-hover:border-[#4cc9f0] group-hover:shadow-[0_0_30px_rgba(76,201,240,0.3)] transition-all duration-500">
                                    <span class="text-2xl font-black text-slate-500 group-hover:text-white transition-colors">0{{ index + 1 }}</span>
                                    
                                    <!-- Orbit Animation -->
                                    <div class="absolute inset-[-4px] border border-[#4cc9f0]/0 rounded-full group-hover:border-[#4cc9f0]/50 group-hover:animate-spin-slow transition-all"></div>
                                </div>
                            </div>

                            <!-- COLUMNA 2: Imagen Visual -->
                            <div class="flex-1 w-full relative group/image perspective-1000">
                                <div class="relative h-[300px] md:h-[400px] rounded-3xl overflow-hidden shadow-2xl border border-white/10 transform transition-transform duration-700 group-hover:scale-[1.02] group-hover:rotate-1">
                                    
                                    <!-- Imagen -->
                                    <img :src="step.image" :alt="step.title" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                                    
                                    <!-- Overlays -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60"></div>
                                    <div class="absolute inset-0 bg-[#4cc9f0] opacity-0 group-hover:opacity-10 transition-opacity duration-500 mix-blend-overlay"></div>

                                    <!-- Decoración Tecnológica en la imagen -->
                                    <div class="absolute bottom-6 left-6 right-6 flex justify-between items-end opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                        <div class="text-xs font-mono text-[#4cc9f0] bg-slate-900/80 backdrop-blur px-3 py-1 rounded border border-[#4cc9f0]/30">
                                            STEP_0{{ index + 1 }} // EXEC
                                        </div>
                                    </div>
                                </div>

                                <!-- Sombra decorativa detrás -->
                                <div class="absolute -inset-4 bg-gradient-to-r from-[#4cc9f0] to-purple-600 rounded-[2rem] opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500 -z-10"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- CTA Final -->
                <div class="mt-40 text-center relative max-w-4xl mx-auto">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#4cc9f0] to-purple-600 opacity-20 blur-[100px] rounded-full"></div>
                    
                    <div class="relative bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-[3rem] p-12 md:p-20 overflow-hidden group">
                        <!-- Grid decorativo -->
                        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:40px_40px] opacity-20"></div>

                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 relative z-10">¿Listo para inmortalizarte?</h2>
                        <p class="text-slate-300 text-lg mb-10 max-w-xl mx-auto relative z-10">
                            La tecnología del futuro está al alcance de tus manos. Agenda hoy y sé parte de la revolución digital.
                        </p>
                        
                        <div class="relative z-10 flex flex-col sm:flex-row gap-4 justify-center">
                            <Link :href="route('landing.contact')" class="px-10 py-4 bg-[#4cc9f0] hover:bg-white text-slate-900 font-bold rounded-xl transition-all shadow-[0_0_20px_rgba(76,201,240,0.4)] hover:shadow-[0_0_40px_rgba(255,255,255,0.6)] transform hover:-translate-y-1">
                                Agendar Cita Ahora
                            </Link>
                            <Link :href="route('landing.services')" class="px-10 py-4 bg-transparent border border-white/20 hover:border-white text-white font-bold rounded-xl transition-all hover:bg-white/5">
                                Ver Paquetes
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayoutLanding>
</template>

<style scoped>
/* Animaciones CSS personalizadas */
.animate-pulse-slow {
    animation: pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-spin-slow {
    animation: spin 10s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-fade-in-down {
    animation: fadeInDown 1s ease-out forwards;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -50px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* Clase que se activa con JS al hacer scroll */
.step-item.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.perspective-1000 {
    perspective: 1000px;
}
</style>