<template>
  <AppLayout :title="product.name">
    <template #header>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="transform transition-transform active:scale-95">
                    <Back :route="route('products.index')" />
                </div>
                <div>
                    <h2 class="font-bold text-2xl md:text-3xl text-gray-900 tracking-tight leading-none">
                        {{ product.name }}
                    </h2>
                    <span class="text-xs text-gray-400 font-mono tracking-wider">
                        SKU: {{ product.sku || 'N/A' }}
                    </span>
                </div>
            </div>

            <!-- Acciones Principales -->
            <div class="flex items-center gap-3">
                 <!-- Toggle Status -->
                 <button 
                    @click="toggleStatus"
                    class="px-4 py-2 rounded-full text-sm font-bold transition-all border flex items-center gap-2"
                    :class="product.is_active 
                        ? 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' 
                        : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100'"
                 >
                    <div class="w-2 h-2 rounded-full" :class="product.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                    {{ product.is_active ? 'Activo' : 'Inactivo' }}
                 </button>

                 <!-- Editar -->
                 <button 
                    @click="editProduct"
                    class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm active:scale-95"
                    title="Editar"
                 >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                 </button>

                 <!-- Eliminar -->
                 <button 
                    @click="showDeleteModal = true"
                    class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm active:scale-95"
                    title="Eliminar"
                 >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                 </button>
            </div>
        </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <n-config-provider :theme-overrides="iosThemeOverrides">
            
            <!-- SECCIÓN SUPERIOR: DETALLES PRODUCTO -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Columna 1: Imagen (1/3) -->
                <div class="md:col-span-1">
                    <div 
                        class="aspect-square rounded-3xl bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative group cursor-zoom-in"
                        @click="openImageModal"
                    >
                         <img 
                            v-if="product.image_url"
                            :src="product.image_url" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                            alt="Producto"
                        />
                        <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium opacity-75">Sin imagen</span>
                        </div>
                        
                        <!-- Overlay Hint -->
                        <div v-if="product.image_url" class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                            Ver imagen completa
                        </div>
                    </div>
                </div>

                <!-- Columna 2: Info Financiera y Stock (2/3) -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Tarjeta Principal Info -->
                    <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl p-6 h-full flex flex-col">
                        
                        <!-- Grid de Métricas -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                            
                            <!-- Precio Venta -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/50 border border-blue-100">
                                <p class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Precio Público</p>
                                <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ formatCurrency(product.sale_price) }}</p>
                            </div>

                             <!-- Stock -->
                            <div 
                                class="p-4 rounded-2xl border"
                                :class="isLowStock 
                                    ? 'bg-red-50 border-red-100' 
                                    : 'bg-emerald-50 border-emerald-100'"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs font-bold uppercase tracking-wider" :class="isLowStock ? 'text-red-400' : 'text-emerald-500'">Stock Disponible</p>
                                    <div v-if="isLowStock" class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                </div>
                                <p class="text-2xl md:text-3xl font-bold text-gray-900 flex items-baseline gap-1">
                                    {{ product.stock_quantity }}
                                    <span class="text-sm font-medium text-gray-500">un.</span>
                                </p>
                            </div>

                             <!-- Costo (Discreto) -->
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Costo Interno</p>
                                <p class="text-xl font-bold text-gray-600">{{ formatCurrency(product.cost_price) }}</p>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mt-auto">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Descripción del Producto</h4>
                            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                                {{ product.description || 'No hay descripción disponible para este producto.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN INFERIOR: HISTORIAL DE MOVIMIENTOS -->
            <div class="bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl overflow-hidden">
                
                <!-- Header Tabla -->
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Historial de Inventario</h3>
                        <p class="text-xs text-gray-500">Registro de entradas, salidas y ajustes.</p>
                    </div>

                    <!-- Filtro Fecha con Botón -->
                    <div class="w-full md:w-auto flex items-center gap-2">
                        <n-date-picker 
                            v-model:value="filterDate" 
                            type="month" 
                            clearable
                            placeholder="Filtrar por mes"
                            class="ios-input-shadow w-full md:w-48"
                        />
                        <button 
                            @click="handleDateChange"
                            class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg font-bold text-sm hover:bg-indigo-100 transition-colors border border-indigo-100"
                        >
                            Filtrar
                        </button>
                    </div>
                </div>

                <!-- Tabla Movimientos -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 bg-white">
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Tipo</th>
                                <th class="px-6 py-4 text-center">Cantidad</th>
                                <th class="px-6 py-4 text-center">Stock Resultante</th>
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-if="movements.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No hay movimientos registrados en este periodo.
                                </td>
                            </tr>
                            <tr 
                                v-for="mov in movements" 
                                :key="mov.id"
                                class="hover:bg-gray-50/80 transition-colors border-b border-gray-50 last:border-0 group"
                            >
                                <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">
                                    {{ mov.created_at_formatted }}
                                </td>
                                <td class="px-6 py-4">
                                    <n-tag :type="getMovementTagType(mov.type)" size="small" round :bordered="false" class="font-bold">
                                        {{ mov.type }}
                                    </n-tag>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="font-bold"
                                        :class="mov.quantity > 0 ? 'text-green-600' : 'text-red-500'"
                                    >
                                        {{ mov.quantity > 0 ? '+' : '' }}{{ mov.quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600 font-medium">
                                    {{ mov.stock_after }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold">
                                            {{ mov.user_name.charAt(0) }}
                                        </div>
                                        <span class="text-xs">{{ mov.user_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs max-w-xs truncate" :title="mov.description">
                                    {{ mov.description || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </n-config-provider>
      </div>
    </div>

    <!-- Modales -->
    
    <!-- Visualizador de Imagen -->
    <n-modal v-model:show="showImageModal" transform-origin="center">
        <div class="bg-transparent p-0 outline-none flex justify-center items-center" style="max-height: 90vh; max-width: 90vw;">
            <img v-if="product.image_url" :src="product.image_url" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border-4 border-white/20" alt="Vista previa">
        </div>
    </n-modal>

    <!-- Confirmación Eliminar -->
    <n-modal v-model:show="showDeleteModal" transform-origin="center">
        <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
            <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar producto?</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">
            Esta acción eliminará el producto y todo su historial de forma permanente.
            </p>
            <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="deleteProduct" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
            </div>
        </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { 
  NConfigProvider, NDatePicker, NTag, NModal
} from 'naive-ui';

const props = defineProps({
    product: Object,
    movements: Array,
    filters: Object
});

// Estado
const showImageModal = ref(false);
const showDeleteModal = ref(false);
const filterDate = ref(props.filters.date || Date.now());

// Computed
const isLowStock = computed(() => props.product.stock_quantity <= props.product.alert_threshold);

// Métodos
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(value);
};

const getMovementTagType = (type) => {
    switch (type) {
        case 'Venta': return 'success';
        case 'Compra': return 'info';
        case 'Devolucion': return 'warning';
        case 'Merma': return 'error';
        case 'Ajuste': return 'default';
        default: return 'default';
    }
};

const handleDateChange = () => {
    // Se ejecuta al hacer clic en el botón Filtrar
    router.get(route('products.show', props.product.id), {
        date: filterDate.value // Inertia/NaiveUI enviará el timestamp
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['movements', 'filters'] // Partial reload para eficiencia
    });
};

const toggleStatus = () => {
    router.put(route('products.toggle-status', props.product.id), {}, { preserveScroll: true });
};

const editProduct = () => {
    router.get(route('products.edit', props.product.id));
};

const deleteProduct = () => {
    router.delete(route('products.destroy', props.product.id));
};

const openImageModal = () => {
    if (props.product.image_url) showImageModal.value = true;
};

// Tema
const iosThemeOverrides = {
  common: {
    primaryColor: '#4F46E5',
    borderRadius: '12px',
  },
  DatePicker: {
      itemTextColorActive: '#4F46E5',
      itemBorderRadius: '8px'
  }
};
</script>

<style scoped>
.ios-input-shadow {
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border-radius: 12px;
}
/* Ocultar bordes de la tabla nativa para look clean */
table {
    border-spacing: 0;
}
</style>