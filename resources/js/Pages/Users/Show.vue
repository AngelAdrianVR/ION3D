<template>
  <AppLayout title="Detalles de Usuario">
    <template #header>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="transform transition-transform active:scale-95">
                    <Back :route="route('users.index')" />
                </div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                    Perfil de Usuario
                </h2>
            </div>

            <!-- Botón Crear Nuevo Usuario (PERMISO: users.create) -->
            <Link v-if="can('users.create')" :href="route('users.create')">
                <button class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-blue-500/30 transition-all transform active:scale-95 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Nuevo Usuario</span>
                </button>
            </Link>
        </div>
    </template>

    <div class="py-8 min-h-screen relative">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Configuración Naive UI -->
        <n-config-provider :theme-overrides="iosThemeOverrides">
          
          <!-- TARJETA PRINCIPAL (Glassmorphism) -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-[2rem] overflow-hidden transition-all relative z-10">
            
            <!-- FONDO DECORATIVO SUPERIOR -->
            <div class="h-32 bg-gradient-to-r from-blue-50 to-indigo-50 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-blue-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-indigo-400/10 rounded-full blur-3xl"></div>
            </div>

            <!-- CONTENIDO DEL PERFIL -->
            <div class="px-6 pb-8 md:px-10 md:pb-12 relative -mt-16">
                
                <!-- HEADER: FOTO Y NOMBRE (Adaptable Móvil) -->
                <div class="flex flex-col md:flex-row items-center md:items-end gap-6 mb-8 text-center md:text-left">
                    
                    <!-- FOTO DE PERFIL CON INTERACCIÓN -->
                    <div class="relative group shrink-0">
                        <!-- Anillo exterior -->
                        <div class="p-1.5 bg-white rounded-[2rem] shadow-lg shadow-blue-900/5">
                            <div 
                                class="w-32 h-32 md:w-40 md:h-40 rounded-[1.7rem] overflow-hidden cursor-zoom-in transition-transform duration-300 ease-out active:scale-95 relative"
                                @click="openPhotoModal"
                            >
                                <img 
                                    :src="user.profile_photo_url" 
                                    :alt="user.name"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    :class="{'grayscale opacity-80': !user.is_active}"
                                >
                                <!-- Overlay 'Ver' al hover -->
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0h-3" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Badge de estado dinámico -->
                        <div 
                            class="absolute bottom-4 right-0 md:-right-2 w-6 h-6 border-4 border-white rounded-full transition-colors duration-300"
                            :class="user.is_active ? 'bg-green-500' : 'bg-red-500'"
                            :title="user.is_active ? 'Usuario Activo' : 'Usuario Inactivo'"
                        ></div>
                    </div>

                    <!-- NOMBRE Y ROL -->
                    <div class="flex-1 mb-2">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight leading-tight flex flex-col md:block">
                            {{ user.name }}
                            <!-- Etiqueta de Inactivo visible junto al nombre -->
                            <span v-if="!user.is_active" class="md:ml-2 inline-block md:inline mt-2 md:mt-0 px-3 py-1 text-xs font-bold text-red-600 bg-red-100 rounded-full align-middle border border-red-200">
                                CUENTA INACTIVA
                            </span>
                        </h1>
                        <div class="mt-2 flex flex-wrap gap-2 justify-center md:justify-start items-center">
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                {{ user.roles[0]?.name || 'Sin Rol' }}
                            </span>
                            <span class="text-sm text-gray-400 font-medium">
                                Miembro desde {{ formatDate(user.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="mt-4 md:mt-0 flex flex-wrap justify-center gap-3">
                        
                        <!-- Botón Activar/Desactivar (PERMISO: users.status) -->
                        <button
                            v-if="can('users.status')"
                            @click="toggleStatus"
                            class="px-5 py-2.5 text-sm font-bold rounded-full shadow-lg transition-all duration-300 active:scale-95 flex items-center gap-2 border"
                            :class="user.is_active 
                                ? 'bg-white text-red-600 border-red-100 hover:bg-red-50 hover:border-red-200' 
                                : 'bg-green-600 text-white border-transparent hover:bg-green-500 shadow-green-500/30'"
                        >
                            <svg v-if="user.is_active" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ user.is_active ? 'Desactivar' : 'Activar' }}
                        </button>

                        <!-- Botón Editar (PERMISO: users.edit) -->
                        <Link v-if="can('users.edit')" :href="route('users.edit', user.id)">
                            <button class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-full shadow-lg hover:bg-gray-800 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 active:scale-95 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Editar
                            </button>
                        </Link>

                        <!-- Botón Eliminar (PERMISO: users.delete) -->
                        <button
                            v-if="can('users.delete')"
                            @click="showDeleteModal = true"
                            class="px-3 py-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-colors active:scale-95 flex items-center justify-center border border-transparent hover:border-red-200"
                            title="Eliminar usuario"
                        >
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>

                <!-- SEPARADOR -->
                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent my-8"></div>

                <!-- GRID DE DETALLES -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Información de Contacto -->
                    <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Información de Contacto</h3>
                        
                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-500 shadow-sm border border-gray-50 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Email</p>
                                    <p class="text-gray-900 font-medium break-all">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                         <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Detalles de Sistema</h3>
                         
                         <div class="space-y-5">
                             <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-purple-500 shadow-sm border border-gray-50 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Estado de Verificación</p>
                                    <div class="mt-1">
                                        <span v-if="user.email_verified_at" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Verificado
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-yellow-100 text-yellow-700">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                            </div>

                             <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-500 shadow-sm border border-gray-50 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Última Actualización</p>
                                    <p class="text-gray-900 font-medium">{{ formatDate(user.updated_at) }}</p>
                                </div>
                            </div>
                         </div>
                    </div>

                </div>

            </div>
          </div>
        </n-config-provider>

      </div>
    </div>

    <!-- MODAL FOTO FULL SCREEN (Estilo iOS Lightbox) -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div 
            v-if="isPhotoModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/30 backdrop-blur-2xl"
            @click="closePhotoModal"
        >
            <!-- Botón Cerrar -->
            <button class="absolute top-6 right-6 p-2 rounded-full bg-gray-200/50 hover:bg-gray-300/50 text-gray-800 transition-colors backdrop-blur-md z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Imagen Grande -->
            <div class="relative max-w-5xl w-full max-h-[90vh] flex justify-center" @click.stop>
                <img 
                    :src="user.profile_photo_url" 
                    :alt="user.name"
                    class="rounded-3xl shadow-2xl object-contain max-h-[85vh] w-auto animate-float-in border border-white/20"
                >
            </div>
        </div>
    </Transition>

    <!-- MODAL CONFIRMACIÓN ELIMINAR -->
    <n-modal v-model:show="showDeleteModal" transform-origin="center">
        <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
            <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar usuario?</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">
            Esta acción es irreversible. ¿Seguro que deseas eliminar a este usuario?
            </p>
            <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="deleteUser" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
            </div>
        </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { NConfigProvider, NModal } from 'naive-ui';

const props = defineProps({
    user: Object,
});

const page = usePage();

// --- ESTADO MODAL FOTO ---
const isPhotoModalOpen = ref(false);

const openPhotoModal = () => {
    isPhotoModalOpen.value = true;
    document.body.style.overflow = 'hidden'; // Prevenir scroll
};

const closePhotoModal = () => {
    isPhotoModalOpen.value = false;
    document.body.style.overflow = ''; // Restaurar scroll
};

// --- ESTADO MODAL ELIMINAR ---
const showDeleteModal = ref(false);

// --- PERMISOS ---
const can = (permission) => {
    return page.props.auth?.permissions?.includes(permission) || page.props.auth?.can?.[permission] || false;
};

// --- ACCIONES ---
const toggleStatus = () => {
    // router.put conserva el scroll y el estado para una experiencia fluida
    router.put(route('users.status', props.user.id), {}, {
        preserveScroll: true
    });
};

const deleteUser = () => {
    router.delete(route('users.destroy', props.user.id), {
        onSuccess: () => { showDeleteModal.value = false; }
    });
};

// --- HELPERS ---
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    }).format(date);
};

// --- ESTILOS THEME (Consistentes con Create/Edit) ---
const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF', 
    borderRadius: '16px', 
  }
};
</script>

<style scoped>
/* Animación suave para la entrada de la imagen modal */
@keyframes float-in {
    0% { transform: translateY(20px) scale(0.95); opacity: 0; }
    100% { transform: translateY(0) scale(1); opacity: 1; }
}
.animate-float-in {
    animation: float-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Efecto de lupa personalizado */
.cursor-zoom-in {
    cursor: zoom-in;
}
</style>