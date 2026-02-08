<template>
  <AppLayout title="Crear Producto">
    <template #header>
        <div class="flex items-center gap-4">
            <div class="transform transition-transform active:scale-95">
                <Back />
            </div>
            <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                Nuevo Producto
            </h2>
        </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Configuración Naive UI (Estilo iOS) -->
        <n-config-provider :theme-overrides="iosThemeOverrides">
          
          <!-- Contenedor Principal con efecto Glass suave -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden p-8 transition-all">
            
            <!-- Encabezado de la Tarjeta -->
            <div class="mb-10 flex items-start gap-5">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm shrink-0">
                <!-- Icono Box/Product -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 leading-tight">Detalles del Producto</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed max-w-xl">
                   Registra un nuevo artículo en el inventario. Asegúrate de establecer precios y niveles de stock iniciales.
                </p>
              </div>
            </div>

            <n-form
              ref="formRef"
              :model="form"
              :rules="rules"
              label-placement="top"
              size="large"
              class="grid grid-cols-1 md:grid-cols-12 gap-8"
            >
              
              <!-- COLUMNA IZQUIERDA: Imagen (md:col-span-4) -->
              <div class="md:col-span-4 space-y-4">
                  <label class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1">Imagen del Producto</label>
                  
                  <div class="relative group">
                      <div 
                        class="w-full aspect-square rounded-3xl border-2 border-dashed border-gray-300 hover:border-indigo-400 bg-gray-50 flex flex-col items-center justify-center cursor-pointer transition-all overflow-hidden relative"
                        @click="triggerFileInput"
                      >
                          <!-- Previsualización -->
                          <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover" />
                          
                          <!-- Placeholder -->
                          <div v-else class="text-center p-6 transition-opacity group-hover:opacity-75">
                              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-3">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                  </svg>
                              </div>
                              <p class="text-sm font-medium text-gray-600">Subir imagen</p>
                              <p class="text-xs text-gray-400 mt-1">PNG, JPG hasta 2MB</p>
                          </div>

                          <!-- Overlay Hover -->
                          <div v-if="previewUrl" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                              <span class="text-white font-medium text-sm bg-white/20 backdrop-blur-md px-4 py-2 rounded-full">Cambiar</span>
                          </div>
                      </div>
                      <input 
                        type="file" 
                        ref="fileInputRef" 
                        class="hidden" 
                        accept="image/*"
                        @change="handleFileChange"
                      />
                  </div>
              </div>

              <!-- COLUMNA DERECHA: Campos (md:col-span-8) -->
              <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
                  
                  <!-- Nombre -->
                  <div class="md:col-span-2">
                      <n-form-item path="name">
                        <template #label>
                          <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Nombre del Producto</span>
                        </template>
                        <n-input 
                          v-model:value="form.name" 
                          placeholder="Ej. Copa mundial 2026" 
                          class="ios-input-transition"
                        />
                      </n-form-item>
                  </div>

                  <!-- SKU -->
                  <n-form-item path="sku">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">SKU (Código)</span>
                    </template>
                    <n-input 
                      v-model:value="form.sku" 
                      placeholder="Ej. AUD-001" 
                      class="ios-input-transition"
                    />
                  </n-form-item>

                  <!-- Estado (Activo/Inactivo) -->
                   <n-form-item path="is_active">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Estado Inicial</span>
                    </template>
                     <n-select
                      v-model:value="form.is_active"
                      :options="[{label: 'Activo', value: 1}, {label: 'Inactivo', value: 0}]"
                    />
                  </n-form-item>

                  <div class="md:col-span-2 my-2 border-t border-gray-100"></div>
                  
                  <!-- Precio Venta -->
                  <n-form-item path="sale_price">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Precio Público</span>
                    </template>
                    <n-input-number
                        v-model:value="form.sale_price"
                        :parse="parseCurrency"
                        :format="formatCurrency"
                        :show-button="false"
                        placeholder="$ 0.00"
                        class="ios-input-transition w-full text-lg font-bold"
                    >
                        <template #prefix>$</template>
                    </n-input-number>
                  </n-form-item>

                  <!-- Costo -->
                  <n-form-item path="cost_price">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Costo Interno</span>
                    </template>
                    <n-input-number
                        v-model:value="form.cost_price"
                        :parse="parseCurrency"
                        :format="formatCurrency"
                        :show-button="false"
                        placeholder="$ 0.00"
                        class="ios-input-transition w-full"
                    >
                         <template #prefix>$</template>
                    </n-input-number>
                  </n-form-item>

                  <div class="md:col-span-2 my-2 border-t border-gray-100"></div>

                  <!-- Stock -->
                  <n-form-item path="stock_quantity">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Stock Inicial</span>
                    </template>
                     <n-input-number 
                        v-model:value="form.stock_quantity" 
                        button-placement="both"
                        class="ios-input-transition w-full"
                        :min="0"
                    />
                  </n-form-item>

                  <!-- Alerta Stock -->
                  <n-form-item path="alert_threshold">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Alerta Stock Bajo</span>
                    </template>
                     <n-input-number 
                        v-model:value="form.alert_threshold" 
                        button-placement="both"
                        class="ios-input-transition w-full"
                        :min="0"
                    />
                  </n-form-item>

                  <!-- Descripción -->
                  <div class="md:col-span-2 mt-2">
                    <n-form-item path="description">
                        <template #label>
                        <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Descripción</span>
                        </template>
                        <n-input 
                        v-model:value="form.description" 
                        type="textarea"
                        placeholder="Detalles adicionales del producto..." 
                        class="ios-input-transition"
                        :autosize="{ minRows: 3, maxRows: 5 }"
                        />
                    </n-form-item>
                  </div>

              </div>

              <!-- Botones de Acción -->
              <div class="md:col-span-12 flex justify-end items-center gap-4 mt-8 pt-8 border-t border-gray-100">
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
                  Guardar Producto
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
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { 
  NConfigProvider, NForm, NFormItem, NInput, NInputNumber,
  NSelect, NButton
} from 'naive-ui';

const form = useForm({
  name: '',
  sku: '',
  description: '',
  sale_price: 0,
  cost_price: 0,
  stock_quantity: 0,
  alert_threshold: 5,
  is_active: 1,
  image: null, // Campo para el archivo
});

const formRef = ref(null);
const fileInputRef = ref(null);
const previewUrl = ref(null);

const rules = {
  name: { required: true, message: 'El nombre es requerido', trigger: 'blur' },
  sale_price: { required: true, type: 'number', message: 'Requerido', trigger: ['input', 'blur'] },
  cost_price: { required: true, type: 'number', message: 'Requerido', trigger: ['input', 'blur'] },
  stock_quantity: { required: true, type: 'number', message: 'Requerido', trigger: ['input', 'blur'] },
};

// Utilidades para Inputs de Moneda
const parseCurrency = (input) => {
    const nums = input.replace(/(,|\$|\s)/g, '').trim();
    if (/^\d+(\.(\d+)?)?$/.test(nums)) return Number(nums);
    return nums === '' ? null : Number.NaN;
};
const formatCurrency = (value) => {
    if (value === null || value === undefined) return '';
    return value.toLocaleString('en-US'); // Formato simple 1,000.00
};

// Manejo de Imagen
const triggerFileInput = () => {
    fileInputRef.value.click();
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = (e) => {
  e.preventDefault();
  formRef.value?.validate((errors) => {
    if (!errors) {
      // Inertia convierte automáticamente form con archivos a FormData
      form.post(route('products.store'), {
          forceFormData: true,
      });
    }
  });
};

const cancel = () => {
  window.history.back(); 
};

// Tema iOS / Apple-Like (Ajustado para productos)
const iosThemeOverrides = {
  common: {
    primaryColor: '#4F46E5', // Indigo para productos (diferenciar de usuarios)
    primaryColorHover: '#4338ca',
    primaryColorPressed: '#3730a3',
    borderRadius: '16px',
  },
  Input: {
    color: '#F2F2F7', 
    colorFocus: '#FFFFFF',
    border: '1px solid transparent',
    borderFocus: '1px solid #4F46E5',
    borderRadius: '12px',
    boxShadowFocus: '0 4px 12px rgba(79, 70, 229, 0.15)',
    heightLarge: '48px', 
    textColor: '#1F2937',
    placeholderColor: '#9CA3AF',
  },
  InputNumber: {
    // Estilos similares al Input normal
    peers: {
        Input: {
            color: '#F2F2F7',
            colorFocus: '#FFFFFF',
            border: '1px solid transparent',
            borderFocus: '1px solid #4F46E5',
            borderRadius: '12px',
            heightLarge: '48px',
        }
    }
  },
  Select: {
    peers: {
      InternalSelection: {
        color: '#F2F2F7',
        colorActive: '#FFFFFF',
        border: '1px solid transparent',
        borderActive: '1px solid #4F46E5',
        borderRadius: '12px',
        boxShadowActive: '0 4px 12px rgba(79, 70, 229, 0.15)',
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
:deep(.ios-input-transition .n-input__input-el),
:deep(.ios-input-transition .n-input__state-border),
:deep(.ios-input-transition .n-input__placeholder) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.ios-btn-shadow) {
    box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);
}
</style>