<script setup>
import { ref, onMounted, computed } from 'vue';

// Datos de las estadísticas
const stats = ref([
  { 
    id: 1, 
    label: 'Escaneos Realizados', 
    targetValue: 1200, 
    displayValue: 0, 
    suffix: '+',
    prefix: '',
    iconPath: 'M3.5 3.5c0-1.1.9-2 2-2h13c1.1 0 2 .9 2 2v13c0 1.1-.9 2-2 2h-13c-1.1 0-2-.9-2-2v-13z M7 7h10 M7 12h10 M7 17h6' // Icono estilo documento/scan
  },
  { 
    id: 2, 
    label: 'Tiempo de Entrega', 
    targetValue: 48, 
    displayValue: 0, 
    suffix: 'h', 
    prefix: '',
    iconPath: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M12 8v4' // Icono escudo/tiempo
  },
  { 
    id: 3, 
    label: 'Resolución Texturas', 
    targetValue: 4, 
    displayValue: 0, 
    suffix: 'K', 
    prefix: '',
    iconPath: 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z M3.27 6.96L12 12.01l8.73-5.05 M12 22.08V12' // Cubo 3D
  },
  { 
    id: 4, 
    label: 'Satisfacción', 
    targetValue: 100, 
    displayValue: 0, 
    suffix: '%', 
    prefix: '',
    iconPath: 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z' // Estrella
  },
]);

const sectionRef = ref(null);
const hasAnimated = ref(false);

// Función de animación suave
const runAnimation = () => {
  if (hasAnimated.value) return;
  hasAnimated.value = true;

  const duration = 2000; // Duración de la animación en ms
  const frameDuration = 1000 / 60; // 60fps
  const totalFrames = Math.round(duration / frameDuration);

  stats.value.forEach(stat => {
    let frame = 0;
    const countTo = stat.targetValue;
    
    // Función de easing para que empiece rápido y termine lento
    const easeOutQuad = t => t * (2 - t);

    const counter = setInterval(() => {
      frame++;
      const progress = easeOutQuad(frame / totalFrames);
      const currentCount = Math.round(countTo * progress);

      if (parseInt(stat.displayValue) !== currentCount) {
        stat.displayValue = currentCount;
      }

      if (frame === totalFrames) {
        clearInterval(counter);
        stat.displayValue = countTo; // Asegurar valor final exacto
      }
    }, frameDuration);
  });
};

// Intersection Observer para detectar cuando la sección es visible
onMounted(() => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        runAnimation();
        // Opcional: dejar de observar si solo queremos que anime una vez
        // observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 }); // Se activa cuando el 30% del componente es visible

  if (sectionRef.value) {
    observer.observe(sectionRef.value);
  }
});
</script>

<template>
  <section ref="sectionRef" class="py-20 bg-slate-900 relative overflow-hidden group">
    
    <!-- Elementos de fondo decorativos (Grid y Glow) -->
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#4cc9f0]/5 to-transparent pointer-events-none"></div>
    
    <!-- Efecto de "Grid" tecnológico de fondo -->
    <div class="absolute inset-0" 
         style="background-image: radial-gradient(#4cc9f0 1px, transparent 1px); background-size: 40px 40px; opacity: 0.05;">
    </div>

    <div class="container mx-auto px-6 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Cards de Estadísticas -->
        <div v-for="stat in stats" :key="stat.id" 
             class="stat-card group/card relative p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm 
                    hover:bg-white/10 hover:border-[#4cc9f0]/50 transition-all duration-300 transform hover:-translate-y-2">
          
          <!-- Brillo en hover -->
          <div class="absolute -inset-0.5 bg-gradient-to-r from-[#4cc9f0] to-purple-500 rounded-2xl blur opacity-0 group-hover/card:opacity-20 transition duration-500"></div>
          
          <div class="relative flex flex-col items-center justify-center text-center">
            
            <!-- Icono con círculo -->
            <div class="mb-4 p-3 rounded-full bg-[#4cc9f0]/10 text-[#4cc9f0] group-hover/card:bg-[#4cc9f0] group-hover/card:text-slate-900 transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path :d="stat.iconPath" />
              </svg>
            </div>

            <!-- Número animado -->
            <div class="flex items-baseline gap-1 mb-2">
              <span class="text-4xl md:text-5xl font-black text-white tracking-tighter tabular-nums drop-shadow-lg">
                {{ stat.prefix }}{{ stat.displayValue }}
              </span>
              <span class="text-xl md:text-2xl font-bold text-[#4cc9f0]">{{ stat.suffix }}</span>
            </div>

            <!-- Etiqueta -->
            <div class="h-px w-12 bg-gradient-to-r from-transparent via-white/30 to-transparent my-3"></div>
            <div class="text-sm text-slate-400 uppercase tracking-widest font-semibold group-hover/card:text-white transition-colors">
              {{ stat.label }}
            </div>
            
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<style scoped>
/* Asegura que los números no "bailen" en el ancho mientras cuentan */
.tabular-nums {
  font-variant-numeric: tabular-nums;
}
</style>