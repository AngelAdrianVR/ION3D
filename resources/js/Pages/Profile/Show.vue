<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { NConfigProvider, NGlobalStyle, NMessageProvider } from 'naive-ui';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

// Configuración de tema base para Naive UI (Opcional, ajusta colores a tu gusto)
const themeOverrides = {
    common: {
        primaryColor: '#2f4b59',
        primaryColorHover: '#466d80',
        primaryColorPressed: '#1a2e38',
        borderRadius: '12px',
    },
    Card: {
        borderRadius: '16px',
        boxShadow: '0 4px 20px 0 rgba(0, 0, 0, 0.05)',
    }
};
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Configuración del Perfil
            </h2>
        </template>

        <n-config-provider :theme-overrides="themeOverrides">
            <n-message-provider>
                <div class="py-12 min-h-screen">
                    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                        
                        <!-- Información del Perfil -->
                        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                            <UpdateProfileInformationForm :user="$page.props.auth.user" />
                        </div>

                        <!-- Actualizar Contraseña -->
                        <div v-if="$page.props.jetstream.canUpdatePassword">
                            <UpdatePasswordForm />
                        </div>

                        <!-- Autenticación de Dos Factores -->
                        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                            <TwoFactorAuthenticationForm
                                :requires-confirmation="confirmsTwoFactorAuthentication"
                            />
                        </div>

                        <!-- Sesiones de Navegador -->
                        <LogoutOtherBrowserSessionsForm :sessions="sessions" />

                        <!-- Eliminar Cuenta -->
                        <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                            <DeleteUserForm />
                        </template>
                    </div>
                </div>
            </n-message-provider>
        </n-config-provider>
    </AppLayout>
</template>