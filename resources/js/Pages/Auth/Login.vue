<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
// Importamos los componentes de Naive UI
import { NInput, NButton, NCheckbox, NIcon } from 'naive-ui';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <AuthenticationCard>
        <template #logo>
            <!-- Header con Logo y detalle tech -->
            <div class="flex flex-col items-center gap-2 mb-4">
                <AuthenticationCardLogo class="h-16 w-16 text-[#2f4b59]" />
                <span class="text-[10px] font-mono tracking-[0.3em] text-slate-400 uppercase">Secure Access Node</span>
            </div>
        </template>

        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-slate-800 mb-2 tracking-tight">Bienvenido</h1>
            <p class="text-slate-500 text-sm">Identifícate para acceder al sistema.</p>
        </div>

        <div v-if="status" class="mb-6 font-medium text-sm text-cyan-600 bg-cyan-50 p-4 rounded-lg border border-cyan-100 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            
            <!-- Email Input (Naive UI) -->
            <div class="space-y-1">
                <n-input
                    v-model:value="form.email"
                    placeholder="Correo Electrónico"
                    size="large"
                    round
                    class="!bg-slate-50 hover:!bg-white focus-within:!bg-white transition-all text-lg py-1"
                >
                    <template #prefix>
                        <n-icon size="20" class="text-slate-400 mr-2">
                             <!-- Icono SVG de Email -->
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </n-icon>
                    </template>
                </n-input>
                <InputError :message="form.errors.email" />
            </div>

            <!-- Password Input (Naive UI) -->
            <div class="space-y-1">
                <n-input
                    v-model:value="form.password"
                    type="password"
                    show-password-on="click"
                    placeholder="Contraseña"
                    size="large"
                    round
                    class="!bg-slate-50 hover:!bg-white focus-within:!bg-white transition-all text-lg py-1"
                >
                    <template #prefix>
                        <n-icon size="20" class="text-slate-400 mr-2">
                            <!-- Icono SVG de Candado -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </n-icon>
                    </template>
                </n-input>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Opciones Inferiores -->
            <div class="flex items-center justify-between mt-2 px-2">
                <div class="flex items-center">
                    <n-checkbox v-model:checked="form.remember" size="medium">
                        <span class="text-sm text-slate-600 font-medium">Recordarme</span>
                    </n-checkbox>
                </div>
                
                 <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-bold text-cyan-600 hover:text-cyan-800 transition-colors uppercase tracking-wider">
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <!-- Botón de Acción (Moderno y Tecnológico) -->
            <div class="pt-4">
                <n-button
                    attr-type="submit"
                    type="primary"
                    size="large"
                    block
                    round
                    :loading="form.processing"
                    :disabled="form.processing"
                    class="h-14 text-lg font-bold tracking-widest shadow-xl shadow-[#2f4b59]/20 hover:shadow-[#2f4b59]/40 transition-shadow duration-300"
                    color="#2f4b59"
                    text-color="#ffffff"
                >
                    <template #icon>
                        <n-icon v-if="!form.processing">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </n-icon>
                    </template>
                    ACCEDER
                </n-button>
            </div>
        </form>
        
        <!-- Footer simple -->
        <div class="mt-10 text-center border-t border-slate-100 pt-6">
            <p class="text-[10px] text-slate-400 font-mono">
                SYSTEM ID: ION-AUTH-V2
            </p>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
/* Sobrescribimos estilos de Naive UI para alinearlos con la marca (Cyan y Azul Oscuro) */
:deep(.n-input) {
    --n-border-hover: 1px solid #4cc9f0 !important;
    --n-border-focus: 1px solid #4cc9f0 !important;
    --n-box-shadow-focus: 0 0 0 2px rgba(76, 201, 240, 0.2) !important;
    --n-caret-color: #2f4b59 !important;
}

:deep(.n-checkbox.n-checkbox--checked .n-checkbox-box) {
    background-color: #2f4b59 !important;
    border-color: #2f4b59 !important;
}

:deep(.n-checkbox:hover .n-checkbox-box) {
    border-color: #4cc9f0 !important;
}
</style>