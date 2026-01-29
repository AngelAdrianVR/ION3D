<template>
  <AppLayout title="Editar Usuario">
    <template #header>
        <div class="flex items-center gap-4">
            <div class="transform transition-transform active:scale-95">
                <Back />
            </div>
            <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                Editar Usuario
            </h2>
        </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Configuración Naive UI (Estilo iOS) -->
        <n-config-provider :theme-overrides="iosThemeOverrides">
          
          <!-- Contenedor Principal con efecto Glass suave -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden p-8 transition-all">
            
            <!-- Encabezado de la Tarjeta -->
            <div class="mb-10 flex items-start gap-5">
              <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shadow-sm shrink-0">
                <!-- Icono de Edición -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 leading-tight">Actualizar Información</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed max-w-xl">
                   Modifica los datos del usuario. Deja la contraseña en blanco si no deseas cambiarla.
                </p>
              </div>
            </div>

            <n-form
              ref="formRef"
              :model="form"
              :rules="rules"
              label-placement="top"
              size="large"
              class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2"
            >
              <!-- Nombre -->
              <n-form-item path="name">
                <template #label>
                  <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Nombre Completo</span>
                </template>
                <n-input 
                  v-model:value="form.name" 
                  placeholder="Ej. Ana García" 
                  class="ios-input-transition"
                />
              </n-form-item>

              <!-- Email -->
              <n-form-item path="email">
                <template #label>
                  <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Correo Electrónico</span>
                </template>
                <n-input 
                  v-model:value="form.email" 
                  placeholder="correo@ejemplo.com" 
                  class="ios-input-transition"
                />
              </n-form-item>

              <!-- Rol -->
              <n-form-item path="role">
                <template #label>
                  <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Rol Asignado</span>
                </template>
                <n-select
                  v-model:value="form.role"
                  :options="roleOptions"
                  placeholder="Seleccionar rol"
                />
              </n-form-item>

              <!-- Separador visual para cambio de contraseña -->
              <div class="md:col-span-2 mt-4 mb-2">
                 <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">
                    Seguridad (Opcional)
                 </h4>
              </div>

              <!-- Contraseña -->
              <n-form-item path="password">
                <template #label>
                  <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Nueva Contraseña</span>
                </template>
                <div class="flex gap-2 w-full">
                    <n-input
                      v-model:value="form.password"
                      type="password"
                      show-password-on="click"
                      placeholder="Dejar vacío para mantener"
                      class="ios-input-transition flex-grow"
                      @input="handlePasswordInput"
                    />
                    <!-- Botón Generar -->
                    <button 
                      type="button"
                      @click="generatePassword"
                      class="shrink-0 w-12 h-[48px] rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 active:scale-95 transition-all flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-gray-200"
                      title="Generar contraseña nueva"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                    </button>
                </div>
              </n-form-item>

              <!-- Confirmar Contraseña (Solo visible si escribe contraseña) -->
              <n-form-item path="password_confirmation" v-if="form.password">
                <template #label>
                  <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Confirmar Contraseña</span>
                </template>
                <n-input 
                  v-model:value="form.password_confirmation" 
                  type="password"
                  show-password-on="click"
                  placeholder="Repite la contraseña" 
                  class="ios-input-transition"
                />
              </n-form-item>

              <!-- Botones de Acción -->
              <div class="md:col-span-2 flex justify-end items-center gap-4 mt-8 pt-8 border-t border-gray-100">
                <button 
                  @click="cancel"
                  class="px-6 py-3 rounded-full font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all active:scale-95 text-sm"
                >
                  Cancelar
                </button>
                <n-button 
                  type="primary" 
                  :loading="form.processing" 
                  @click="submit"
                  class="ios-btn-shadow px-8"
                  size="large"
                  icon-placement="right"
                >
                  <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </template>
                  Actualizar Usuario
                </n-button>
              </div>

            </n-form>
          </div>
        </n-config-provider>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { 
  NConfigProvider, NForm, NFormItem, NInput, 
  NSelect, NButton
} from 'naive-ui';

// Props recibidas desde el controlador (Edit function)
const props = defineProps({
    user: Object,
});

const roleOptions = [
  { label: 'Super Admin', value: 'Super admin' },
  { label: 'Vendedor', value: 'Vendedor' },
  { label: 'Diseñador', value: 'Diseñador' }
];

// Inicializamos el formulario con los datos existentes
const form = useForm({
  _method: 'PUT', // Necesario para rutas resource update en Laravel
  name: props.user.name,
  email: props.user.email,
  role: props.user.roles && props.user.roles.length > 0 ? props.user.roles[0].name : null,
  password: '',
  password_confirmation: '',
});

const formRef = ref(null);

const rules = {
  name: { required: true, message: 'Requerido', trigger: 'blur' },
  email: { required: true, message: 'Requerido', trigger: ['input', 'blur'] },
  role: { required: true, message: 'Requerido', trigger: ['blur', 'change'] },
  // Password no es requerido en edición, pero si se llena, debe confirmarse
  password_confirmation: {
    validator: (rule, value) => {
      if (form.password && value !== form.password) {
        return new Error("Las contraseñas no coinciden");
      }
      return true;
    },
    trigger: ['blur', 'input']
  }
};

const generatePassword = () => {
  const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
  let password = "";
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  form.password = password;
  form.password_confirmation = password; // Llenamos confirmación automáticamente
};

// Si el usuario borra la contraseña manualmente, limpiamos la confirmación
const handlePasswordInput = (value) => {
    if (!value) {
        form.password_confirmation = '';
    }
};

const submit = (e) => {
  e.preventDefault();
  formRef.value?.validate((errors) => {
    if (!errors) {
      form.post(route('users.update', props.user.id), {
          onSuccess: () => {
              // Opcional: Notificación o lógica extra
          }
      });
    }
  });
};

const cancel = () => {
  window.history.back();
};

// Tema iOS / Apple-Like (Mismo que Create.vue)
const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF', 
    primaryColorHover: '#0056b3',
    primaryColorPressed: '#004080',
    borderRadius: '16px', 
  },
  Input: {
    color: '#F2F2F7', 
    colorFocus: '#FFFFFF', 
    border: '1px solid transparent', 
    borderFocus: '1px solid #007AFF', 
    borderRadius: '12px',
    boxShadowFocus: '0 4px 12px rgba(0, 122, 255, 0.15)', 
    heightLarge: '48px', 
    textColor: '#1F2937',
    placeholderColor: '#9CA3AF',
  },
  Select: {
    peers: {
      InternalSelection: {
        color: '#F2F2F7',
        colorActive: '#FFFFFF',
        border: '1px solid transparent',
        borderActive: '1px solid #007AFF',
        borderRadius: '12px',
        boxShadowActive: '0 4px 12px rgba(0, 122, 255, 0.15)',
        heightLarge: '48px',
      }
    }
  },
  Button: {
      borderRadiusLarge: '50px',
      fontWeight: '600',
      fontSizeLarge: '15px'
  },
  Form: {
      labelTextColor: '#6B7280',
      labelFontWeight: '600'
  }
};
</script>

<style scoped>
/* Transición suave para los inputs al cambiar de estado */
:deep(.ios-input-transition .n-input__input-el),
:deep(.ios-input-transition .n-input__state-border),
:deep(.ios-input-transition .n-input__placeholder) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Sombra específica para el botón principal */
:deep(.ios-btn-shadow) {
    box-shadow: 0 4px 14px 0 rgba(0, 118, 255, 0.39);
}
</style>