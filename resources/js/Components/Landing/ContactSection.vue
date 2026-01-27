<script setup>
import { useForm } from '@inertiajs/vue3';
import { NInput, NButton, NForm, NFormItem, createDiscreteApi } from 'naive-ui';

// Configuración del formulario
const form = useForm({
    name: '',
    phone: '',
    email: '',
    message: ''
});

// Función para enviar el mensaje
const submitMessage = () => {
    // Validaciones básicas antes de enviar
    if (!form.name || !form.email || !form.message) {
        const { message } = createDiscreteApi(['message']);
        message.warning('Por favor completa los campos obligatorios (*).');
        return;
    }

    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const { message } = createDiscreteApi(['message']);
            message.success('¡Mensaje enviado! Nos pondremos en contacto pronto.');
            form.reset();
        },
        onError: () => {
            const { message } = createDiscreteApi(['message']);
            message.error('Ocurrió un error al enviar el mensaje. Inténtalo de nuevo.');
        }
    });
};
</script>

<template>
    <section class="py-24 bg-white relative">
        <div class="container mx-auto px-6 max-w-4xl">
            <div class="bg-slate-900 rounded-3xl p-8 md:p-16 relative overflow-hidden shadow-2xl shadow-[#2f4b59]/30">
                <!-- Círculos decorativos -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#4cc9f0]/10 rounded-full translate-x-1/2 -translate-y-1/2"></div>
                
                <div class="grid md:grid-cols-2 gap-12 relative z-10">
                    <div class="text-white">
                        <h3 class="text-3xl font-bold mb-4">¿Listo para el futuro?</h3>
                        <p class="text-slate-400 mb-8">Agenda tu cita de escaneo o solicita una cotización para tu proyecto.</p>
                        
                        <!-- Botón WhatsApp Actualizado -->
                        <a href="https://wa.me/5213343249116" target="_blank" class="inline-flex items-center gap-3 bg-[#25D366] hover:bg-[#20bd5a] text-white px-6 py-3 rounded-xl transition-colors duration-300 font-bold mb-8 w-full md:w-auto justify-center">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            WhatsApp Directo (+52 1 33 4324 9116)
                        </a>
                        
                        <div class="text-sm text-slate-500">
                            <p>Zapopan, Jalisco, MX.</p>
                            <p>contacto@ion3d.com</p>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="bg-white rounded-2xl p-6">
                        <n-form
                            ref="formRef"
                            :model="form"
                        >
                            <n-form-item label="Nombre *" path="name">
                                <n-input v-model:value="form.name" placeholder="Tu nombre completo" />
                            </n-form-item>
                            
                            <!-- Campo Teléfono Agregado -->
                            <n-form-item label="Teléfono (Opcional)" path="phone">
                                <n-input v-model:value="form.phone" placeholder="55 1234 5678" />
                            </n-form-item>

                            <n-form-item label="Email *" path="email">
                                <n-input v-model:value="form.email" placeholder="ejemplo@correo.com" />
                            </n-form-item>

                            <n-form-item label="Mensaje *" path="message">
                                <n-input 
                                    v-model:value="form.message" 
                                    type="textarea" 
                                    placeholder="Cuéntanos tu idea..." 
                                    :autosize="{ minRows: 3, maxRows: 5 }"
                                />
                            </n-form-item>

                            <n-button 
                                type="primary" 
                                block 
                                color="#2f4b59" 
                                class="mt-2 h-10 font-bold"
                                :loading="form.processing"
                                @click="submitMessage"
                            >
                                Enviar Mensaje
                            </n-button>
                        </n-form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>