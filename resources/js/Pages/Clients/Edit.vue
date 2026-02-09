<template>
  <AppLayout title="Editar Cliente">
    <template #header>
        <div class="flex items-center gap-4">
            <div class="transform transition-transform active:scale-95">
                <Back />
            </div>
            <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                Editar Cliente
            </h2>
        </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Configuración Naive UI (Estilo iOS) -->
        <n-config-provider :theme-overrides="iosThemeOverrides">
          
          <!-- Contenedor Principal con efecto Glass suave -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden p-8 transition-all">
            
            <!-- Encabezado de la Tarjeta -->
            <div class="mb-10 flex items-start gap-5">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm shrink-0">
                <!-- Icono Edit/User -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 leading-tight">Actualizar Información</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed max-w-xl">
                   Modifica los datos fiscales, de contacto o ajusta la línea de crédito del cliente.
                </p>
              </div>
            </div>

            <n-form
              ref="formRef"
              :model="form"
              :rules="rules"
              label-placement="top"
              size="large"
            >
              <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                  
                  <!-- COLUMNA IZQUIERDA: Datos Principales y Crédito (lg:col-span-5) -->
                  <div class="lg:col-span-5 space-y-6">
                      
                      <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                          <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4 flex items-center gap-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                              Datos Generales
                          </h4>

                          <!-- Nombre -->
                          <n-form-item path="name" label="Nombre Completo / Razón Social">
                            <n-input 
                              v-model:value="form.name" 
                              placeholder="Ej. Juan Pérez o Empresa S.A." 
                              class="ios-input-transition"
                            />
                          </n-form-item>

                          <!-- RFC / Tax ID -->
                          <n-form-item path="tax_id" label="RFC / Identificación Fiscal">
                            <n-input 
                              v-model:value="form.tax_id" 
                              placeholder="XAXX010101000" 
                              class="ios-input-transition font-mono"
                              uppercase
                            />
                          </n-form-item>

                          <div class="grid grid-cols-2 gap-4">
                              <!-- Email -->
                              <n-form-item path="email" label="Correo Electrónico">
                                <n-input 
                                  v-model:value="form.email" 
                                  placeholder="cliente@email.com" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>

                              <!-- Teléfono -->
                              <n-form-item path="phone" label="Teléfono / WhatsApp">
                                <n-input 
                                  v-model:value="form.phone" 
                                  placeholder="55 1234 5678" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>
                          </div>
                      </div>

                      <div class="bg-blue-50/30 p-6 rounded-2xl border border-blue-100">
                          <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wide mb-4 flex items-center gap-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" /><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" /></svg>
                              Configuración de Crédito
                          </h4>
                          
                          <!-- Límite de Crédito -->
                          <n-form-item path="credit_limit" label="Límite de Crédito Autorizado">
                            <n-input-number
                                v-model:value="form.credit_limit"
                                :parse="parseCurrency"
                                :format="formatCurrency"
                                :show-button="false"
                                placeholder="$ 0.00"
                                class="ios-input-transition w-full text-lg font-bold"
                            >
                                <template #prefix><span class="text-blue-600">$</span></template>
                            </n-input-number>
                          </n-form-item>
                          
                          <div class="flex justify-between items-center mt-[-10px]">
                              <p class="text-xs text-blue-600/70">
                                  * Define el tope de deuda permitido.
                              </p>
                              <div class="text-xs font-mono text-gray-400 bg-white px-2 py-1 rounded border border-blue-100">
                                  Saldo actual: {{ formatCurrency(client.current_balance) }}
                              </div>
                          </div>
                      </div>

                  </div>

                  <!-- COLUMNA DERECHA: Dirección y Notas (lg:col-span-7) -->
                  <div class="lg:col-span-7 space-y-6">
                      
                      <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                           <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4 flex items-center gap-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                              Dirección de Facturación / Entrega
                          </h4>

                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                              <!-- CP -->
                              <n-form-item path="zip_code" label="Código Postal">
                                <n-input 
                                  v-model:value="form.zip_code" 
                                  placeholder="00000" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>

                              <!-- Estado (Select) -->
                              <n-form-item path="state" label="Estado">
                                <n-select
                                  v-model:value="form.state" 
                                  :options="mexicoStates"
                                  placeholder="Selecciona estado" 
                                  filterable
                                />
                              </n-form-item>

                              <!-- Municipio -->
                              <n-form-item path="municipality" label="Municipio / Alcaldía">
                                <n-input 
                                  v-model:value="form.municipality" 
                                  placeholder="Ej. Cuauhtémoc" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                              <!-- Colonia -->
                              <div class="md:col-span-3">
                                <n-form-item path="neighborhood" label="Colonia">
                                    <n-input 
                                    v-model:value="form.neighborhood" 
                                    placeholder="Ej. Roma Norte" 
                                    class="ios-input-transition"
                                    />
                                </n-form-item>
                              </div>
                          </div>

                          <div class="grid grid-cols-12 gap-4">
                              <!-- Calle -->
                              <div class="col-span-12">
                                  <n-form-item path="street" label="Calle">
                                    <n-input 
                                      v-model:value="form.street" 
                                      placeholder="Nombre de la calle" 
                                      class="ios-input-transition"
                                    />
                                  </n-form-item>
                              </div>
                          </div>

                          <div class="grid grid-cols-2 gap-4">
                              <!-- Num Ext -->
                              <n-form-item path="exterior_number" label="No. Ext.">
                                <n-input 
                                  v-model:value="form.exterior_number" 
                                  placeholder="123" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>

                              <!-- Num Int -->
                              <n-form-item path="interior_number" label="No. Int.">
                                <n-input 
                                  v-model:value="form.interior_number" 
                                  placeholder="Apto 4B" 
                                  class="ios-input-transition"
                                />
                              </n-form-item>
                          </div>
                      </div>

                      <!-- Notas -->
                      <n-form-item path="notes" label="Notas Internas">
                        <n-input 
                          v-model:value="form.notes" 
                          type="textarea"
                          placeholder="Información adicional sobre el cliente..." 
                          class="ios-input-transition"
                          :autosize="{ minRows: 2, maxRows: 4 }"
                        />
                      </n-form-item>

                  </div>
              </div>

              <!-- Botones de Acción -->
              <div class="flex justify-end items-center gap-4 mt-8 pt-8 border-t border-gray-100">
                <button 
                  type="button"
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
                  Actualizar Cliente
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

const props = defineProps({
    client: Object,
});

const form = useForm({
  name: props.client.name,
  tax_id: props.client.tax_id,
  email: props.client.email,
  phone: props.client.phone,
  credit_limit: parseFloat(props.client.credit_limit),
  // Dirección
  street: props.client.street,
  exterior_number: props.client.exterior_number,
  interior_number: props.client.interior_number,
  neighborhood: props.client.neighborhood,
  municipality: props.client.municipality,
  state: props.client.state,
  zip_code: props.client.zip_code,
  country: props.client.country || 'México',
  notes: props.client.notes
});

const formRef = ref(null);

const rules = {
  name: { required: true, message: 'El nombre es requerido', trigger: 'blur' },
  state: { required: true, message: 'Selecciona un estado', trigger: ['blur', 'change'] },
};

// Lista de Estados de México para el Select
const mexicoStates = [
    { label: 'Aguascalientes', value: 'Aguascalientes' },
    { label: 'Baja California', value: 'Baja California' },
    { label: 'Baja California Sur', value: 'Baja California Sur' },
    { label: 'Campeche', value: 'Campeche' },
    { label: 'Chiapas', value: 'Chiapas' },
    { label: 'Chihuahua', value: 'Chihuahua' },
    { label: 'Ciudad de México', value: 'Ciudad de México' },
    { label: 'Coahuila', value: 'Coahuila' },
    { label: 'Colima', value: 'Colima' },
    { label: 'Durango', value: 'Durango' },
    { label: 'Guanajuato', value: 'Guanajuato' },
    { label: 'Guerrero', value: 'Guerrero' },
    { label: 'Hidalgo', value: 'Hidalgo' },
    { label: 'Jalisco', value: 'Jalisco' },
    { label: 'México', value: 'México' },
    { label: 'Michoacán', value: 'Michoacán' },
    { label: 'Morelos', value: 'Morelos' },
    { label: 'Nayarit', value: 'Nayarit' },
    { label: 'Nuevo León', value: 'Nuevo León' },
    { label: 'Oaxaca', value: 'Oaxaca' },
    { label: 'Puebla', value: 'Puebla' },
    { label: 'Querétaro', value: 'Querétaro' },
    { label: 'Quintana Roo', value: 'Quintana Roo' },
    { label: 'San Luis Potosí', value: 'San Luis Potosí' },
    { label: 'Sinaloa', value: 'Sinaloa' },
    { label: 'Sonora', value: 'Sonora' },
    { label: 'Tabasco', value: 'Tabasco' },
    { label: 'Tamaulipas', value: 'Tamaulipas' },
    { label: 'Tlaxcala', value: 'Tlaxcala' },
    { label: 'Veracruz', value: 'Veracruz' },
    { label: 'Yucatán', value: 'Yucatán' },
    { label: 'Zacatecas', value: 'Zacatecas' }
];

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

const submit = (e) => {
  e.preventDefault();
  formRef.value?.validate((errors) => {
    if (!errors) {
      form.put(route('clients.update', props.client.id));
    }
  });
};

const cancel = () => {
  window.history.back(); 
};

// Tema iOS / Apple-Like (Ajustado en Azul para Clientes)
const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF', // Azul iOS clásico
    primaryColorHover: '#0062cc',
    primaryColorPressed: '#004999',
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
  InputNumber: {
    peers: {
        Input: {
            color: '#F2F2F7',
            colorFocus: '#FFFFFF',
            border: '1px solid transparent',
            borderFocus: '1px solid #007AFF',
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
:deep(.ios-input-transition .n-input__input-el),
:deep(.ios-input-transition .n-input__state-border),
:deep(.ios-input-transition .n-input__placeholder) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.ios-btn-shadow) {
    box-shadow: 0 4px 14px 0 rgba(0, 122, 255, 0.39);
}
</style>