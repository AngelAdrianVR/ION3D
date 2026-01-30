<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { NCard, NForm, NFormItem, NInput, NButton, useMessage } from 'naive-ui';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const message = useMessage();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            message.success('Contraseña actualizada correctamente');
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <n-card title="Actualizar Contraseña" size="large" :bordered="false">
        <template #header-extra>
             <span class="text-slate-400 text-xs uppercase tracking-wider font-bold">Seguridad</span>
        </template>
        
        <p class="text-slate-500 mb-6 text-sm">
            Asegúrate de que tu cuenta esté protegida usando una contraseña larga y aleatoria.
        </p>

        <n-form @submit.prevent="updatePassword">
            <div class="max-w-xl space-y-2">
                
                <n-form-item label="Contraseña Actual" :validation-status="form.errors.current_password ? 'error' : undefined" :feedback="form.errors.current_password">
                    <n-input
                        ref="currentPasswordInput"
                        v-model:value="form.current_password"
                        type="password"
                        show-password-on="click"
                        placeholder="••••••••"
                        size="large"
                    />
                </n-form-item>

                <n-form-item label="Nueva Contraseña" :validation-status="form.errors.password ? 'error' : undefined" :feedback="form.errors.password">
                    <n-input
                        ref="passwordInput"
                        v-model:value="form.password"
                        type="password"
                        show-password-on="click"
                        placeholder="••••••••"
                        size="large"
                    />
                </n-form-item>

                <n-form-item label="Confirmar Contraseña" :validation-status="form.errors.password_confirmation ? 'error' : undefined" :feedback="form.errors.password_confirmation">
                    <n-input
                        v-model:value="form.password_confirmation"
                        type="password"
                        show-password-on="click"
                        placeholder="••••••••"
                        size="large"
                    />
                </n-form-item>
            </div>

            <div class="flex justify-end mt-6 pt-6 border-t border-slate-100">
                <n-button 
                    type="primary" 
                    size="large" 
                    :loading="form.processing"
                    @click="updatePassword"
                >
                    Guardar Contraseña
                </n-button>
            </div>
        </n-form>
    </n-card>
</template>