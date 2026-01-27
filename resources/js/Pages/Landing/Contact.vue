<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
    NInput, 
    NButton, 
    NForm, 
    NFormItem, 
    createDiscreteApi,
    NConfigProvider
} from 'naive-ui';
import { onMounted, ref } from 'vue';

// --- Configuración de Tema (Coincidiendo con Layout) ---
const themeOverrides = {
    common: {
        primaryColor: '#2f4b59',
        primaryColorHover: '#3e6070',
        primaryColorPressed: '#1f333d'
    },
    Input: {
        borderRadius: '12px',
        borderHover: '1px solid #4cc9f0',
        borderFocus: '1px solid #4cc9f0',
        boxShadowFocus: '0 0 0 2px rgba(76, 201, 240, 0.2)'
    }
};

// --- Estado y Formulario ---
const formRef = ref(null);
const showContent = ref(false); // Control para animaciones

// Formulario de Inertia
const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: ''
});

// Reglas de validación para Naive UI
const rules = {
    name: { required: true, message: 'Por favor ingresa tu nombre', trigger: 'blur' },
    email: { required: true, message: 'Ingresa un correo válido', trigger: ['blur', 'input'], type: 'email' },
    message: { required: true, message: 'Escribe tu mensaje', trigger: 'blur' }
};

// --- Ciclo de Vida ---
onMounted(() => {
    // Activar animaciones al montar
    setTimeout(() => {
        showContent.value = true;
    }, 100);
});

// --- Métodos ---
const handleSubmit = (e) => {
    e.preventDefault();
    
    formRef.value?.validate((errors) => {
        if (!errors) {
            // Enviar datos
            form.post(route('contact.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    const { message } = createDiscreteApi(['message']);
                    message.success('¡Mensaje enviado con éxito! Te contactaremos pronto.');
                    form.reset();
                },
                onError: () => {
                    const { message } = createDiscreteApi(['message']);
                    message.error('Hubo un error al enviar el mensaje.');
                }
            });
        } else {
            const { message } = createDiscreteApi(['message']);
            message.warning('Por favor revisa los campos obligatorios.');
        }
    });
};
</script>

<template>
    <AppLayoutLanding>
        <Head title="Contacto" />
        
        <NConfigProvider :theme-overrides="themeOverrides">
            <div class="relative min-h-screen bg-slate-50 overflow-hidden">
                
                <!-- 1. FONDO DECORATIVO ANIMADO -->
                <!-- Círculos de color difusos de fondo -->
                <div class="absolute top-0 left-0 w-full h-[60vh] bg-[#2f4b59] overflow-hidden rounded-b-[50px] md:rounded-b-[100px] shadow-2xl z-0">
                    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-[#4cc9f0] opacity-10 blur-[120px] rounded-full animate-pulse-slow"></div>
                    <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-[#ffffff] opacity-5 blur-[100px] rounded-full"></div>
                    
                    <!-- Patrón de puntos sutil -->
                    <div class="absolute inset-0 opacity-10" 
                        style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;">
                    </div>
                </div>

                <!-- 2. CONTENIDO PRINCIPAL -->
                <div class="relative z-10 max-w-7xl mx-auto px-6 pt-32 md:pt-40 pb-20">
                    
                    <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-start">
                        
                        <!-- COLUMNA IZQUIERDA: Texto e Info (Animación 1) -->
                        <div class="lg:col-span-5 text-white space-y-8 transition-all duration-1000 transform"
                             :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                            
                            <div>
                                <h2 class="text-[#4cc9f0] font-bold tracking-widest uppercase text-sm mb-2">Contáctanos</h2>
                                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                                    Hagamos realidad <br/>
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">tu visión digital.</span>
                                </h1>
                                <p class="text-slate-300 text-lg leading-relaxed">
                                    Ya sea para escaneo 3D, impresión o desarrollo de activos digitales, nuestro equipo está listo para asesorarte.
                                </p>
                            </div>

                            <!-- Tarjetas de Información de Contacto -->
                            <div class="space-y-6 pt-4">
                                <!-- Ubicación -->
                                <div class="flex items-start gap-5 p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors group cursor-default">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#4cc9f0] to-[#2f4b59] flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-white">Estudio Central</h3>
                                        <p class="text-slate-400 text-sm mt-1">Av. Vallarta 2440, Arcos Vallarta<br>Guadalajara, Jalisco, MX.</p>
                                    </div>
                                </div>
                                
                                <!-- Correo / Teléfono -->
                                <div class="flex items-start gap-5 p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors group cursor-default">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#4cc9f0] to-[#2f4b59] flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-white">Contacto Directo</h3>
                                        <p class="text-slate-400 text-sm mt-1">hola@ion3d.com</p>
                                        <p class="text-[#4cc9f0] font-bold text-sm mt-1">+52 (33) 4324 9116</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- COLUMNA DERECHA: Formulario (Animación 2 - Retrasada) -->
                        <div class="lg:col-span-7 transition-all duration-1000 delay-300 transform"
                             :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                            
                            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-[#2f4b59]/20 p-8 md:p-12 border border-slate-100 relative overflow-hidden">
                                <!-- Decoración esquina formulario -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#4cc9f0]/20 to-transparent rounded-bl-full pointer-events-none"></div>

                                <h3 class="text-2xl font-bold text-[#2f4b59] mb-8 flex items-center gap-2">
                                    <span class="w-2 h-8 bg-[#4cc9f0] rounded-full inline-block"></span>
                                    Envíanos un mensaje
                                </h3>

                                <n-form 
                                    ref="formRef" 
                                    :model="form" 
                                    :rules="rules"
                                    size="large"
                                >
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <n-form-item label="Nombre Completo" path="name">
                                            <n-input v-model:value="form.name" placeholder="Ej. Alex Rivera" />
                                        </n-form-item>
                                        <n-form-item label="Teléfono (Opcional)" path="phone">
                                            <n-input v-model:value="form.phone" placeholder="Ej. 33 1234 5678" />
                                        </n-form-item>
                                    </div>

                                    <n-form-item label="Correo Electrónico" path="email">
                                        <n-input v-model:value="form.email" placeholder="nombre@empresa.com" />
                                    </n-form-item>

                                    <n-form-item label="¿Cómo podemos ayudarte?" path="message">
                                        <n-input 
                                            v-model:value="form.message" 
                                            type="textarea" 
                                            placeholder="Descríbenos tu proyecto o duda..." 
                                            :autosize="{ minRows: 5, maxRows: 8 }"
                                            class="!rounded-2xl" 
                                        />
                                    </n-form-item>

                                    <div class="mt-4 flex items-center justify-end">
                                        <n-button 
                                            type="primary" 
                                            size="large" 
                                            :loading="form.processing"
                                            @click="handleSubmit"
                                            class="w-full md:w-auto px-12 !h-14 !text-lg !font-bold !rounded-xl shadow-lg shadow-[#2f4b59]/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                                            color="#2f4b59"
                                        >
                                            Enviar Solicitud
                                            <template #icon>
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </template>
                                        </n-button>
                                    </div>
                                </n-form>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- 3. MAPA FLOTANTE INFERIOR (Decorativo) -->
                <!-- Una sección sutil para cerrar el diseño -->
                <div class="w-full h-64 bg-slate-100 relative overflow-hidden border-t border-slate-200">
                    <div class="absolute inset-0 grayscale opacity-40 bg-[url('https://maps.googleapis.com/maps/api/staticmap?center=20.673887,-103.389771&zoom=14&size=1600x400&scale=2&style=feature:all|saturation:-100&key=')] bg-cover bg-center"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-50 to-transparent"></div>
                    
                    <!-- Overlay de texto -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="bg-white/80 backdrop-blur-md px-8 py-4 rounded-full shadow-lg border border-white flex items-center gap-3">
                            <span class="w-3 h-3 bg-green-500 rounded-full animate-ping"></span>
                            <span class="font-bold text-[#2f4b59]">Ubicados en el corazón de Guadalajara</span>
                        </div>
                    </div>
                </div>

            </div>
        </NConfigProvider>
    </AppLayoutLanding>
</template>

<style scoped>
/* Animación suave personalizada */
@keyframes pulse-slow {
    0%, 100% { opacity: 0.1; transform: scale(1); }
    50% { opacity: 0.2; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 6s infinite ease-in-out;
}
</style>