<template>
  <AppLayout title="Editar Producto">
    <template #header>
        <div class="flex items-center gap-4">
            <div class="transform transition-transform active:scale-95">
                <Back />
            </div>
            <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                Editar Producto
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
                <!-- Icono Edit/Pencil -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 leading-tight">Editar: {{ product.name }}</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed max-w-xl">
                   Modifica los detalles del producto. Si subes una nueva imagen, la anterior será reemplazada.
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
              
              <!-- COLUMNA IZQUIERDA: Imágenes y Video (md:col-span-4) -->
              <div class="md:col-span-4 space-y-6">
                  
                  <!-- Sección: Imágenes -->
                  <div>
                      <label class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1">Imágenes del Producto</label>
                      <p class="text-gray-400 text-xs ml-1 mb-3">Galería de imágenes. Puedes eliminar o agregar más.</p>
                      
                      <!-- Grid de imágenes existentes + nuevas -->
                      <div v-if="allImagePreviews.length > 0" class="grid grid-cols-3 gap-2 mb-3">
                          <div 
                              v-for="(img, index) in allImagePreviews" 
                              :key="img.key"
                              class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100"
                          >
                              <img :src="img.url" class="w-full h-full object-cover" />
                              
                              <!-- Overlay con botón eliminar -->
                              <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                  <button 
                                      @click="removeImageByIndex(index)"
                                      class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors"
                                      title="Eliminar imagen"
                                  >
                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                      </svg>
                                  </button>
                              </div>
                          </div>
                      </div>

                      <!-- Mensaje si no hay imágenes -->
                      <div v-else class="text-center py-4 text-gray-400 text-sm bg-gray-50 rounded-2xl mb-3">
                          No hay imágenes. Agrega la primera.
                      </div>

                      <!-- Botón para agregar más imágenes -->
                      <div 
                        class="w-full aspect-video rounded-2xl border-2 border-dashed border-gray-300 hover:border-indigo-400 bg-gray-50 flex flex-col items-center justify-center cursor-pointer transition-all"
                        @click="triggerImageInput"
                      >
                          <div class="text-center p-4 transition-opacity">
                              <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                  </svg>
                              </div>
                              <p class="text-sm font-medium text-gray-600">Agregar imágenes</p>
                              <p class="text-xs text-gray-400 mt-1">PNG, JPG hasta 5MB c/u</p>
                          </div>
                      </div>
                      <input 
                        type="file" 
                        ref="imageInputRef" 
                        class="hidden" 
                        accept="image/*"
                        multiple
                        @change="handleImagesChange"
                      />
                  </div>

                  <!-- Sección: Video -->
                  <div>
                      <label class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1">Video del Producto</label>
                      <p class="text-gray-400 text-xs ml-1 mb-3">Opcional. Formatos: MP4, MOV, WebM (máx. 50MB).</p>
                      
                      <!-- Video existente o preview nuevo -->
                      <div v-if="videoPreviewUrl" class="relative group rounded-2xl overflow-hidden border border-gray-200 mb-3">
                          <video :src="videoPreviewUrl" class="w-full aspect-video object-cover" controls></video>
                          <button 
                              @click="removeVideo"
                              class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-600"
                              title="Eliminar video"
                          >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                              </svg>
                          </button>
                      </div>

                      <!-- Botón para agregar/reemplazar video -->
                      <div 
                        v-if="!videoPreviewUrl"
                        class="w-full aspect-video rounded-2xl border-2 border-dashed border-gray-300 hover:border-purple-400 bg-gray-50 flex flex-col items-center justify-center cursor-pointer transition-all"
                        @click="triggerVideoInput"
                      >
                          <div class="text-center p-4 transition-opacity">
                              <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                  </svg>
                              </div>
                              <p class="text-sm font-medium text-gray-600">Agregar video</p>
                              <p class="text-xs text-gray-400 mt-1">MP4, MOV, WebM</p>
                          </div>
                      </div>
                      <input 
                        type="file" 
                        ref="videoInputRef" 
                        class="hidden" 
                        accept="video/mp4,video/mov,video/ogg,video/quicktime,video/webm"
                        @change="handleVideoChange"
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
                          placeholder="Ej. Audífonos Bluetooth Pro" 
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
                      :status="form.errors.sku ? 'error' : undefined"
                    />
                    <div v-if="form.errors.sku" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.sku }}</div>
                  </n-form-item>

                  <!-- Estado (Activo/Inactivo) -->
                   <n-form-item path="is_active">
                    <template #label>
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Estado Actual</span>
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
                      <span class="text-gray-500 font-semibold text-xs uppercase tracking-wider ml-1 mb-1">Stock Actual</span>
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
                  Guardar Cambios
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
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { 
  NConfigProvider, NForm, NFormItem, NInput, NInputNumber,
  NSelect, NButton
} from 'naive-ui';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const form = useForm({
  _method: 'PUT',
  name: props.product.name,
  sku: props.product.sku,
  description: props.product.description,
  sale_price: Number(props.product.sale_price),
  cost_price: Number(props.product.cost_price),
  stock_quantity: Number(props.product.stock_quantity),
  alert_threshold: Number(props.product.alert_threshold),
  is_active: props.product.is_active ? 1 : 0,
  images: [],           // Nuevas imágenes a subir (Files)
  delete_image_ids: [], // IDs de imágenes existentes a eliminar
  video: null,          // Nuevo video a subir (File)
  delete_video: false,  // Flag para eliminar video existente
});

const formRef = ref(null);
const imageInputRef = ref(null);
const videoInputRef = ref(null);

// --- Estado de imágenes ---
// Lista combinada: imágenes existentes del servidor + nuevas previews locales
const existingImages = ref(
    (props.product.images || []).map(img => ({
        id: img.id,
        url: img.url,
        name: img.name,
        isExisting: true,
        deleted: false, // marcado para eliminar
        key: 'existing-' + img.id,
    }))
);
const newImagePreviews = ref([]); // { url, key }

// Preview combinado (existente no eliminadas + nuevas)
const allImagePreviews = computed(() => {
    const existing = existingImages.value.filter(img => !img.deleted);
    return [...existing, ...newImagePreviews.value];
});

// --- Estado de video ---
const existingVideoUrl = ref(props.product.video_url || null);
const newVideoPreviewUrl = ref(null);
const videoMarkedForDeletion = ref(false);

const videoPreviewUrl = computed(() => {
    if (videoMarkedForDeletion.value) return null;
    if (newVideoPreviewUrl.value) return newVideoPreviewUrl.value;
    return existingVideoUrl.value;
});

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
    return value.toLocaleString('en-US'); 
};

// Manejo de Imágenes
const triggerImageInput = () => {
    imageInputRef.value.click();
};

const handleImagesChange = (event) => {
    const files = Array.from(event.target.files);
    files.forEach((file, i) => {
        form.images.push(file);
        newImagePreviews.value.push({
            url: URL.createObjectURL(file),
            key: 'new-' + Date.now() + '-' + i,
        });
    });
    event.target.value = '';
};

const removeImageByIndex = (index) => {
    const existingFiltered = existingImages.value.filter(img => !img.deleted);
    if (index < existingFiltered.length) {
        // Es una imagen existente → marcarla para eliminar
        const img = existingFiltered[index];
        const realIndex = existingImages.value.findIndex(ei => ei.id === img.id);
        existingImages.value[realIndex].deleted = true;
        form.delete_image_ids.push(img.id);
    } else {
        // Es una nueva preview → quitarla
        const newIndex = index - existingFiltered.length;
        form.images.splice(newIndex, 1);
        URL.revokeObjectURL(newImagePreviews.value[newIndex].url);
        newImagePreviews.value.splice(newIndex, 1);
    }
};

// Manejo de Video
const triggerVideoInput = () => {
    videoInputRef.value.click();
};

const handleVideoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.video = file;
        form.delete_video = false;
        videoMarkedForDeletion.value = false;
        newVideoPreviewUrl.value = URL.createObjectURL(file);
    }
    event.target.value = '';
};

const removeVideo = () => {
    if (newVideoPreviewUrl.value) {
        // Se está quitando un video recién seleccionado
        URL.revokeObjectURL(newVideoPreviewUrl.value);
        newVideoPreviewUrl.value = null;
        form.video = null;
    }
    if (existingVideoUrl.value && !videoMarkedForDeletion.value) {
        // Se está marcando el video existente para eliminar
        videoMarkedForDeletion.value = true;
        form.delete_video = true;
    }
};

const submit = (e) => {
  e.preventDefault();
  formRef.value?.validate((errors) => {
    if (!errors) {
      // Usamos POST con _method: PUT para permitir envío de FormData (archivos)
      form.post(route('products.update', props.product.id), {
          forceFormData: true,
      });
    }
  });
};

const cancel = () => {
  window.history.back(); 
};

// Tema iOS / Apple-Like 
const iosThemeOverrides = {
  common: {
    primaryColor: '#4F46E5', // Indigo
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