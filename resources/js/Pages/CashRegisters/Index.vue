<template>
  <AppLayout title="Cajas Registradoras">
    <template #header>
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Cajas Registradoras
            </h2>
            <button 
                @click="openCreateModal"
                class="px-4 py-2 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-lg hover:bg-gray-800 transition-all flex items-center gap-2 active:scale-95"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nueva Caja
            </button>
        </div>
    </template>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <n-config-provider :theme-overrides="iosThemeOverrides">
                <n-message-provider>
                    
                    <!-- Grid de Cajas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <div 
                            v-for="register in registers" 
                            :key="register.id"
                            @click="goToDetail(register.id)"
                            class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all relative group cursor-pointer"
                        >
                            <!-- Badge ID -->
                            <div class="absolute top-6 right-6 text-xs font-mono text-gray-300">
                                #{{ register.id }}
                            </div>

                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-600 border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ register.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ register.location || 'Sin ubicación' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    <span 
                                        class="w-2.5 h-2.5 rounded-full"
                                        :class="register.is_active ? 'bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.4)]' : 'bg-red-400'"
                                    ></span>
                                    <span class="text-sm font-bold text-gray-600">
                                        {{ register.is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    <button 
                                        @click.stop="openEditModal(register)"
                                        class="p-2 rounded-xl text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors z-10"
                                        title="Editar"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    
                                    <button 
                                        v-if="register.id !== 1"
                                        @click.stop="confirmDelete(register)"
                                        class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors z-10"
                                        title="Eliminar"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card de Crear Nueva -->
                        <button 
                            @click="openCreateModal"
                            class="rounded-3xl p-6 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50/30 transition-all min-h-[200px]"
                        >
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3 group-hover:bg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </div>
                            <span class="font-bold">Agregar Caja</span>
                        </button>

                    </div>

                    <!-- MODAL FORMULARIO -->
                    <n-modal v-model:show="showModal">
                        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ isEditing ? 'Editar Caja' : 'Nueva Caja' }}
                                </h3>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitForm">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre de la Caja</label>
                                        <input 
                                            v-model="form.name" 
                                            type="text" 
                                            class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Ej: Caja Principal"
                                        >
                                        <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Ubicación / Referencia</label>
                                        <input 
                                            v-model="form.location" 
                                            type="text" 
                                            class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Ej: Entrada, Planta Alta..."
                                        >
                                    </div>

                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <span class="text-sm font-bold text-gray-700">Estado Activo</span>
                                        <n-switch v-model:value="form.is_active" />
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end gap-3">
                                    <button 
                                        type="button" 
                                        @click="showModal = false" 
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-xl font-bold transition-colors"
                                    >
                                        Cancelar
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="form.processing"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all disabled:opacity-50"
                                    >
                                        {{ isEditing ? 'Actualizar' : 'Guardar' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </n-modal>

                    <!-- MODAL CONFIRMACIÓN ELIMINAR -->
                    <n-modal v-model:show="showDeleteModal">
                        <div class="bg-white rounded-2xl p-6 w-80 text-center shadow-2xl">
                            <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-2">¿Eliminar Caja?</h3>
                            <p class="text-sm text-gray-500 mb-6">Esta acción es irreversible si la caja no tiene historial.</p>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="showDeleteModal = false" class="py-2 text-gray-600 font-bold hover:bg-gray-50 rounded-xl">Cancelar</button>
                                <button @click="deleteRegister" class="py-2 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 shadow-lg shadow-red-500/30">Eliminar</button>
                            </div>
                        </div>
                    </n-modal>

                    <!-- TOAST HANDLER -->
                    <ToastHandler />

                </n-message-provider>
            </n-config-provider>
        </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, defineComponent, watch } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { NConfigProvider, NModal, NSwitch, NMessageProvider, useMessage } from 'naive-ui';

const props = defineProps({
    registers: Array
});

// Componente auxiliar para manejar los mensajes flash
const ToastHandler = defineComponent({
    setup() {
        const message = useMessage();
        const page = usePage();
        
        watch(() => page.props.flash, (flash) => {
            if (flash?.success) message.success(flash.success);
            if (flash?.error) message.error(flash.error);
        }, { deep: true, immediate: true });

        return () => null;
    }
});

// Estados
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const registerToDelete = ref(null);

const form = useForm({
    id: null,
    name: '',
    location: '',
    is_active: true
});

// Métodos
const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (register) => {
    isEditing.value = true;
    form.id = register.id;
    form.name = register.name;
    form.location = register.location;
    form.is_active = Boolean(register.is_active); 
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('cash-registers.update', form.id), {
            onSuccess: () => showModal.value = false
        });
    } else {
        form.post(route('cash-registers.store'), {
            onSuccess: () => showModal.value = false
        });
    }
};

const confirmDelete = (register) => {
    if (register.id === 1) return; 
    registerToDelete.value = register;
    showDeleteModal.value = true;
};

const deleteRegister = () => {
    if (!registerToDelete.value) return;
    router.delete(route('cash-registers.destroy', registerToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            registerToDelete.value = null;
        }
    });
};

const goToDetail = (id) => {
    router.get(route('cash-registers.show', id));
};

const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF',
    borderRadius: '12px',
  },
  Switch: {
      railColorActive: '#22c55e'
  }
};
</script>