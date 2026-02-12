<template>
  <AppLayout title="Productos">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Título grande y limpio estilo iOS -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
          Productos
        </h2>
        
        <!-- Acciones: Buscador redondeado y Botón de acción -->
        <div class="flex items-center gap-4 w-full md:w-auto">
          <n-config-provider :theme-overrides="searchThemeOverrides">
            <n-input 
              v-model:value="search" 
              placeholder="Buscar producto o SKU..." 
              class="w-full md:w-72 ios-search"
              size="large"
            >
              <template #prefix>
                <!-- Icono Search SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </template>
            </n-input>
          </n-config-provider>

          <Link :href="route('products.create')">
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-blue-500/30 transition-all transform active:scale-95 flex items-center gap-2">
              <!-- Icono Plus SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Crear</span>
            </button>
          </Link>
        </div>
      </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <n-config-provider :theme-overrides="tableThemeOverrides">
          
          <!-- CONTENEDOR PRINCIPAL CON SOMBRA SUAVE (GLASSMORPHISM LIGERO) -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden">
            
            <!-- VISTA ESCRITORIO: Tabla estilo iPadOS -->
            <div class="hidden md:block p-2">
              <n-data-table
                :columns="columns"
                :data="products.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL: Tarjetas estilo Widget -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="product in products.data" 
                :key="product.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer"
                @click="goToShow(product)"
              >
                <!-- Header de la Tarjeta -->
                <div class="flex items-start gap-4">
                  <!-- Imagen Clickeable (abre modal, no navega al show) -->
                  <div 
                    class="relative h-20 w-20 rounded-xl overflow-hidden shadow-sm flex-shrink-0 bg-gray-100 cursor-zoom-in"
                    @click.stop="openImageModal(product.image_url)"
                  >
                    <img 
                        v-if="product.image_url"
                        :src="product.image_url" 
                        class="h-full w-full object-cover" 
                        alt="Producto"
                    />
                    <div v-else class="h-full w-full flex items-center justify-center text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <h4 class="text-base font-bold text-gray-900 leading-tight truncate pr-2">{{ product.name }}</h4>
                        <span class="text-xs font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">{{ product.sku || 'N/A' }}</span>
                    </div>
                    <p class="text-lg font-bold text-blue-600 mt-1">{{ formatCurrency(product.sale_price) }}</p>
                    
                    <!-- Stock Móvil -->
                    <div class="flex items-center gap-2 mt-2">
                         <div class="h-2 w-2 rounded-full" :class="product.stock_quantity <= product.alert_threshold ? 'bg-red-500' : 'bg-green-500'"></div>
                         <span class="text-xs font-medium text-gray-600">
                             Stock: {{ product.stock_quantity }}
                             <span v-if="product.stock_quantity <= product.alert_threshold" class="text-red-500 ml-1">(Bajo)</span>
                         </span>
                    </div>
                  </div>
                </div>

                <!-- Footer de la tarjeta con Acciones -->
                <div class="mt-5 flex items-center justify-between border-t border-gray-50 pt-3">
                  <!-- Botón Estado (Toggle) -->
                   <button 
                    @click.stop="toggleStatus(product)"
                    class="px-3 py-1 rounded-full text-xs font-bold transition-colors border flex items-center gap-1.5"
                    :class="product.is_active 
                        ? 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' 
                        : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100'"
                   >
                     <div class="w-1.5 h-1.5 rounded-full" :class="product.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                     {{ product.is_active ? 'Activo' : 'Inactivo' }}
                   </button>
                  
                  <div class="flex gap-2">
                     <button 
                      @click.stop="editProduct(product)"
                      class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-600 rounded-full hover:bg-blue-100 hover:text-blue-600 transition-colors"
                     >
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                     </button>
                     <button 
                      @click.stop="confirmDelete(product)"
                      class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-600 rounded-full hover:bg-red-100 hover:text-red-600 transition-colors"
                     >
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                     </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Paginación estilo iOS -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="products.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="products.last_page"
                :on-update:page="handlePageChange"
              />
            </div>

          </div>

          <!-- Modal Confirmación Eliminar -->
          <n-modal v-model:show="showDeleteModal" transform-origin="center">
            <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
              <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar producto?</h3>
              <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                Esta acción no se puede deshacer. ¿Estás seguro de eliminar <b>{{ productToDelete?.name }}</b>?
              </p>
              <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="deleteProduct" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
              </div>
            </div>
          </n-modal>

          <!-- Modal Visualizador de Imagen -->
          <n-modal v-model:show="showImageModal" transform-origin="center">
            <div class="bg-transparent p-0 outline-none flex justify-center items-center" style="max-height: 90vh; max-width: 90vw;">
                <img :src="selectedImage" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border-4 border-white/20" alt="Vista previa">
            </div>
          </n-modal>

        </n-config-provider>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch, h } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NDataTable, NInput, NTag, NAvatar, 
  NPagination, NModal 
} from 'naive-ui';
import { debounce } from 'lodash';

const props = defineProps({
  products: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.products.current_page);

// Modales
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const showImageModal = ref(false);
const selectedImage = ref('');

// UTILIDADES
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(value);
};

// Componentes SVG Render Functions para usar dentro de h()
const IconEdit = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })
]);

const IconTrash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
]);

const IconEye = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-3.5 w-3.5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' }),
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' })
]);

// COLUMNAS TABLA (Escritorio)
const columns = [
  {
    title: 'PRODUCTO',
    key: 'name',
    width: 300,
    render(row) {
      return h('div', { class: 'flex items-center gap-4 py-1' }, [
        // Imagen clickeable (Stop propagation para no ir al show solo con la imagen)
        h('div', { 
            class: 'h-12 w-12 rounded-lg bg-gray-100 border border-gray-100 flex-shrink-0 cursor-zoom-in overflow-hidden relative group',
            onClick: (e) => { e.stopPropagation(); openImageModal(row.image_url); }
        }, [
            row.image_url 
                ? h('img', { src: row.image_url, class: 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-110' })
                : h('div', { class: 'h-full w-full flex items-center justify-center text-gray-300' }, h(IconEye))
        ]),
        h('div', { class: 'flex flex-col' }, [
            h('span', { class: 'font-bold text-gray-900 text-sm' }, row.name),
            h('span', { class: 'text-xs text-gray-400 font-mono' }, row.sku || '---'),
        ])
      ]);
    }
  },
  {
    title: 'PRECIO',
    key: 'sale_price',
    width: 150,
    render(row) {
      return h('span', { class: 'text-gray-900 font-bold' }, formatCurrency(row.sale_price));
    }
  },
  {
    title: 'STOCK',
    key: 'stock_quantity',
    width: 150,
    render(row) {
      const isLowStock = row.stock_quantity <= row.alert_threshold;
      
      return h(NTag, { 
        type: isLowStock ? 'error' : 'success', 
        size: 'small', 
        round: true, 
        bordered: false,
        style: { fontWeight: '600' }
      }, { 
          default: () => [
              h('span', {}, `${row.stock_quantity} un.`),
              isLowStock ? h('span', { class: 'ml-1 opacity-75' }, '(!)') : null
          ]
      });
    }
  },
  {
    title: 'ESTADO',
    key: 'is_active',
    width: 120,
    render(row) {
      // Botón Toggle Estado
      return h('button', {
        class: `px-3 py-1 rounded-full text-xs font-bold transition-all border flex items-center gap-1.5 ${row.is_active ? 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100'}`,
        onClick: (e) => { e.stopPropagation(); toggleStatus(row); }
      }, [
          h('div', { class: `w-1.5 h-1.5 rounded-full ${row.is_active ? 'bg-green-500' : 'bg-gray-400'}` }),
          row.is_active ? 'Activo' : 'Inactivo'
      ]);
    }
  },
  {
    title: '',
    key: 'actions',
    width: 100,
    render(row) {
      return h('div', { class: 'flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0' }, [
        h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm',
          onClick: (e) => { e.stopPropagation(); editProduct(row); }
        }, [h(IconEdit)]),
        h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm',
          onClick: (e) => { e.stopPropagation(); confirmDelete(row); }
        }, [h(IconTrash)])
      ]);
    }
  }
];

const rowProps = (row) => {
  return {
    class: 'group hover:bg-gray-50/80 cursor-pointer transition-colors duration-200',
    // Navegar al Show al hacer clic en la fila
    onClick: () => goToShow(row)
  };
};

// ACCIONES
const goToShow = (product) => router.get(route('products.show', product.id));

const editProduct = (product) => router.get(route('products.edit', product.id));

const toggleStatus = (product) => {
    router.put(route('products.toggle-status', product.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
};

const openImageModal = (url) => {
    if(!url) return;
    selectedImage.value = url;
    showImageModal.value = true;
};

const confirmDelete = (product) => {
  productToDelete.value = product;
  showDeleteModal.value = true;
};

const deleteProduct = () => {
  if (productToDelete.value) {
    router.delete(route('products.destroy', productToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; productToDelete.value = null; }
    });
  }
};

const handlePageChange = (page) => router.get(route('products.index'), { page, search: search.value }, { preserveState: true });

watch(search, debounce((value) => {
  router.get(route('products.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// TEMAS NAIVE UI (Copia exacta para consistencia visual)
const searchThemeOverrides = {
  Input: {
    borderRadius: '50px',
    color: '#F2F2F7',
    colorFocus: '#FFFFFF',
    border: '1px solid transparent',
    borderFocus: '1px solid #e5e7eb',
    boxShadowFocus: '0 4px 12px rgba(0,0,0,0.05)',
    placeholderColor: '#9CA3AF',
    textColor: '#1F2937',
    heightLarge: '44px',
  }
};

const tableThemeOverrides = {
  common: {
    primaryColor: '#007AFF',
    borderRadius: '12px',
  },
  DataTable: {
    thColor: '#ffffff',
    thTextColor: '#8E8E93',
    thFontWeight: '700',
    tdColor: '#ffffff',
    tdColorHover: '#F2F2F7',
    borderColor: 'transparent',
    tdPaddingMedium: '16px 24px',
    borderRadius: '0px',
  },
  Pagination: {
    itemTextColor: '#8E8E93',
    itemTextColorActive: '#007AFF',
    itemBorder: 'none',
    itemBorderActive: 'none',
    itemColorActive: 'rgba(0, 122, 255, 0.1)',
    itemBorderRadius: '50%',
  }
};
</script>

<style scoped>
/* Ajustes finos para la tipografía de la tabla */
:deep(.ios-table .n-data-table-th) {
  text-transform: uppercase;
  font-size: 0.7rem;
  letter-spacing: 0.08em;
  border-bottom: 1px solid #f3f4f6 !important;
}

:deep(.ios-table .n-data-table-td) {
  font-size: 0.9rem;
  color: #1f2937;
  border-bottom: 1px solid #f9fafb !important;
}

/* Eliminar borde del último item */
:deep(.ios-table .n-data-table-tr:last-child .n-data-table-td) {
  border-bottom: none !important;
}

:deep(.ios-search .n-input__input-el) {
  font-weight: 500;
}
</style>