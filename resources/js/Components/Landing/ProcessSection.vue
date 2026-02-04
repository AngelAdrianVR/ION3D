<script setup>
import { ref, computed } from 'vue';

const activeStep = ref(0);

const steps = [
  { 
    id: 1, 
    title: 'Agendar Cita', 
    duration: 'Online', 
    shortDesc: 'Reserva tu sesión fácilmente.',
    description: 'Selecciona el paquete ideal para ti y elige el horario que mejor te convenga en nuestro calendario en línea. Recibirás una confirmación inmediata.',
    techSpecs: ['Reserva 24/7', 'Asesoría Previa', 'Confirmación Instantánea'],
    // Imagen: Persona agendando / Calendario
    image: 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 2, 
    title: 'Sesión de Escaneo', 
    duration: '30 min', 
    shortDesc: 'Asiste a nuestro estudio.',
    description: 'Ven a nuestra cabina fotogramétrica. Recomendamos usar ropa con texturas, patrones o colores mate. Evita el color negro absoluto, materiales brillantes o transparencias para una captura perfecta.',
    techSpecs: ['Evitar Ropa Negra', 'Sin Materiales Brillantes', 'Captura en 1/1000s'],
    // Imagen: Estudio / Cámaras
    image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 3, 
    title: 'Impresión y Acabado', 
    duration: '3-5 Días', 
    shortDesc: 'Materialización de alta fidelidad.',
    description: 'Procesamos tu gemelo digital y lo imprimimos capa por capa con tecnología Full Color. Posteriormente, nuestros artesanos realizan el curado y sellado manual para garantizar durabilidad.',
    techSpecs: ['Impresión CJP/SLA', 'Millones de Colores', 'Post-proceso Manual'],
    // Imagen: Impresión 3D
    image: 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'
  },
  { 
    id: 4, 
    title: 'Entrega Final', 
    duration: 'Inmediata', 
    shortDesc: 'Recibe tu figura terminada.',
    description: 'Tu figura está lista. Puedes recogerla en nuestro estudio o solicitar el envío asegurado a tu domicilio. Se entrega en un empaque de protección premium.',
    techSpecs: ['Empaque Seguro', 'Envío Nacional', 'Certificado Digital'],
    // Imagen: Entrega / Caja / Figura
    image: 'https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&q=80&w=1000'
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
      <div class="text-center mb-16">
        <h2 class="text-sm font-bold tracking-[0.2em] text-[#4cc9f0] uppercase mb-3">Tu camino al 3D</h2>
        <h3 class="text-3xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
          Proceso de Creación
        </h3>
        <p class="text-slate-400 mt-4 max-w-lg mx-auto">Cuatro pasos simples para inmortalizar tu momento.</p>
      </div>

      <!-- Sistema de Pasos (Stepper) -->
      <div class="relative max-w-6xl mx-auto">
        
        <!-- Línea de Fondo (Riel) -->
        <div class="absolute top-12 left-0 w-full h-1 bg-slate-800 rounded-full overflow-hidden hidden md:block">
           <!-- Línea de Progreso Animada -->
           <div class="h-full bg-gradient-to-r from-[#4cc9f0] to-purple-500 transition-all duration-700 ease-out shadow-[0_0_15px_rgba(76,201,240,0.5)]"
                :style="{ width: progressWidth }"></div>
        </div>

        <!-- Grid de Pasos (Actualizado a 4 columnas) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-4 relative mb-12">
          
          <div v-for="(step, index) in steps" :key="step.id" 
               @click="setActive(index)"
               class="relative flex flex-col items-center group cursor-pointer">
            
            <!-- Círculo del Paso -->
            <div class="w-24 h-24 rounded-full border-4 flex items-center justify-center relative z-10 transition-all duration-500 bg-slate-900"
                 :class="[
                   activeStep >= index ? 'border-[#4cc9f0] shadow-[0_0_20px_rgba(76,201,240,0.3)] scale-110' : 'border-slate-700 group-hover:border-slate-500'
                 ]">
               
               <!-- Número -->
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
              <h4 class="text-sm md:text-base font-bold mb-1" 
                  :class="activeStep === index ? 'text-[#4cc9f0]' : 'text-white'">
                  {{ step.title }}
              </h4>
              <p class="text-[10px] font-mono text-slate-500 uppercase tracking-wider">{{ step.duration }}</p>
            </div>
            
          </div>
        </div>

        <!-- Panel de Detalles (Sección Dinámica con Imagen) -->
        <div class="relative min-h-[400px] md:min-h-[350px]">
          <TransitionGroup name="fade" mode="out-in">
            <div v-for="(step, index) in steps" :key="step.id" v-show="activeStep === index"
                 class="absolute inset-0 w-full">
              
              <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl overflow-hidden flex flex-col md:flex-row shadow-2xl h-full">
                
                <!-- Imagen del Paso (Nueva Sección) -->
                <div class="w-full md:w-5/12 relative h-48 md:h-auto overflow-hidden group">
                    <img :src="step.image" :alt="step.title" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                    <!-- Gradiente Overlay para texto legible si fuera necesario -->
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-900/10 to-slate-900/50 mix-blend-multiply"></div>
                    <div class="absolute top-4 left-4 bg-black/60 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full border border-white/20">
                        Paso 0{{ index + 1 }}
                    </div>
                </div>

                <!-- Contenido de Texto -->
                <div class="flex-1 p-8 md:p-10 flex flex-col justify-center text-center md:text-left">
                   <h5 class="text-2xl md:text-3xl font-bold text-white mb-4">{{ step.title }}</h5>
                   <p class="text-slate-300 text-lg leading-relaxed mb-6">{{ step.description }}</p>
                   
                   <!-- Specs Técnicas (Badges) -->
                   <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-8">
                      <span v-for="spec in step.techSpecs" :key="spec" 
                            class="px-3 py-1 bg-[#4cc9f0]/10 border border-[#4cc9f0]/20 text-[#4cc9f0] text-xs rounded-full font-medium">
                        {{ spec }}
                      </span>
                   </div>

                    <!-- Botón de acción -->
                    <div class="flex justify-center md:justify-start">
                        <button @click="$inertia.visit(route('landing.contact'))" class="px-8 py-3 bg-[#4cc9f0] hover:bg-[#3db5da] text-slate-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-[#4cc9f0]/20 flex items-center gap-2 group/btn">
                            {{ index === 0 ? 'Agendar Ahora' : 'Más Información' }}
                            <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </div>
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