<script setup>
import { NButton, NCarousel, NCarouselItem } from 'naive-ui';
import { Link } from '@inertiajs/vue3';

// Recibimos los items del portafolio directamente de la BD
const props = defineProps({
    portfolioItems: {
        type: Array,
        default: () => []
    }
});

</script>

<template>
  <section class="py-24 bg-slate-50 overflow-hidden relative group/section">
    
    <!-- Fondo sutil tecnológico -->
    <div class="absolute inset-0 pointer-events-none opacity-5" 
         style="background-image: linear-gradient(0deg, transparent 24%, #2f4b59 25%, #2f4b59 26%, transparent 27%, transparent 74%, #2f4b59 75%, #2f4b59 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, #2f4b59 25%, #2f4b59 26%, transparent 27%, transparent 74%, #2f4b59 75%, #2f4b59 76%, transparent 77%, transparent); background-size: 50px 50px;">
    </div>

    <div class="container mx-auto px-6 mb-12 relative z-10">
      <div class="flex flex-col md:flex-row justify-between items-end">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="w-2 h-2 bg-[#4cc9f0] rounded-full animate-pulse"></span>
            <h2 class="text-sm font-bold tracking-widest text-[#2f4b59] uppercase">Base de Datos Visual</h2>
          </div>
          <h3 class="text-3xl md:text-4xl font-bold text-slate-900">Portafolio de Escaneos</h3>
        </div>
        
        <!-- Botón con Inertia Link -->
        <Link :href="route('landing.portfolio')" class="mt-6 md:mt-0 group/btn inline-flex">
            <n-button strong secondary round type="primary">
            Explorar Galería Completa
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </template>
            </n-button>
        </Link>
      </div>
    </div>

    <!-- Carrusel -->
    <n-carousel
      v-if="portfolioItems.length > 0"
      autoplay
      draggable
      loop
      :interval="2500"
      slides-per-view="auto"
      class="pb-12 cursor-grab active:cursor-grabbing"
      :show-dots="false"
      effect="slide"
    >
      <n-carousel-item v-for="item in portfolioItems" :key="item.id" 
                       class="carousel-item-width">
        
        <!-- Wrapper con Padding -->
        <div class="p-3 h-full">
            <div class="group relative h-[400px] w-full rounded-2xl overflow-hidden bg-slate-900 shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl border border-slate-200 hover:border-[#4cc9f0]">
              
              <!-- Imagen de Fondo con Zoom en Hover -->
              <img :src="item.image" :alt="item.title" 
                  class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90 group-hover:opacity-60" />
              
              <!-- Gradiente Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-transparent to-transparent opacity-60 group-hover:opacity-90 transition-opacity duration-300"></div>

              <!-- Contenido Informativo (Tech Style) -->
              <div class="absolute inset-0 p-6 flex flex-col justify-between">
                
                <!-- Header Card -->
                <div class="flex justify-between items-start translate-y-[-20px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                  <span class="px-2 py-1 bg-[#4cc9f0]/20 text-[#4cc9f0] text-[10px] font-mono border border-[#4cc9f0]/30 rounded backdrop-blur-sm">
                    {{ item.scanId }}
                  </span>
                  <span class="text-xs text-slate-300 font-mono">{{ item.date }}</span>
                </div>

                <!-- Footer Card -->
                <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                  <p class="text-[#4cc9f0] text-xs font-bold tracking-widest uppercase mb-1 opacity-80 group-hover:opacity-100">
                    {{ item.category }}
                  </p>
                  <h4 class="text-white font-bold text-2xl leading-tight group-hover:text-[#4cc9f0] transition-colors">
                    {{ item.title }}
                  </h4>
                  
                  <!-- Línea decorativa animada -->
                  <div class="h-0.5 bg-[#4cc9f0] mt-4 w-0 group-hover:w-full transition-all duration-500 ease-out"></div>
                </div>
              </div>
              
            </div>
        </div>

      </n-carousel-item>
    </n-carousel>
    
    <!-- Estado vacío si no hay datos -->
    <div v-else class="text-center py-12 text-slate-400">
        <p>No hay proyectos destacados por el momento.</p>
    </div>

  </section>
</template>

<style scoped>
/* Ancho de las tarjetas controlado estrictamente por CSS.
   Usamos !important para asegurar que Naive UI no sobrescriba el ancho.
*/
.carousel-item-width {
  width: 85% !important; 
  flex-shrink: 0 !important; /* Vital para que no se aplasten */
}

@media (min-width: 768px) {
  .carousel-item-width {
    width: 50% !important; /* 2 items exactos */
  }
}

@media (min-width: 1024px) {
  .carousel-item-width {
    width: 25% !important; /* 4 items exactos */
  }
}

:deep(.n-carousel) {
  overflow: visible; 
}
</style>