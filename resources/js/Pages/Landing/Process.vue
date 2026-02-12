<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// Datos actualizados con los nuevos pasos de Personalización e Impresión
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
    title: 'Personaliza', 
    subtitle: 'Edición Digital',
    duration: 'Diseño • 1-2 Días', 
    description: 'Antes de imprimir, tienes el control. Personaliza tu modelo 3D cambiando tu vestimenta, agregando accesorios únicos o situando tu figura en escenas temáticas para hacerla única.',
    specs: ['Cambio de Vestuario', 'Accesorios Digitales', 'Escenas Virtuales'],
    image: 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 4, 
    title: 'Impresión 3D', 
    subtitle: 'Materialización',
    duration: 'Producción • 3-5 Días', 
    description: 'Una vez aprobado el diseño, imprimimos tu figura en alta definición. Recibirás una notificación cuando esté lista; puedes pasar por ella al estudio o pedir envío directo a tu domicilio.',
    specs: ['Envío a Domicilio', 'Pickup en Estudio', 'Protección Premium'],
    image: 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'
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

        <!-- Contenedor Principal LIGHT MODE (Fondo blanco con luces suaves) -->
        <div class="min-h-screen bg-white text-slate-900 relative overflow-hidden font-sans selection:bg-[#4cc9f0] selection:text-white">
            
            <!-- Fondos Ambientales Animados (Luces suaves) -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden">
                <div class="absolute top-[-10%] left-[-10%] w-[800px] h-[800px] bg-purple-200/40 rounded-full blur-[100px] animate-pulse-slow mix-blend-multiply"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-[#4cc9f0]/20 rounded-full blur-[80px] animate-pulse-slow delay-1000 mix-blend-multiply"></div>
                <!-- Textura de ruido muy sutil para dar acabdo premium -->
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-30 mix-blend-overlay"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-20 relative z-10">
                
                <!-- Encabezado Hero -->
                <div class="text-center mb-24 space-y-4 animate-fade-in-down">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#4cc9f0] bg-[#4cc9f0]/5 text-[#0ea5e9] text-xs font-bold uppercase tracking-widest backdrop-blur-md shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#4cc9f0] animate-ping"></span>
                        Workflow Digital
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tight text-slate-900 mb-6">
                        De la Realidad <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#0ea5e9] to-purple-600">al Metaverso</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto font-light leading-relaxed">
                        Un proceso de 4 pasos diseñado para capturar la esencia del momento con precisión milimétrica y personalización total.
                    </p>
                </div>

                <!-- Timeline Vertical -->
                <div class="relative">
                    
                    <!-- Línea Central Conectora (Ahora gris suave) -->
                    <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-100 transform -translate-x-1/2 hidden md:block border-l border-r border-slate-200">
                        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-[#4cc9f0] via-purple-400 to-transparent opacity-30"></div>
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
                                    <span class="text-[#0ea5e9] font-mono text-sm tracking-widest uppercase border-b-2 border-[#4cc9f0]/30 pb-1 font-semibold">
                                        {{ step.duration }}
                                    </span>
                                </div>
                                
                                <h3 class="text-4xl font-bold text-slate-900 mb-2 group-hover:text-[#0ea5e9] transition-colors duration-300">
                                    {{ step.title }}
                                </h3>
                                <p class="text-lg text-purple-600 mb-6 font-medium">{{ step.subtitle }}</p>
                                
                                <p class="text-slate-600 leading-relaxed text-lg mb-8">
                                    {{ step.description }}
                                </p>

                                <!-- Specs Pills (Estilo Light) -->
                                <div class="flex flex-wrap gap-2" :class="index % 2 === 0 ? 'justify-center md:justify-end' : 'justify-center md:justify-start'">
                                    <span v-for="spec in step.specs" :key="spec" 
                                          class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-500 group-hover:border-[#4cc9f0]/50 group-hover:text-[#0ea5e9] transition-colors shadow-sm">
                                        {{ spec }}
                                    </span>
                                </div>
                            </div>

                            <!-- COLUMNA CENTRAL: Nodo del Timeline (Estilo Light) -->
                            <div class="relative z-10 shrink-0 hidden md:flex items-center justify-center w-24">
                                <div class="w-16 h-16 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center relative group-hover:border-[#4cc9f0] group-hover:shadow-[0_0_30px_rgba(76,201,240,0.4)] transition-all duration-500 shadow-lg">
                                    <span class="text-2xl font-black text-slate-300 group-hover:text-[#0ea5e9] transition-colors">0{{ index + 1 }}</span>
                                    
                                    <!-- Orbit Animation -->
                                    <div class="absolute inset-[-4px] border border-[#4cc9f0]/0 rounded-full group-hover:border-[#4cc9f0]/50 group-hover:animate-spin-slow transition-all"></div>
                                </div>
                            </div>

                            <!-- COLUMNA 2: Imagen Visual -->
                            <div class="flex-1 w-full relative group/image perspective-1000">
                                <div class="relative h-[300px] md:h-[400px] rounded-3xl overflow-hidden shadow-2xl shadow-slate-200/50 border border-slate-100 transform transition-transform duration-700 group-hover:scale-[1.02] group-hover:rotate-1">
                                    
                                    <!-- Imagen -->
                                    <img :src="step.image" :alt="step.title" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                                    
                                    <!-- Overlays (Ajustados para que no oscurezcan demasiado) -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
                                    <div class="absolute inset-0 bg-[#4cc9f0] opacity-0 group-hover:opacity-10 transition-opacity duration-500 mix-blend-overlay"></div>

                                    <!-- Decoración Tecnológica en la imagen -->
                                    <div class="absolute bottom-6 left-6 right-6 flex justify-between items-end opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                        <div class="text-xs font-mono text-[#4cc9f0] bg-slate-900/90 backdrop-blur px-3 py-1 rounded border border-[#4cc9f0]/30 shadow-lg">
                                            STEP_0{{ index + 1 }} // EXEC
                                        </div>
                                    </div>
                                </div>

                                <!-- Sombra decorativa detrás -->
                                <div class="absolute -inset-4 bg-gradient-to-r from-[#4cc9f0] to-purple-600 rounded-[2rem] opacity-0 group-hover:opacity-10 blur-xl transition-opacity duration-500 -z-10"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- CTA Final (Mantenemos contraste oscuro para resaltar al final) -->
                <div class="mt-40 text-center relative max-w-4xl mx-auto">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#4cc9f0] to-purple-600 opacity-20 blur-[80px] rounded-full"></div>
                    
                    <!-- Tarjeta Oscura para contraste -->
                    <div class="relative bg-slate-900 backdrop-blur-xl border border-slate-700 rounded-[3rem] p-12 md:p-20 overflow-hidden group shadow-2xl">
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