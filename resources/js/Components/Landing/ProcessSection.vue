<script setup>
import { ref, computed } from 'vue';

const activeStep = ref(0);

const steps = [
  { 
    id: 1, 
    title: 'Escaneo Volumétrico', 
    duration: '2 min', 
    shortDesc: 'Captura instantánea de alta resolución.',
    description: 'Entras a nuestra cabina de fotogrametría donde 120 cámaras DSLR sincronizadas capturan tu geometría y textura en una fracción de segundo.',
    techSpecs: ['120 Cámaras DSLR', 'Sincronización 1/1000s', '8K Texturas'],
    iconPath: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'
  },
  { 
    id: 2, 
    title: 'Procesamiento Digital', 
    duration: '24 hrs', 
    shortDesc: 'Reconstrucción y limpieza de malla.',
    description: 'Nuestros algoritmos de IA reconstruyen la nube de puntos. Luego, artistas digitales perfeccionan manualmente los detalles para garantizar un parecido exacto.',
    techSpecs: ['Topología Quad', 'Limpieza Manual', 'UV Mapping Optimizado'],
    iconPath: 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z'
  },
  { 
    id: 3, 
    title: 'Impresión y Acabado', 
    duration: '48 hrs', 
    shortDesc: 'Materialización en alta definición.',
    description: 'Imprimimos tu gemelo digital capa por capa utilizando tecnología de inyección de aglutinante a todo color (CJP) para un resultado fotorrealista.',
    techSpecs: ['Impresión CJP/SLA', 'Color de 24-bits', 'Sellado Protector'],
    iconPath: 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z M16.5 12L12 14.6 7.5 12 M12 3l9 4.5v9L12 21 3 16.5v-9L12 3z'
  },
];

// Calcula el porcentaje de ancho de la barra de progreso
const progressWidth = computed(() => {
  return (activeStep.value / (steps.length - 1)) * 100 + '%';
});

const setActive = (index) => {
  activeStep.value = index;
};
</script>

<template>
  <section class="py-24 bg-slate-900 text-white relative overflow-hidden select-none">
    
    <!-- Elementos de fondo decorativos -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-[#4cc9f0] opacity-5 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-purple-600 opacity-5 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">
      
      <!-- Cabecera -->
      <div class="text-center mb-20">
        <h2 class="text-sm font-bold tracking-[0.2em] text-[#4cc9f0] uppercase mb-3">Workflow Interactivo</h2>
        <h3 class="text-3xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
          Del Mundo Real al Digital
        </h3>
        <p class="text-slate-400 mt-4 max-w-lg mx-auto">Haz clic en cada paso para descubrir cómo transformamos la realidad en datos digitales.</p>
      </div>

      <!-- Sistema de Pasos (Stepper) -->
      <div class="relative max-w-5xl mx-auto">
        
        <!-- Línea de Fondo (Riel) -->
        <div class="absolute top-12 left-0 w-full h-1 bg-slate-800 rounded-full overflow-hidden hidden md:block">
           <!-- Línea de Progreso Animada -->
           <div class="h-full bg-gradient-to-r from-[#4cc9f0] to-purple-500 transition-all duration-700 ease-out shadow-[0_0_15px_rgba(76,201,240,0.5)]"
                :style="{ width: progressWidth }"></div>
        </div>

        <!-- Grid de Pasos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-4 relative">
          
          <div v-for="(step, index) in steps" :key="step.id" 
               @click="setActive(index)"
               class="relative flex flex-col items-center group cursor-pointer">
            
            <!-- Círculo del Paso -->
            <div class="w-24 h-24 rounded-full border-4 flex items-center justify-center relative z-10 transition-all duration-500 bg-slate-900"
                 :class="[
                   activeStep >= index ? 'border-[#4cc9f0] shadow-[0_0_20px_rgba(76,201,240,0.3)] scale-110' : 'border-slate-700 group-hover:border-slate-500'
                 ]">
               
               <!-- Número o Icono -->
               <span class="text-2xl font-bold transition-colors duration-300"
                     :class="activeStep >= index ? 'text-white' : 'text-slate-600'">
                 0{{ index + 1 }}
               </span>

               <!-- Indicador de 'Activo' (Punto pulsante) -->
               <span v-if="activeStep === index" class="absolute -top-1 -right-1 w-4 h-4 bg-[#4cc9f0] rounded-full animate-ping"></span>
               <span v-if="activeStep === index" class="absolute -top-1 -right-1 w-4 h-4 bg-[#4cc9f0] rounded-full border-2 border-slate-900"></span>
            </div>

            <!-- Título del Paso -->
            <div class="mt-6 text-center transition-all duration-300"
                 :class="activeStep === index ? 'opacity-100 transform translate-y-0' : 'opacity-70 group-hover:opacity-100'">
              <h4 class="text-lg font-bold mb-1" 
                  :class="activeStep === index ? 'text-[#4cc9f0]' : 'text-white'">
                  {{ step.title }}
              </h4>
              <p class="text-xs font-mono text-slate-500 uppercase tracking-wider">{{ step.duration }}</p>
            </div>
            
          </div>
        </div>

        <!-- Panel de Detalles (Sección Dinámica) -->
        <div class="mt-16 relative h-64 md:h-48">
          <TransitionGroup name="fade" mode="out-in">
            <div v-for="(step, index) in steps" :key="step.id" v-show="activeStep === index"
                 class="absolute inset-0 w-full">
              
              <div class="bg-slate-800/50 backdrop-blur-md border border-white/10 rounded-2xl p-8 flex flex-col md:flex-row items-center md:items-start gap-8 shadow-2xl">
                
                <!-- Icono Grande Decorativo -->
                <div class="hidden md:flex flex-shrink-0 w-20 h-20 bg-[#4cc9f0]/10 rounded-2xl items-center justify-center text-[#4cc9f0]">
                   <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="step.iconPath"></path></svg>
                </div>

                <!-- Contenido de Texto -->
                <div class="flex-1 text-center md:text-left">
                   <h5 class="text-xl font-bold text-white mb-2">{{ step.title }}</h5>
                   <p class="text-slate-300 leading-relaxed mb-4">{{ step.description }}</p>
                   
                   <!-- Specs Técnicas (Badges) -->
                   <div class="flex flex-wrap justify-center md:justify-start gap-2">
                      <span v-for="spec in step.techSpecs" :key="spec" 
                            class="px-3 py-1 bg-[#4cc9f0]/10 border border-[#4cc9f0]/20 text-[#4cc9f0] text-xs rounded-full font-medium">
                        {{ spec }}
                      </span>
                   </div>
                </div>

                <!-- Botón de acción (Falso) -->
                <div class="flex-shrink-0">
                  <button class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/20 rounded-lg text-sm font-bold transition-all flex items-center gap-2 group/btn">
                    Ver Detalles
                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </button>
                </div>

              </div>
            </div>
          </TransitionGroup>
        </div>

      </div>
    </div>
  </section>
</template>

<style scoped>
/* Transiciones personalizadas para el contenido dinámico */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>