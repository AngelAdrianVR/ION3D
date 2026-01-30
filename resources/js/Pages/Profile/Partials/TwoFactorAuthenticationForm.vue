<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue'; // Mantenemos este para la confirmación de seguridad
import { NCard, NButton, NTag, NInput, NFormItem, NDivider, NAlert } from 'naive-ui';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => ! enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    <n-card title="Autenticación de Dos Factores" size="large" :bordered="false">
        <template #header-extra>
             <n-tag v-if="twoFactorEnabled && !confirming" type="success" size="small" round>Habilitado</n-tag>
             <n-tag v-else-if="twoFactorEnabled && confirming" type="warning" size="small" round>Pendiente</n-tag>
             <n-tag v-else type="error" size="small" round>Deshabilitado</n-tag>
        </template>

        <div class="mb-6">
            <h3 v-if="twoFactorEnabled && ! confirming" class="text-lg font-medium text-slate-800">
                Has habilitado la autenticación de dos factores.
            </h3>
            <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium text-slate-800">
                Termina de habilitar la autenticación de dos factores.
            </h3>
            <h3 v-else class="text-lg font-medium text-slate-800">
                No has habilitado la autenticación de dos factores.
            </h3>
            <p class="mt-2 text-sm text-slate-500">
                Cuando la autenticación de dos factores está habilitada, se te solicitará un token seguro y aleatorio durante la autenticación. Puedes recuperar este token desde la aplicación Google Authenticator de tu teléfono.
            </p>
        </div>

        <div v-if="twoFactorEnabled">
            <div v-if="qrCode" class="bg-slate-50 p-6 rounded-xl border border-slate-100 mb-6">
                <div class="text-sm text-slate-600 mb-4">
                    <p v-if="confirming" class="font-semibold text-slate-800">
                        Para terminar, escanea el siguiente código QR usando tu aplicación de autenticación o ingresa la clave de configuración y proporciona el código OTP generado.
                    </p>
                    <p v-else>
                        La autenticación de dos factores está habilitada. Escanea el siguiente código QR usando tu aplicación de autenticación o ingresa la clave de configuración.
                    </p>
                </div>

                <div class="bg-white p-4 inline-block rounded-lg shadow-sm" v-html="qrCode" />

                <div v-if="setupKey" class="mt-4 text-sm">
                    <p class="font-semibold text-slate-700">
                        Clave de Configuración: <span v-html="setupKey" class="font-mono bg-slate-200 px-2 py-1 rounded ml-2"></span>
                    </p>
                </div>

                <div v-if="confirming" class="mt-6 max-w-xs">
                    <n-form-item label="Código" :validation-status="confirmationForm.errors.code ? 'error' : undefined" :feedback="confirmationForm.errors.code">
                        <n-input
                            v-model:value="confirmationForm.code"
                            placeholder="123456"
                            inputmode="numeric"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />
                    </n-form-item>
                </div>
            </div>

            <div v-if="recoveryCodes.length > 0 && ! confirming" class="bg-slate-50 p-6 rounded-xl border border-slate-100 mb-6">
                <p class="text-sm font-semibold text-slate-800 mb-4">
                    Guarda estos códigos de recuperación en un administrador de contraseñas seguro. Se pueden usar para recuperar el acceso a tu cuenta si pierdes tu dispositivo de dos factores.
                </p>

                <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-slate-900 text-slate-300 p-4 rounded-lg">
                    <div v-for="code in recoveryCodes" :key="code">
                        {{ code }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-slate-100">
            <div v-if="! twoFactorEnabled">
                <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                    <n-button type="primary" :loading="enabling">
                        Habilitar
                    </n-button>
                </ConfirmsPassword>
            </div>

            <div v-else class="flex flex-wrap gap-3">
                <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                    <n-button
                        v-if="confirming"
                        type="primary"
                        :loading="enabling || confirmationForm.processing"
                    >
                        Confirmar
                    </n-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                    <n-button
                        v-if="recoveryCodes.length > 0 && ! confirming"
                        secondary
                    >
                        Regenerar Códigos
                    </n-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="showRecoveryCodes">
                    <n-button
                        v-if="recoveryCodes.length === 0 && ! confirming"
                        secondary
                    >
                        Mostrar Códigos
                    </n-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <n-button
                        v-if="confirming"
                        type="default"
                        :loading="disabling"
                    >
                        Cancelar
                    </n-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <n-button
                        v-if="! confirming"
                        type="error"
                        ghost
                        :loading="disabling"
                    >
                        Deshabilitar
                    </n-button>
                </ConfirmsPassword>
            </div>
        </div>
    </n-card>
</template>