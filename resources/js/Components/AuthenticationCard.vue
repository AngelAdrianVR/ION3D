<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-6 lg:p-8 relative overflow-hidden">
        
        <!-- Elementos de fondo decorativos globales -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[20%] -left-[10%] w-[70vw] h-[70vw] bg-[#2f4b59]/5 rounded-full blur-3xl"></div>
            <div class="absolute top-[20%] right-[10%] w-[50vw] h-[50vw] bg-cyan-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Tarjeta Principal (Split Layout) -->
        <div class="w-full max-w-6xl bg-white shadow-[0_25px_60px_-15px_rgba(0,0,0,0.1)] rounded-3xl overflow-hidden flex flex-col lg:flex-row relative z-10 min-h-[600px] lg:min-h-[750px]">
            
            <!-- Columna Izquierda: Formulario -->
            <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative bg-white/80 backdrop-blur-sm order-2 lg:order-1">
                <div class="max-w-md mx-auto w-full">
                    <!-- Slot para Logo (visible en movil y desktop dentro del form) -->
                    <div class="mb-10 flex justify-center lg:justify-start">
                        <slot name="logo" />
                    </div>
                    
                    <slot />
                </div>
            </div>

            <!-- Columna Derecha: Arte / Decoración (Solo visible grande o como banner en movil) -->
            <div class="w-full lg:w-1/2 relative overflow-hidden bg-[#2f4b59] order-1 lg:order-2 lg:flex flex-col justify-center items-center text-white p-12 hidden">
                
                <!-- Fondo con patrones tecnológicos -->
                <div class="absolute inset-0 z-0">
                    <!-- Gradiente base -->
                    <div class="absolute inset-0 bg-gradient-to-br from-[#2f4b59] via-[#1e3a47] to-[#0f1f26]"></div>
                    
                    <!-- Grid Pattern (Efecto escaneo) -->
                    <div class="absolute inset-0 opacity-50" 
                         style="background-image: radial-gradient(#4fd1c5 1px, transparent 1px); background-size: 30px 30px;">
                    </div>
                    
                    <!-- Formas abstractas flotantes -->
                    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl animate-pulse delay-700"></div>
                    
                    <!-- Líneas decorativas "Tech" -->
                    <svg class="absolute inset-0 w-full h-full opacity-100 pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                        
                        <!-- Círculos concéntricos (Efecto radar/escaneo) -->
                        <circle cx="50%" cy="50%" r="100" stroke="rgba(79, 209, 197, 0.3)" stroke-width="1" fill="none" class="animate-[spin_10s_linear_infinite]" />
                        <circle cx="50%" cy="50%" r="180" stroke="rgba(79, 209, 197, 0.1)" stroke-width="1" fill="none" class="animate-[spin_15s_linear_infinite_reverse]" style="stroke-dasharray: 10, 10;" />
                        <circle cx="50%" cy="50%" r="260" stroke="rgba(79, 209, 197, 0.1)" stroke-width="20" fill="none" class="opacity-10" />
                    </svg>
                </div>

                <!-- Contenido sobre la imagen -->
                <div class="relative z-10 text-center lg:text-left max-w-lg">
                    <h2 class="text-3xl lg:text-5xl font-bold mb-6 tracking-tight leading-tight">
                        La nueva dimensión del <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300">Escaneo 3D</span>
                    </h2>
                    <p class="text-lg text-gray-300 mb-8 font-light leading-relaxed">
                        Precisión milimétrica, texturas realistas y tecnología de vanguardia para digitalizar el mundo físico.
                    </p>
                    
                    <!-- Elemento decorativo flotante 3D simulado -->
                    <div class="mt-8 relative inline-block group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-600 rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative px-6 py-3 bg-[#1e3a47] rounded-lg border border-cyan-500/30 flex items-center gap-3">
                            <div class="h-2 w-2 rounded-full bg-cyan-400 animate-ping"></div>
                            <span class="text-cyan-100 font-mono text-xs tracking-widest uppercase">System Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer simple -->
        <!-- <div class="absolute bottom-4 text-center w-full text-xs text-gray-400 font-mono">
            &copy; 2024 3D SCAN CORP. ALL RIGHTS RESERVED.
        </div> -->
    </div>
</template>