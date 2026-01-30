<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { NCard, NForm, NFormItem, NInput, NButton, NAvatar, NUpload, NDivider, useMessage } from 'naive-ui';

const props = defineProps({
    user: Object,
});

const message = useMessage();

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => {
            clearPhotoFileInput();
            message.success('Perfil actualizado correctamente');
        },
        onError: () => {
             message.error('Por favor revisa los errores en el formulario');
        }
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
            message.info('Foto de perfil eliminada');
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <n-card title="Información del Perfil" size="large" :bordered="false">
        <template #header-extra>
            <span class="text-slate-400 text-xs uppercase tracking-wider font-bold">Cuenta Personal</span>
        </template>
        
        <p class="text-slate-500 mb-6 text-sm">
            Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.
        </p>

        <n-form @submit.prevent="updateProfileInformation">
            
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="mb-6 flex items-center gap-6">
                <!-- Input Oculto -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <div class="relative group">
                    <!-- Current Profile Photo -->
                    <n-avatar
                        v-show="!photoPreview"
                        round
                        :size="80"
                        :src="user.profile_photo_url"
                        class="border-4 border-slate-50 shadow-lg"
                    />

                    <!-- New Profile Photo Preview -->
                    <n-avatar
                        v-show="photoPreview"
                        round
                        :size="80"
                        :src="photoPreview"
                        class="border-4 border-slate-50 shadow-lg"
                    />
                </div>

                <div class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-slate-700">Foto de Perfil</span>
                    <div class="flex gap-2">
                        <n-button size="small" secondary @click.prevent="selectNewPhoto">
                            Cambiar Foto
                        </n-button>
                        <n-button 
                            v-if="user.profile_photo_path" 
                            size="small" 
                            type="error" 
                            ghost 
                            @click.prevent="deletePhoto"
                        >
                            Eliminar
                        </n-button>
                    </div>
                    <span v-if="form.errors.photo" class="text-xs text-red-500 mt-1">{{ form.errors.photo }}</span>
                </div>
            </div>

            <!-- Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <n-form-item label="Nombre" :validation-status="form.errors.name ? 'error' : undefined" :feedback="form.errors.name">
                    <n-input 
                        v-model:value="form.name" 
                        placeholder="Tu nombre completo" 
                        size="large"
                    />
                </n-form-item>

                <!-- Email -->
                <n-form-item label="Correo Electrónico" :validation-status="form.errors.email ? 'error' : undefined" :feedback="form.errors.email">
                    <n-input 
                        v-model:value="form.email" 
                        placeholder="tu@correo.com" 
                        size="large"
                    />
                </n-form-item>
            </div>

            <!-- Email Verification -->
            <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null" class="mt-2 p-4 bg-orange-50 rounded-xl border border-orange-100">
                <p class="text-sm text-orange-700">
                    Tu dirección de correo no está verificada.
                    <button
                        class="underline font-bold hover:text-orange-900 ml-1"
                        @click.prevent="sendEmailVerification"
                    >
                        Haz clic aquí para reenviar el correo de verificación.
                    </button>
                </p>
                <div v-show="verificationLinkSent" class="mt-2 text-sm font-bold text-green-600">
                    Se ha enviado un nuevo enlace de verificación a tu correo.
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end mt-6 pt-6 border-t border-slate-100">
                <n-button 
                    type="primary" 
                    size="large" 
                    :loading="form.processing"
                    @click="updateProfileInformation"
                >
                    Guardar Cambios
                </n-button>
            </div>
        </n-form>
    </n-card>
</template>