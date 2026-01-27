<script>
export default {
    data() {
        return {
            services: [
                {
                    title: "Escaneo de Personas",
                    desc: "Captura biométrica instantánea para crear tu 'Mini-Me' o avatar digital.",
                    icon: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                },
                {
                    title: "Impresión Full Color",
                    desc: "Tecnología de polímero en polvo para reproducir millones de colores con precisión.",
                    icon: "M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                },
                {
                    title: "Activos Digitales & NFT",
                    desc: "Preparamos tus modelos para el metaverso, videojuegos o colecciones NFT.",
                    icon: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                }
            ]
        }
    }
}
</script>

<template>
    <section class="py-24 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <h2 class="text-sm font-bold tracking-widest text-[#4cc9f0] uppercase mb-2">Nuestras Capacidades</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900">Soluciones Integrales 3D</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div v-for="(service, idx) in services" :key="idx" 
                     class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-[#4cc9f0]/30 hover:shadow-2xl hover:shadow-[#2f4b59]/10 transition-all duration-500 cursor-default relative overflow-hidden">
                    
                    <!-- Fondo gradiente al hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <!-- Icono -->
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 group-hover:bg-[#2f4b59] group-hover:text-white transition-all duration-300 relative z-10 text-[#2f4b59]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="service.icon" />
                        </svg>
                    </div>

                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">{{ service.title }}</h4>
                    <p class="text-slate-500 leading-relaxed relative z-10 group-hover:text-slate-600 transition-colors">{{ service.desc }}</p>

                    <!-- Flecha decorativa -->
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-500 text-[#4cc9f0]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>