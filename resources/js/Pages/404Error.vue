<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';

const mouseX = ref(0);
const mouseY = ref(0);

// Función para el efecto parallax
const handleMouseMove = (event) => {
    // Calculamos la posición relativa al centro de la pantalla
    // Dividimos por valores más grandes para suavizar el movimiento
    mouseX.value = event.clientX - window.innerWidth / 2;
    mouseY.value = event.clientY - window.innerHeight / 2;
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Modelo No Encontrado - 404" />
    
    <div 
        class="relative h-screen w-full flex items-center justify-center overflow-hidden bg-slate-50 font-sans" 
        @mousemove="handleMouseMove"
    >
        <!-- Fondo: Grid Tecnológico Sutil -->
        <div class="absolute inset-0 tech-grid opacity-30 pointer-events-none"></div>
        
        <!-- Fondo: Vignette azulada suave en las esquinas -->
        <div class="absolute inset-0 bg-radial-gradient pointer-events-none"></div>

        <!-- --- ELEMENTOS FLOTANTES (PARALLAX) --- -->
        
        <!-- Elemento 1: Icosaedro Wireframe (Fondo lejano) -->
        <div 
            class="absolute opacity-20 text-[#2f4b59] z-0 blur-[1px]"
            :style="{ transform: `translate(${mouseX * 0.02}px, ${mouseY * 0.02}px) rotate(${mouseY * 0.05}deg)` }"
            style="transition: transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1); top: 15%; right: 10%;"
        >
            <svg width="150" height="150" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="0.5">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                <path d="M2 7v10l10 5 10-5V7l-10 5L2 7z"/>
            </svg>
        </div>

        <!-- Elemento 2: Cubo Sólido (Primer plano desenfocado) -->
        <div 
            class="absolute opacity-10 text-[#4cc9f0] z-20 blur-sm"
            :style="{ transform: `translate(${mouseX * -0.04}px, ${mouseY * -0.04}px) rotate(${mouseX * 0.02}deg)` }"
            style="transition: transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1); bottom: 10%; left: 10%;"
        >
             <svg width="200" height="200" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.94 12.21 22 12 22C11.79 22 11.59 21.94 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.06 11.79 2 12 2C12.21 2 12.41 2.06 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5Z"/>
            </svg>
        </div>

        <!-- Elemento 3: Nube de Puntos (Decoración de escaneo) -->
        <div 
            class="absolute top-1/2 left-1/2 w-[800px] h-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-dashed border-[#4cc9f0]/20 z-0 animate-spin-slow"
        ></div>

        <!-- --- CONTENIDO CENTRAL --- -->
        <div class="relative z-10 text-center max-w-2xl px-6">
            
            <!-- Icono Glitch 3D -->
            <div class="mb-6 relative inline-block group">
                <!-- Sombra / Base -->
                <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 w-32 h-4 bg-[#2f4b59]/10 rounded-full blur-md group-hover:w-40 transition-all duration-500"></div>
                
                <!-- Cubo SVG Central -->
                <div class="relative transform transition-transform duration-500 hover:scale-110 hover:rotate-6">
                    <svg class="w-48 h-48 drop-shadow-2xl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Caras del cubo "roto" -->
                        <path d="M100 20L170 60V140L100 180L30 140V60L100 20Z" fill="white" stroke="#2f4b59" stroke-width="4"/>
                        <path d="M100 20L100 100L170 60" stroke="#2f4b59" stroke-width="4" stroke-linecap="round"/>
                        <path d="M100 100L30 60" stroke="#2f4b59" stroke-width="4" stroke-linecap="round"/>
                        <path d="M100 100L100 180" stroke="#2f4b59" stroke-width="4" stroke-linecap="round"/>
                        
                        <!-- Piezas flotantes (Error) -->
                        <rect x="130" y="40" width="20" height="20" rx="4" fill="#4cc9f0" class="animate-bounce-custom" />
                        <rect x="40" y="100" width="15" height="15" rx="2" fill="#ef4444" class="animate-bounce-custom delay-100" />
                        
                        <!-- Texto 404 dentro del cubo -->
                        <text x="100" y="115" font-family="monospace" font-weight="bold" font-size="32" text-anchor="middle" fill="#2f4b59">404</text>
                    </svg>
                </div>

                <!-- Etiqueta de Error -->
                <div class="absolute -top-2 -right-6 bg-red-500 text-white font-mono font-bold text-xs px-2 py-1 rounded shadow-lg transform rotate-12 animate-pulse">
                    MESH ERROR
                </div>
            </div>

            <!-- Títulos -->
            <h1 class="text-4xl md:text-6xl font-black mb-2 text-[#2f4b59] tracking-tight">
                Objeto No Encontrado
            </h1>
            
            <p class="text-slate-500 text-lg md:text-xl mb-10 leading-relaxed max-w-lg mx-auto">
                El modelo que buscas no ha podido ser renderizado. <br/>
                Es posible que el enlace esté roto o el archivo haya sido eliminado de la base de datos.
            </p>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <!-- Botón Principal -->
                <!-- <Link 
                    href="/dashboard"
                    class="group relative px-8 py-3 bg-[#2f4b59] text-white rounded-full font-bold transition-all duration-300 hover:shadow-[0_0_20px_rgba(47,75,89,0.3)] hover:-translate-y-1 overflow-hidden"
                >
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Volver al Inicio
                    </span>
                </Link> -->

                <!-- Botón Secundario -->
                <button 
                    @click="goBack" 
                    class="px-8 py-3 bg-white border-2 border-slate-200 text-slate-600 rounded-full font-bold transition-all duration-300 hover:border-[#4cc9f0] hover:text-[#4cc9f0] flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Regresar
                </button>
            </div>
            
            <!-- Footer Técnico / Decorativo -->
            <div class="mt-16 pt-6 border-t border-slate-200 w-full max-w-md mx-auto">
                <div class="flex justify-between text-[10px] uppercase tracking-widest text-slate-400 font-mono">
                    <span>Error Code: 0x404_NULL</span>
                    <span class="flex items-center gap-1">
                        Status: <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span> Failed
                    </span>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Fondo con retícula técnica */
.tech-grid {
    background-image: 
        linear-gradient(rgba(76, 201, 240, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(76, 201, 240, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Vignette azulada suave */
.bg-radial-gradient {
    background: radial-gradient(circle at center, transparent 0%, rgba(224, 242, 254, 0.4) 100%);
}

/* Animación de rotación lenta */
.animate-spin-slow {
    animation: spin 60s linear infinite;
}
@keyframes spin {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Rebote personalizado para las piezas flotantes */
.animate-bounce-custom {
    animation: floatBounce 3s ease-in-out infinite;
}
.delay-100 { animation-delay: 1.5s; }

@keyframes floatBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Efecto de brillo al pasar el mouse por el botón */
@keyframes shimmer {
    100% { transform: translateX(100%); }
}
.group-hover\:animate-shimmer:hover {
    animation: shimmer 1s infinite;
}
</style>