<script>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'ProcessView',
    components: {
        AppLayoutLanding,
        Head
    },
    data() {
        return {
            steps: [
                {
                    id: 1,
                    title: 'Consulta Inicial',
                    description: 'Evaluamos tus necesidades y determinamos la mejor tecnología de escaneo para tu proyecto.',
                    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'
                },
                {
                    id: 2,
                    title: 'Sesión de Escaneo',
                    description: 'Utilizamos nuestros escáneres biométricos de alta resolución. El proceso toma apenas unos segundos para personas.',
                    icon: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'
                },
                {
                    id: 3,
                    title: 'Procesamiento Digital',
                    description: 'Nuestros artistas 3D refinan la malla, ajustan texturas y preparan el archivo para su propósito final.',
                    icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'
                },
                {
                    id: 4,
                    title: 'Entrega o Impresión',
                    description: 'Recibes tus archivos digitales optimizados o tu figura impresa en 3D full color enviada a domicilio.',
                    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
                }
            ]
        }
    }
}
</script>

<template>
    <AppLayoutLanding>
        <Head title="Nuestro Proceso" />

        <div class="max-w-7xl mx-auto px-6 py-12">
            <!-- Encabezado -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-[#2f4b59] mb-4">Nuestro Proceso</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Transformamos lo físico en digital a través de un flujo de trabajo optimizado y preciso.
                </p>
            </div>

            <!-- Timeline Vertical -->
            <div class="relative">
                <!-- Línea conectora central -->
                <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-gradient-to-b from-[#2f4b59] to-[#4cc9f0] opacity-20 hidden md:block transform -translate-x-1/2"></div>

                <div class="space-y-12 md:space-y-24">
                    <div v-for="(step, index) in steps" :key="step.id" 
                        class="relative flex flex-col md:flex-row gap-8 items-center"
                        :class="index % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'">
                        
                        <!-- Contenido -->
                        <div class="flex-1 text-center md:text-left" :class="index % 2 === 0 ? 'md:text-right' : 'md:text-left'">
                            <div class="inline-block p-3 rounded-2xl bg-slate-50 text-[#2f4b59] mb-4 md:hidden">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="step.icon"></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-[#2f4b59] mb-2">{{ step.title }}</h3>
                            <p class="text-slate-600 leading-relaxed">{{ step.description }}</p>
                        </div>

                        <!-- Badge Central -->
                        <div class="relative z-10 shrink-0">
                            <div class="w-16 h-16 rounded-full bg-[#2f4b59] text-white flex items-center justify-center text-xl font-bold shadow-xl ring-4 ring-white">
                                {{ index + 1 }}
                            </div>
                        </div>

                        <!-- Espacio vacío para balancear grid -->
                        <div class="flex-1 hidden md:block pl-10" :class="index % 2 === 0 ? 'text-left' : 'text-right'">
                            <div class="inline-block p-6 rounded-2xl bg-white shadow-lg border border-slate-100 group hover:-translate-y-1 transition-transform duration-300">
                                <svg class="w-10 h-10 text-[#4cc9f0] group-hover:text-[#2f4b59] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" v-html="step.icon"></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-24 bg-[#2f4b59] rounded-3xl p-12 text-center text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-4">¿Listo para comenzar?</h2>
                    <p class="mb-8 text-blue-100">Agenda tu sesión de escaneo hoy mismo.</p>
                    <button class="bg-[#4cc9f0] text-[#0f2530] px-8 py-3 rounded-full font-bold hover:bg-white transition-colors">
                        Agendar Cita
                    </button>
                </div>
                <!-- Decoración -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#4cc9f0] opacity-10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            </div>
        </div>
    </AppLayoutLanding>
</template>