<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { NCard, NButton, NModal, NInput, NList, NListItem, NThing, NIcon, useMessage } from 'naive-ui';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);
const message = useMessage();

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    // Pequeño delay para que el modal renderice y podamos hacer focus
    setTimeout(() => {
        // En Naive UI, el ref puede necesitar acceso al elemento DOM interno
    }, 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            message.success('Sesiones cerradas correctamente');
        },
        onError: () => {
             // Manejo de error visual en el input
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
    <n-card title="Sesiones del Navegador" size="large" :bordered="false">
        <template #header-extra>
             <span class="text-slate-400 text-xs uppercase tracking-wider font-bold">Dispositivos</span>
        </template>

        <p class="text-slate-500 mb-6 text-sm">
            Administra y cierra tus sesiones activas en otros navegadores y dispositivos.
        </p>

        <div class="max-w-xl text-sm text-slate-600 mb-6">
            Si es necesario, puedes cerrar la sesión de todas tus otras sesiones de navegador en todos tus dispositivos. Algunas de tus sesiones recientes se enumeran a continuación; sin embargo, esta lista puede no ser exhaustiva.
        </div>

        <!-- Lista de Sesiones -->
        <div v-if="sessions.length > 0" class="bg-white rounded-xl border border-slate-100 overflow-hidden mb-6">
            <n-list hoverable clickable>
                <n-list-item v-for="(session, i) in sessions" :key="i">
                    <template #prefix>
                        <div class="p-2 bg-slate-50 rounded-lg text-slate-400">
                            <!-- Icono Desktop -->
                            <svg v-if="session.agent.is_desktop" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                            <!-- Icono Mobile -->
                            <svg v-else class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                    </template>
                    
                    <n-thing :title="session.agent.platform ? session.agent.platform : 'Desconocido'">
                        <template #description>
                            {{ session.agent.browser ? session.agent.browser : 'Desconocido' }}
                        </template>
                        <template #footer>
                            <div class="text-xs">
                                {{ session.ip_address }},
                                <span v-if="session.is_current_device" class="text-green-500 font-bold">Este dispositivo</span>
                                <span v-else class="text-slate-400">Última actividad {{ session.last_active }}</span>
                            </div>
                        </template>
                    </n-thing>
                </n-list-item>
            </n-list>
        </div>

        <div class="flex items-center mt-6 pt-6 border-t border-slate-100">
            <n-button type="primary" color="#2f4b59" @click="confirmLogout">
                Cerrar Sesión en Otros Dispositivos
            </n-button>

            <span v-if="form.recentlySuccessful" class="ml-3 text-sm text-green-600 font-medium animate-pulse">
                Hecho.
            </span>
        </div>

        <!-- Modal de Confirmación -->
        <n-modal
            v-model:show="confirmingLogout"
            preset="card"
            title="Cerrar Otras Sesiones"
            class="max-w-md"
            :bordered="false"
            @after-leave="closeModal"
        >
            <p class="text-slate-600 mb-4">
                Por favor ingresa tu contraseña para confirmar que deseas cerrar la sesión de tus otras sesiones de navegador en todos tus dispositivos.
            </p>

            <div class="space-y-4">
                <n-input
                    ref="passwordInput"
                    v-model:value="form.password"
                    type="password"
                    show-password-on="click"
                    placeholder="Contraseña"
                    @keyup.enter="logoutOtherBrowserSessions"
                    :status="form.errors.password ? 'error' : undefined"
                />
                <span v-if="form.errors.password" class="text-xs text-red-500 block">
                    {{ form.errors.password }}
                </span>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <n-button @click="closeModal">
                        Cancelar
                    </n-button>
                    <n-button 
                        type="primary" 
                        :loading="form.processing"
                        @click="logoutOtherBrowserSessions"
                    >
                        Cerrar Otras Sesiones
                    </n-button>
                </div>
            </template>
        </n-modal>
    </n-card>
</template>