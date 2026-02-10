<template>
  <AppLayout title="Ventas">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <!-- Título -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
          Ventas
        </h2>
        
        <!-- Acciones: Buscador, Rango de Fechas -->
        <div class="flex flex-col md:flex-row items-center space-y-2 w-full md:w-auto">
          <!-- CORRECCIÓN: Usamos esAR y dateEsAR para español (Naive UI usa base Argentina para español) -->
          <n-config-provider :theme-overrides="searchThemeOverrides" :locale="esAR" :date-locale="dateEsAR">
            
            <!-- Selector de Rango de Fechas -->
             <n-date-picker 
                v-model:value="dateRange" 
                type="daterange" 
                clearable 
                class="w-full md:w-64 ios-search my-2"
                size="large"
                start-placeholder="Desde"
                end-placeholder="Hasta"
             />

            <!-- Buscador -->
            <n-input 
              v-model:value="search" 
              placeholder="Folio o Cliente..." 
              class="w-full md:w-64 ios-search"
              size="large"
            >
              <template #prefix>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </template>
            </n-input>
          </n-config-provider>
        </div>
      </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <n-config-provider :theme-overrides="tableThemeOverrides">
          
          <!-- CONTENEDOR PRINCIPAL -->
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden">
            
            <!-- VISTA ESCRITORIO -->
            <div class="hidden md:block p-2">
              <n-data-table
                :columns="columns"
                :data="orders.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL (Cards) -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="order in orders.data" 
                :key="order.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer"
                @click="goToShow(order)"
              >
                <!-- Encabezado Card: Folio y Estado -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-2">
                         <span class="font-mono font-bold text-gray-400 text-xs">#{{ String(order.id).padStart(5, '0') }}</span>
                         <span class="text-xs text-gray-500">{{ formatDate(order.sold_at) }}</span>
                    </div>
                    <n-tag :type="getStatusType(order.payment_status)" size="small" :bordered="false" round>
                        {{ order.payment_status }}
                    </n-tag>
                </div>

                <!-- Cliente e Info Principal -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-50 to-purple-100 flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm border border-white">
                        {{ order.client ? getInitials(order.client.name) : 'NM' }}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-bold text-gray-900 leading-tight">
                            {{ order.client ? order.client.name : 'Venta de Mostrador' }}
                        </h4>
                        <span class="text-xs text-gray-400">Atendido por: {{ order.user ? order.user.name : 'Sistema' }}</span>
                    </div>
                </div>

                <!-- Totales y Estatus de Envío -->
                <div class="flex justify-between items-end border-t border-gray-50 pt-3">
                     <div class="flex flex-col">
                         <span class="text-xs text-gray-400 mb-0.5">Envío: 
                             <span :class="order.shipping_status === 'Devuelto' ? 'text-red-500 font-bold' : 'text-gray-600'">
                                 {{ order.shipping_status }}
                             </span>
                         </span>
                         <span class="text-xs text-gray-400">Método: {{ order.payment_method }}</span>
                     </div>
                     <div class="text-right">
                         <span class="block text-xs text-gray-400">Total</span>
                         <span class="text-lg font-bold text-gray-900">{{ formatCurrency(order.total_amount) }}</span>
                     </div>
                </div>

                <!-- Footer Acciones Móvil -->
                <div class="mt-4 flex items-center justify-end gap-3 border-t border-gray-50 pt-3">
                    <button 
                      v-if="order.shipping_status !== 'Devuelto'"
                      @click.stop="confirmReturn(order)"
                      class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-sm font-semibold flex items-center gap-1 hover:bg-orange-100 transition-colors"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                      Devolución
                    </button>
                    <button @click.stop="confirmDelete(order)" class="p-2 bg-gray-50 text-gray-600 rounded-full hover:bg-red-50 hover:text-red-600">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="orders.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="orders.last_page"
                :on-update:page="handlePageChange"
              />
            </div>
          </div>
        </n-config-provider>
      </div>
    </div>

    <!-- MODAL CONFIRMACIÓN ELIMINAR -->
    <n-modal v-model:show="showDeleteModal" transform-origin="center">
      <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
        <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar Venta?</h3>
        <p class="text-gray-500 text-sm mb-6 leading-relaxed">
          Se eliminará el registro de la venta #{{ String(orderToAction?.id).padStart(5, '0') }}. 
          <br><span class="text-xs text-orange-500">Nota: Esto no afecta stock automáticamente, usa Devolución para eso.</span>
        </p>
        <div class="grid grid-cols-2 gap-3">
          <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
          <button @click="deleteOrder" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
        </div>
      </div>
    </n-modal>

    <!-- MODAL CONFIRMACIÓN DEVOLUCIÓN -->
    <n-modal v-model:show="showReturnModal" transform-origin="center">
      <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
        <div class="w-12 h-12 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">¿Procesar Devolución?</h3>
        <p class="text-gray-500 text-sm mb-6 leading-relaxed">
          Se restaurará el stock de los productos de la venta <b>#{{ String(orderToAction?.id).padStart(5, '0') }}</b> y se marcará como devuelta.
        </p>
        <div class="grid grid-cols-2 gap-3">
          <button @click="showReturnModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
          <button @click="returnOrder" class="py-2.5 rounded-xl font-semibold bg-orange-500 text-white hover:bg-orange-600 shadow-lg shadow-orange-500/30 transition-colors">Confirmar</button>
        </div>
      </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref, watch, h } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NDataTable, NInput, NTag, NPagination, NModal, NTooltip, NDatePicker 
} from 'naive-ui';
// CORRECCIÓN: Naive UI utiliza esAR como estándar para español
import { esAR, dateEsAR } from 'naive-ui'; 
import { debounce } from 'lodash';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
  orders: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const dateRange = ref(props.filters.date_range ? props.filters.date_range.map(d => Number(d)) : null);
const currentPage = ref(props.orders.current_page);

// Modales Estado
const showDeleteModal = ref(false);
const showReturnModal = ref(false);
const orderToAction = ref(null); 

// UTILIDADES
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(value);
};

const formatDate = (dateString) => {
    if(!dateString) return '-';
    return format(new Date(dateString), 'dd MMM yyyy, HH:mm', { locale: es });
};

const getInitials = (name) => {
    return name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??';
};

const getStatusType = (status) => {
    switch(status) {
        case 'Pagado': return 'success';
        case 'Pendiente': return 'warning';
        case 'Crédito': return 'info';
        case 'Parcial': return 'warning';
        default: return 'default';
    }
};

// ICONOS
const IconTrash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
]);

const IconEye = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' }),
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' })
]);

const IconReturn = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6' })
]);

// COLUMNAS TABLA
const columns = [
  {
    title: 'FOLIO',
    key: 'id',
    width: 100,
    render(row) {
        return h('span', { class: 'font-mono text-gray-500 font-bold' }, '#' + String(row.id).padStart(2, '0'));
    }
  },
  {
    title: 'CLIENTE',
    key: 'client',
    width: 250,
    render(row) {
      const clientName = row.client ? row.client.name : 'Venta de Mostrador';
      return h('div', { class: 'flex items-center gap-3 py-1' }, [
        h('div', { 
            class: 'h-9 w-9 rounded-full bg-gradient-to-br from-indigo-50 to-purple-100 flex items-center justify-center text-indigo-600 font-bold text-xs border border-white shadow-sm' 
        }, getInitials(clientName)),
        h('div', { class: 'flex flex-col' }, [
            h('span', { class: 'font-bold text-gray-900 text-sm' }, clientName),
            h('span', { class: 'text-xs text-gray-400' }, row.user ? `Vendedor: ${row.user.name}` : ''),
        ])
      ]);
    }
  },
  {
    title: 'ESTADO PAGO',
    key: 'payment_status',
    width: 130,
    render(row) {
      return h(NTag, { bordered: false, type: getStatusType(row.payment_status), size: 'small', round: true }, { default: () => row.payment_status });
    }
  },
  {
    title: 'ENVÍO / ESTADO',
    key: 'shipping_status',
    width: 130,
    render(row) {
        if (row.shipping_status === 'Devuelto') {
             return h(NTag, { bordered: false, type: 'error', size: 'small' }, { default: () => 'DEVUELTO' });
        }
        return h('span', { class: 'text-xs text-gray-500' }, row.shipping_status);
    }
  },
  {
    title: 'FECHA VENTA',
    key: 'sold_at',
    width: 160,
    render(row) {
        return h('span', { class: 'text-gray-600 text-sm' }, formatDate(row.sold_at));
    }
  },
  {
    title: 'TOTAL',
    key: 'total_amount',
    width: 140,
    align: 'right',
    render(row) {
        return h('span', { class: 'font-bold text-gray-900' }, formatCurrency(row.total_amount));
    }
  },
  {
    title: '',
    key: 'actions',
    width: 160,
    render(row) {
      const buttons = [];
      
      // Botón Ver
      buttons.push(h(NTooltip, { trigger: 'hover' }, {
            trigger: () => h('button', { 
                class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm',
                onClick: (e) => { e.stopPropagation(); goToShow(row); }
            }, [h(IconEye)]),
            default: () => 'Ver Detalles'
      }));

      // Botón Devolución (Solo si no está devuelto)
      if (row.shipping_status !== 'Devuelto') {
          buttons.push(h(NTooltip, { trigger: 'hover' }, {
                trigger: () => h('button', { 
                    class: 'w-8 h-8 rounded-full flex items-center justify-center bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-all shadow-sm',
                    onClick: (e) => { e.stopPropagation(); confirmReturn(row); }
                }, [h(IconReturn)]),
                default: () => 'Devolución (Restaurar Stock)'
          }));
      }

      // Botón Eliminar
      buttons.push(h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm',
          onClick: (e) => { e.stopPropagation(); confirmDelete(row); }
      }, [h(IconTrash)]));

      return h('div', { class: 'flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300' }, buttons);
    }
  }
];

const rowProps = (row) => {
  return {
    class: 'group hover:bg-gray-50/80 cursor-pointer transition-colors duration-200',
    onClick: () => goToShow(row)
  };
};

// ACTIONS
const goToShow = (order) => router.get(route('orders.show', order.id));

const confirmDelete = (order) => {
  orderToAction.value = order;
  showDeleteModal.value = true;
};

const deleteOrder = () => {
  if (orderToAction.value) {
    router.delete(route('orders.destroy', orderToAction.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; orderToAction.value = null; }
    });
  }
};

const confirmReturn = (order) => {
    orderToAction.value = order;
    showReturnModal.value = true;
};

const returnOrder = () => {
    if (orderToAction.value) {
        router.put(route('orders.return', orderToAction.value.id), {}, {
            preserveScroll: true,
            onSuccess: () => { showReturnModal.value = false; orderToAction.value = null; }
        });
    }
};

const handlePageChange = (page) => {
    const query = { page, search: search.value };
    if(dateRange.value) query.date_range = dateRange.value;
    router.get(route('orders.index'), query, { preserveState: true });
};

// Watchers para filtros
const applyFilters = debounce(() => {
    const query = { search: search.value };
    if(dateRange.value) query.date_range = dateRange.value;
    
    router.get(route('orders.index'), query, { 
        preserveState: true, 
        replace: true 
    });
}, 300);

watch(search, applyFilters);
watch(dateRange, applyFilters);

// THEMES
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
  },
  DatePicker: {
    itemTextColor: '#1F2937',
    itemColorActive: '#007AFF',
    borderRadius: '12px',
    iconColor: '#9CA3AF',
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

:deep(.ios-table .n-data-table-tr:last-child .n-data-table-td) {
  border-bottom: none !important;
}

:deep(.ios-search .n-input__input-el),
:deep(.ios-search .n-date-picker-input__input) {
  font-weight: 500;
}
</style>