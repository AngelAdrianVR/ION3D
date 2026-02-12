<template>
  <AppLayout title="Clientes">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Título -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
          Clientes
        </h2>
        
        <!-- Acciones: Buscador y Botón Crear -->
        <div class="flex items-center gap-4 w-full md:w-auto">
          <n-config-provider :theme-overrides="searchThemeOverrides">
            <n-input 
              v-model:value="search" 
              placeholder="Nombre, RFC o Teléfono..." 
              class="w-full md:w-72 ios-search"
              size="large"
            >
              <template #prefix>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </template>
            </n-input>
          </n-config-provider>

          <!-- PERMISO: Crear Cliente -->
          <Link v-if="can('clients.create')" :href="route('clients.create')">
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-blue-500/30 transition-all transform active:scale-95 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              <span>Nuevo Cliente</span>
            </button>
          </Link>
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
                :data="clients.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL (Cards) -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="client in clients.data" 
                :key="client.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer"
                @click="goToShow(client)"
              >
                <div class="flex items-start gap-4">
                    <!-- Avatar con iniciales -->
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600 font-bold text-lg shadow-sm border border-white">
                        {{ getInitials(client.name) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h4 class="text-base font-bold text-gray-900 leading-tight truncate pr-2">{{ client.name }}</h4>
                            <span class="text-xs font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">{{ client.tax_id || 'S/RFC' }}</span>
                        </div>
                        
                        <!-- Barra de Crédito Móvil (PERMISO: clients.debt) -->
                        <div v-if="can('clients.debt')" class="mt-3 mb-1">
                             <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500">Deuda: <span class="font-bold text-gray-900">{{ formatCurrency(client.current_balance) }}</span></span>
                                <span class="text-gray-400">Límite: {{ formatCurrency(client.credit_limit) }}</span>
                             </div>
                             <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     :class="getProgressColor(client.credit_usage_percent)"
                                     :style="{ width: `${Math.min(client.credit_usage_percent, 100)}%` }">
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Acciones Móvil -->
                <div class="mt-4 flex items-center justify-end gap-3 border-t border-gray-50 pt-3">
                    <!-- PERMISO: clients.payment -->
                    <button 
                      v-if="can('clients.payment')"
                      @click.stop="openPaymentModal(client)"
                      class="px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm font-semibold flex items-center gap-1 hover:bg-green-100 transition-colors"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                      Abonar
                    </button>
                     <!-- PERMISO: clients.edit -->
                     <button v-if="can('clients.edit')" @click.stop="editClient(client)" class="p-2 bg-gray-50 text-gray-600 rounded-full hover:bg-blue-50 hover:text-blue-600">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                     </button>
                     <!-- PERMISO: clients.delete -->
                     <button v-if="can('clients.delete')" @click.stop="confirmDelete(client)" class="p-2 bg-gray-50 text-gray-600 rounded-full hover:bg-red-50 hover:text-red-600">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                     </button>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="clients.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="clients.last_page"
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
        <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar cliente?</h3>
        <p class="text-gray-500 text-sm mb-6 leading-relaxed">
          Se enviará a la papelera. ¿Eliminar a <b>{{ clientToDelete?.name }}</b>?
        </p>
        <div class="grid grid-cols-2 gap-3">
          <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
          <button @click="deleteClient" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
        </div>
      </div>
    </n-modal>

    <!-- MODAL REGISTRAR PAGO/ABONO -->
    <n-modal v-model:show="showPaymentModalVar" transform-origin="center">
      <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Registrar Pago</h3>
            <button @click="showPaymentModalVar = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div v-if="clientToPay" class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Cliente:</span>
                <span class="font-bold text-gray-900">{{ clientToPay.name }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Saldo Actual:</span>
                <span class="font-bold text-red-600">{{ formatCurrency(clientToPay.current_balance) }}</span>
            </div>
        </div>

        <form @submit.prevent="submitPayment">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto a abonar</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input 
                            v-model="paymentForm.amount" 
                            type="number" step="0.01" min="0" 
                            class="w-full pl-8 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-all text-lg font-bold text-gray-900 bg-gray-50 focus:bg-white"
                            placeholder="0.00"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                    <select v-model="paymentForm.payment_method" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 py-2.5">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta Débito/Crédito</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Concepto / Notas</label>
                    <textarea 
                        v-model="paymentForm.description" 
                        rows="2"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 py-2"
                        placeholder="Ej: Abono cuenta pendiente..."
                    ></textarea>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
                <button type="button" @click="showPaymentModalVar = false" class="py-3 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    :disabled="paymentForm.processing"
                    class="py-3 rounded-xl font-semibold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-colors flex justify-center items-center"
                >
                    <span v-if="!paymentForm.processing">Confirmar Abono</span>
                    <span v-else class="animate-pulse">Procesando...</span>
                </button>
            </div>
        </form>
      </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref, watch, h } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NDataTable, NInput, NTag, NPagination, NModal, NTooltip 
} from 'naive-ui';
import { debounce } from 'lodash';

// 1. INICIALIZAR PERMISOS
const page = usePage();
// Helper para verificar permisos
const can = (permission) => page.props.auth.permissions.includes(permission);

const props = defineProps({
  clients: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.clients.current_page);

// CORRECCIÓN BUG PAGINACIÓN
// Si Inertia actualiza props.clients (al cambiar página), actualizamos currentPage visualmente
watch(() => props.clients.current_page, (val) => {
    currentPage.value = val;
});

// Modales Estado
const showDeleteModal = ref(false);
const clientToDelete = ref(null);

const showPaymentModalVar = ref(false);
const clientToPay = ref(null);
const paymentForm = useForm({
    amount: '',
    type: 'Abono',
    payment_method: 'Efectivo',
    description: ''
});

// UTILIDADES
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(value);
};

const getInitials = (name) => {
    return name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??';
};

const getProgressColor = (percent) => {
    if(percent >= 90) return 'bg-red-500';
    if(percent >= 75) return 'bg-orange-500';
    if(percent >= 50) return 'bg-yellow-400';
    return 'bg-green-500';
};

// SVG ICONOS
const IconEdit = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })
]);

const IconTrash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
]);

const IconCash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
]);

// CREAR COLUMNAS DINÁMICAMENTE (Según Permisos)
const createColumns = () => {
    const list = [
        {
            title: 'CLIENTE',
            key: 'name',
            width: 280,
            render(row) {
            return h('div', { class: 'flex items-center gap-3 py-1' }, [
                h('div', { 
                    class: 'h-10 w-10 rounded-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600 font-bold text-xs border border-white shadow-sm' 
                }, getInitials(row.name)),
                h('div', { class: 'flex flex-col' }, [
                    h('span', { class: 'font-bold text-gray-900 text-sm' }, row.name),
                    h('span', { class: 'text-xs text-gray-400 font-mono' }, row.email || row.phone || 'Sin contacto'),
                ])
            ]);
            }
        },
        {
            title: 'RFC / ID',
            key: 'tax_id',
            width: 140,
            render(row) {
            return row.tax_id 
                ? h(NTag, { bordered: false, type: 'default', size: 'small', class: 'font-mono' }, { default: () => row.tax_id }) 
                : h('span', { class: 'text-gray-300 text-xs italic' }, '---');
            }
        },
        {
            title: 'UBICACIÓN',
            key: 'location',
            width: 150,
            render(row) {
                if(!row.municipality && !row.state) return h('span', { class: 'text-gray-300 text-xs' }, 'N/A');
                return h('div', { class: 'flex flex-col text-xs text-gray-600' }, [
                    h('span', { class: 'font-semibold' }, row.municipality),
                    h('span', { class: 'text-gray-400' }, row.state),
                ]);
            }
        },
    ];

    // COLUMNA: CRÉDITO Y SALDO (Solo si tiene permiso clients.debt)
    if (can('clients.debt')) {
        list.push({
            title: 'CRÉDITO Y SALDO',
            key: 'balance',
            width: 220,
            render(row) {
                // Barra de progreso manual usando divs
                return h('div', { class: 'w-full pr-4' }, [
                    h('div', { class: 'flex justify-between text-xs mb-1' }, [
                        h('span', { class: 'text-gray-500' }, 'Deuda:'),
                        h('span', { class: `font-bold ${row.current_balance > 0 ? 'text-red-600' : 'text-green-600'}` }, formatCurrency(row.current_balance))
                    ]),
                    // Contenedor barra
                    h('div', { class: 'w-full bg-gray-100 rounded-full h-1.5 overflow-hidden' }, [
                        h('div', { 
                            class: `h-full rounded-full ${getProgressColor(row.credit_usage_percent)}`,
                            style: { width: `${Math.min(row.credit_usage_percent, 100)}%` }
                        })
                    ]),
                    h('div', { class: 'flex justify-end text-[10px] text-gray-400 mt-0.5' }, `Límite: ${formatCurrency(row.credit_limit)}`)
                ]);
            }
        });
    }

    // COLUMNA ACCIONES
    list.push({
        title: '',
        key: 'actions',
        width: 130,
        render(row) {
            const actions = [];

            // 1. Botón Pagar (clients.payment)
            if (can('clients.payment')) {
                actions.push(
                    h(NTooltip, { trigger: 'hover' }, {
                        trigger: () => h('button', { 
                            class: 'w-8 h-8 rounded-full flex items-center justify-center bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm',
                            onClick: (e) => { e.stopPropagation(); openPaymentModal(row); }
                        }, [h(IconCash)]),
                        default: () => 'Registrar Abono'
                    })
                );
            }

            // 2. Botón Editar (clients.edit)
            if (can('clients.edit')) {
                actions.push(
                    h('button', { 
                        class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm',
                        onClick: (e) => { e.stopPropagation(); editClient(row); }
                    }, [h(IconEdit)])
                );
            }

            // 3. Botón Eliminar (clients.delete)
            if (can('clients.delete')) {
                actions.push(
                    h('button', { 
                        class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm',
                        onClick: (e) => { e.stopPropagation(); confirmDelete(row); }
                    }, [h(IconTrash)])
                );
            }

            return h('div', { class: 'flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300' }, actions);
        }
    });

    return list;
};

const columns = createColumns();

const rowProps = (row) => {
  return {
    class: 'group hover:bg-gray-50/80 cursor-pointer transition-colors duration-200',
    onClick: () => goToShow(row)
  };
};

// ACTIONS
const goToShow = (client) => {
    // Si tienes un permiso para "ver detalle" podrías checarlo aquí, 
    // comúnmente 'clients.index' o 'clients.show' cubre esto.
    if(can('clients.index')) {
        router.get(route('clients.show', client.id));
    }
};

const editClient = (client) => {
    if(can('clients.edit')) router.get(route('clients.edit', client.id));
};

const confirmDelete = (client) => {
  clientToDelete.value = client;
  showDeleteModal.value = true;
};

const deleteClient = () => {
  if (clientToDelete.value) {
    router.delete(route('clients.destroy', clientToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; clientToDelete.value = null; }
    });
  }
};

const handlePageChange = (page) => router.get(route('clients.index'), { page, search: search.value }, { preserveState: true });

watch(search, debounce((value) => {
  router.get(route('clients.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// LOGICA PAGOS
const openPaymentModal = (client) => {
    clientToPay.value = client;
    paymentForm.reset();
    paymentForm.description = `Abono a cuenta de ${client.name}`;
    showPaymentModalVar.value = true;
};

const submitPayment = () => {
    paymentForm.post(route('clients.payment.store', clientToPay.value.id), {
        onSuccess: () => {
            showPaymentModalVar.value = false;
            paymentForm.reset();
        }
    });
};

// THEMES (Mismos que Products para consistencia)
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

:deep(.ios-search .n-input__input-el) {
  font-weight: 500;
}
</style>