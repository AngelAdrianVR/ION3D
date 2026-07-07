<script>
import { NButton } from 'naive-ui';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ImageCarousel from './ImageCarousel.vue';

//images
import image1 from '@/../../public/images/Evolucion-_1_.webp';
import image2 from '@/../../public/images/Evolucion-_2_.webp';
import image3 from '@/../../public/images/Evolucion-_3_.webp';
import image4 from '@/../../public/images/Evolucion-_4_.webp';
import image5 from '@/../../public/images/Evolucion-_5_.webp';
import image6 from '@/../../public/images/Evolucion-_6_.webp';
import image7 from '@/../../public/images/Evolucion-_7_.webp';
import image8 from '@/../../public/images/Evolucion-_8_.webp';
import image9 from '@/../../public/images/Evolucion-_9_.webp';


export default {
    components: { NButton, Link, ImageCarousel },
    setup() {
        const isLoaded = ref(false);

        // ============================================================
        // 🖼️  CONFIGURACIÓN DE IMÁGENES DEL CAROUSEL
        // Agrega o quita rutas aquí según necesites.
        // ============================================================
        const carouselImages = [
            image1,
            image2,
            image3,
            image4,
            image5,
            image6,
            image7,
            image8,
            image9
        ];

        // ============================================================
        // 🎨  GRADIENTES DE FONDO PARA EL LIGHTBOX
        // Cada objeto { from, to } define el gradiente radial
        // que se muestra detrás de cada imagen en pantalla completa.
        // ============================================================
        const lightboxBackgrounds = [
            { from: '#1e3a5f', to: '#0d9488' },  
            { from: '#e7e9ee', to: '#515050' },   
            { from: '#917768', to: '#1f1007' },  
            { from: '#064e3b', to: '#059669' },  
            { from: '#a5aab4', to: '#596b87' }, 
            { from: '#78350f', to: '#f97316' }, 
            { from: '#d3a880', to: '#885f3b' },
            { from: '#d7cbc6', to: '#c4b9b4' },
        ];

        // Pequeño delay para que la UI se active con la primera imagen
        const onCarouselReady = () => {
            setTimeout(() => {
                isLoaded.value = true;
            }, 200);
        };

        return { isLoaded, carouselImages, lightboxBackgrounds, onCarouselReady };
    }
}
</script>

<template>
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden py-16 bg-[#fcfcfc]">
        
        <!-- Fondos Ambientales -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[60%] h-[60%] bg-gradient-to-bl from-blue-50/50 via-transparent to-transparent rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[60%] h-[60%] bg-gradient-to-tr from-slate-100/50 via-transparent to-transparent rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:40px_40px] opacity-20"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 grid lg:grid-cols-2 gap-12 items-center h-full">
            
            <!-- Texto con transiciones suaves controladas por isLoaded -->
            <div class="space-y-10 text-center lg:text-left order-2 lg:order-1 transition-all duration-1000 transform"
                 :class="[isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
                
                <div class="inline-flex items-center gap-3 pl-1 pr-4 py-1.5 rounded-full bg-white border border-slate-100 shadow-sm">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-[10px] font-black tracking-tighter">PRO</span>
                    <span class="text-slate-500 text-[11px] font-bold tracking-[0.2em] uppercase">Vanguardia en Digitalización 3D</span>
                </div>
                
                <h1 class="text-6xl lg:text-8xl font-extrabold leading-[0.9] text-slate-900 tracking-tighter">
                    Diseña el espacio <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">
                        que quieres vivir.
                    </span>
                </h1>
                
                <p class="text-xl text-slate-400 font-light max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    NODO permite crear, adaptar y transformar espacios a medida que cambian las necesidades de quienes lo habitan. <br/>
                    El sistema permanece con una sola instalación y la experiencia evoluciona con infinitas posibilidades.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start pt-4">
                    <n-button @click="$inertia.visit(route('landing.contact'))" color="#0f172a" size="large" round class="px-12 h-16 text-lg shadow-xl shadow-blue-500/10 font-bold hover:scale-105 transition-all">
                        Diseña tu espacio
                    </n-button>
                    <n-button @click="$inertia.visit(route('landing.portfolio'))" size="large" round class="px-12 h-16 text-lg border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-semibold shadow-sm">
                        Explorar Galería
                    </n-button>
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-10 pt-8 border-t border-slate-100 max-w-sm">
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">LATENCIA</div>
                        <div class="text-sm font-mono font-medium text-slate-500">0.02ms</div>
                     </div>
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">TOLERANCIA</div>
                        <div class="text-sm font-mono font-medium text-slate-500">±0.01mm</div>
                     </div>
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">ESTADO</div>
                        <div class="text-sm font-mono font-medium text-green-500">ONLINE</div>
                     </div>
                </div>
            </div>

            <!-- Contenedor del Carousel -->
            <div class="relative flex items-center justify-center order-1 lg:order-2 h-[550px] lg:h-[700px]">
                
                <!-- Tarjeta Principal -->
                <div class="relative w-full max-w-2xl h-full rounded-[3rem] overflow-hidden border border-white bg-white/40 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.06)] group transition-all duration-700 cursor-pointer"
                     :class="[isLoaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95']">
                    
                    <!-- HUD Elements -->
                    <!-- <div class="absolute inset-0 z-30 pointer-events-none p-10 flex flex-col justify-between">
                        <div class="flex justify-between items-start opacity-40 group-hover:opacity-100 transition-opacity duration-500">
                            <div class="space-y-1">
                                <div class="w-8 h-[1px] bg-slate-400"></div>
                                <div class="text-[9px] font-black text-slate-900 uppercase tracking-widest">Galería Visual</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Imagen</div>
                                <div class="text-[11px] font-mono text-slate-800">01/04</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-end">
                            <div class="flex gap-4 items-center bg-white/80 py-2 px-4 rounded-2xl border border-slate-50 backdrop-blur-sm shadow-sm">
                                <div class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </div>
                                <span class="text-[10px] font-black text-slate-700 tracking-widest uppercase">Visualizando...</span>
                            </div>
                            <div class="w-12 h-12 rounded-full border border-slate-100 flex items-center justify-center bg-white/50 backdrop-blur-sm">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div> -->

                    <!-- Efecto de barrido de luz -->
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-300/20 via-transparent to-transparent z-20 pointer-events-none translate-y-full animate-scanner-elegant"></div>
                    
                    <!-- Brillo de cristal -->
                    <div class="absolute top-[-20%] left-[-20%] w-[140%] h-[140%] bg-gradient-to-br from-white/20 via-transparent to-transparent pointer-events-none z-20"></div>

                    <!-- Carousel de imágenes -->
                    <ImageCarousel
                        :images="carouselImages"
                        :auto-play-interval="3500"
                        :transition-duration="550"
                        :lightbox-backgrounds="lightboxBackgrounds"
                        class="w-full h-full absolute inset-0 z-10"
                        @loaded="onCarouselReady"
                    />
                </div>

                <!-- Adornos Geométricos -->
                <div class="absolute -z-10 -bottom-10 -right-10 w-64 h-64 bg-slate-50 rounded-full mix-blend-multiply opacity-50"></div>
                <div class="absolute -z-10 -top-5 -left-5 w-32 h-32 border border-slate-100 rounded-full animate-spin-very-slow"></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Animación del escáner elegante */
@keyframes scanner-elegant {
    0% { transform: translateY(100%); opacity: 0; }
    20% { opacity: 0.4; }
    80% { opacity: 0.4; }
    100% { transform: translateY(-100%); opacity: 0; }
}
.animate-scanner-elegant {
    animation: scanner-elegant 6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes spin-very-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-very-slow {
    animation: spin-very-slow 60s linear infinite;
}

/* Tipografía y suavizado */
h1 {
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
}

/* Contenedor de la tarjeta con efecto flotante */
.animate-float-subtle {
    animation: float 10s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
</style>