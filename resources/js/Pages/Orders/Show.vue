<template>
  <AppLayout :title="`Venta #${String(order.id).padStart(3, '0')}`">
    <template #header>
      <!-- Ajuste responsive: flex-col en móvil, md:flex-row en escritorio -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <Link :href="route('orders.index')" class="p-2 bg-white rounded-full text-gray-400 hover:text-gray-600 shadow-sm transition-all hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </Link>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2">
                    Venta <span class="font-mono text-gray-400">#{{ String(order.id).padStart(3, '0') }}</span>
                </h2>
                <span class="text-sm text-gray-500">{{ formatDate(order.sold_at) }}</span>
            </div>
        </div>

        <!-- Acciones Principales -->
        <div class="flex gap-3 w-full md:w-auto justify-end">
             <button 
                v-if="order.shipping_status !== 'Devuelto'"
                @click="confirmReturn"
                class="px-5 py-2.5 bg-white text-orange-600 border border-orange-100 rounded-xl text-sm font-bold shadow-sm hover:bg-orange-50 transition-all flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                <span class="hidden sm:inline">Devolución</span>
                <span class="sm:hidden">Devolver</span>
            </button>
            <button 
                @click="confirmDelete"
                class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:bg-red-600 transition-all flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Eliminar
            </button>
        </div>
      </div>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- GRID SUPERIOR: CLIENTE Y RESUMEN -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- TARJETA CLIENTE -->
            <div class="md:col-span-2 bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Información del Cliente</h3>
                
                <div class="flex items-start gap-5 relative z-10 flex-wrap sm:flex-nowrap">
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-blue-500/20 flex-shrink-0">
                        {{ order.client ? getInitials(order.client.name) : 'PG' }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 break-words">
                            {{ order.client ? order.client.name : 'Venta a Público en General' }}
                        </h4>
                        
                        <div v-if="order.client" class="mt-2 space-y-1">
                            <p class="text-sm text-gray-500 flex items-center gap-2 break-all">
                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                {{ order.client.email || 'Sin email' }}
                            </p>
                            <p class="text-sm text-gray-500 flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                {{ order.client.phone || 'Sin teléfono' }}
                            </p>
                        </div>
                        <p v-else class="text-sm text-gray-400 mt-1 italic">
                            Venta de mostrador sin registro de cliente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TARJETA RESUMEN -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white shadow-[0_8px_30px_rgb(0,0,0,0.1)] rounded-3xl p-6 relative overflow-hidden flex flex-col justify-between">
                <div>
                     <div class="flex justify-between items-start mb-4">
                        <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Pagado</span>
                         <n-tag :type="getStatusType(order.payment_status)" size="small" :bordered="false" round>
                            {{ order.payment_status }}
                        </n-tag>
                     </div>
                     <div class="text-4xl font-bold tracking-tight mb-1">{{ formatCurrency(order.total_amount) }}</div>
                     <div class="text-sm text-gray-400">{{ order.payment_method }}</div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-700/50">
                     <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Vendedor</p>
                     <div class="flex items-center gap-2">
                         <div class="h-6 w-6 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold">
                             {{ getInitials(order.user ? order.user.name : 'SYS') }}
                         </div>
                         <span class="font-semibold text-sm">{{ order.user ? order.user.name : 'Sistema' }}</span>
                     </div>
                </div>
            </div>
        </div>

        <!-- SECCION ENVÍO (STEPPER) -->
        <n-config-provider :theme-overrides="stepperThemeOverrides">
            <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-sm rounded-3xl p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
                    <h3 class="text-lg font-bold text-gray-900">Estatus del Envío</h3>
                    
                    <div v-if="order.shipping_status === 'Devuelto'" class="flex items-center gap-2 text-red-500 bg-red-50 px-3 py-1 rounded-lg self-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-bold text-sm">ORDEN DEVUELTA</span>
                    </div>
                     <p v-else class="text-sm text-gray-400">
                        Haz clic en los pasos para actualizar el estado.
                    </p>
                </div>

                <!-- CONTENEDOR CON SCROLL HORIZONTAL PARA STEPPER -->
                <div class="overflow-x-auto pb-4">
                    <n-steps 
                        :current="currentStepIndex" 
                        :status="order.shipping_status === 'Devuelto' ? 'error' : 'process'"
                        class="w-full cursor-pointer min-w-[600px]"
                        @update:current="handleStepClick"
                    >
                        <n-step
                            v-for="(status, index) in shippingStatuses"
                            :key="status"
                            :title="formatStatus(status)"
                            :description="getStepDescription(status)"
                            :disabled="order.shipping_status === 'Devuelto'"
                        />
                    </n-steps>
                </div>
                
                <!-- Dirección de Envío si existe -->
                 <div v-if="order.shipping_address" class="mt-8 bg-gray-50 rounded-2xl p-4 border border-gray-100 flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <div class="text-sm text-gray-600 w-full overflow-hidden">
                        <span class="block font-bold text-gray-900 mb-1">Dirección de Entrega:</span>
                        <p class="break-words">{{ formatAddress(order.shipping_address) }}</p>
                    </div>
                 </div>
            </div>
        </n-config-provider>

        <!-- LISTA DE PRODUCTOS -->
        <n-config-provider :theme-overrides="tableThemeOverrides">
             <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-sm rounded-3xl overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Productos</h3>
                </div>
                
                <!-- AGREGADO: scroll-x para pantallas pequeñas -->
                <n-data-table
                    :columns="columns"
                    :data="order.items"
                    :bordered="false"
                    :single-line="false"
                    class="ios-table"
                    :scroll-x="800" 
                />
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
          Esta acción es permanente. <br>¿Estás seguro de eliminar este registro?
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
          Se restaurará el stock de los productos y se marcará la orden como <b>Devuelta</b>.
        </p>
        <div class="grid grid-cols-2 gap-3">
          <button @click="showReturnModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
          <button @click="returnOrder" class="py-2.5 rounded-xl font-semibold bg-orange-500 text-white hover:bg-orange-600 shadow-lg shadow-orange-500/30 transition-colors">Confirmar</button>
        </div>
      </div>
    </n-modal>

    <!-- MODAL IMAGEN GRANDE -->
    <n-modal v-model:show="showImageModal">
        <div class="bg-transparent p-4 max-w-3xl flex justify-center items-center outline-none">
             <img :src="selectedImageUrl" class="max-h-[80vh] max-w-full rounded-2xl shadow-2xl border-4 border-white/20 backdrop-blur-sm" />
        </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NDataTable, NTag, NSteps, NStep, NModal, NImage
} from 'naive-ui';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
  order: Object,
});

const showDeleteModal = ref(false);
const showReturnModal = ref(false);
const showImageModal = ref(false);
const selectedImageUrl = ref('');

// --- Lógica del Stepper ---
const shippingStatuses = ['Pendiente', 'En_Proceso', 'Enviado', 'Entregado'];

const currentStepIndex = computed(() => {
    if (props.order.shipping_status === 'Devuelto') return 0; // O un estado especial
    return shippingStatuses.indexOf(props.order.shipping_status) + 1;
});

const handleStepClick = (newStepIndex) => {
    // newStepIndex es base 1. Convertimos a index de array (base 0)
    const statusIndex = newStepIndex - 1;
    const newStatus = shippingStatuses[statusIndex];
    
    // Evitar hacer request si es el mismo o si está devuelto
    if (newStatus === props.order.shipping_status || props.order.shipping_status === 'Devuelto') return;

    router.put(route('orders.update-shipping', props.order.id), {
        status: newStatus
    }, {
        preserveScroll: true
    });
};

const formatStatus = (status) => status.replace('_', ' ');

const getStepDescription = (status) => {
    const map = {
        'Pendiente': 'Orden recibida',
        'En_Proceso': 'Preparando paquete',
        'Enviado': 'En camino',
        'Entregado': 'Recibido por cliente'
    };
    return map[status] || '';
};

// --- Columnas Tabla Productos ---
const columns = [
  {
    title: 'IMAGEN',
    key: 'image',
    width: 80,
    render(row) {
        // Verificar si existe media en el purchasable (Product)
        const media = row.purchasable?.media?.[0];
        const imageUrl = media ? media.original_url : null;
        
        if (!imageUrl) {
            return h('div', { class: 'h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300 text-xs' }, 'N/A');
        }

        return h('img', { 
            src: imageUrl, 
            class: 'h-12 w-12 object-cover rounded-lg border border-gray-200 cursor-zoom-in hover:scale-105 transition-transform',
            onClick: () => { selectedImageUrl.value = imageUrl; showImageModal.value = true; }
        });
    }
  },
  {
    title: 'PRODUCTO',
    key: 'name',
    render(row) {
        // Si es producto, link al detalle. Si es servicio (ServicePackage), texto simple o link si existiera ruta
        const isProduct = row.purchasable_type === 'App\Models\Product';
        const name = row.purchasable?.name || row.purchasable?.title || 'Ítem Desconocido';
        
        if (isProduct) {
             return h(Link, { 
                 href: route('products.show', row.purchasable_id),
                 class: 'font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-2' 
             }, { default: () => name });
        }
        
        return h('span', { class: 'font-bold text-gray-700 line-clamp-2' }, name);
    }
  },
  {
    title: 'CANT.',
    key: 'quantity',
    width: 80,
    align: 'center',
    render(row) {
        return h('div', { class: 'font-mono bg-gray-50 rounded-lg py-1 px-2 inline-block text-gray-700' }, row.quantity);
    }
  },
  {
    title: 'P. UNITARIO',
    key: 'unit_price',
    width: 120,
    align: 'right',
    render(row) {
        return h('span', { class: 'text-gray-600' }, formatCurrency(row.unit_price));
    }
  },
  {
    title: 'TOTAL',
    key: 'total',
    width: 120,
    align: 'right',
    render(row) {
        return h('span', { class: 'font-bold text-gray-900' }, formatCurrency(row.unit_price * row.quantity));
    }
  }
];

// --- Utilidades ---
const formatCurrency = (value) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
const formatDate = (dateString) => format(new Date(dateString), 'dd MMMM yyyy, HH:mm', { locale: es });
const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??';

const formatAddress = (addr) => {
    if (!addr) return 'Sin dirección registrada';
    
    let addressObj = addr;

    // Intento de parseo si viene como string JSON
    if (typeof addr === 'string') {
        try {
            addressObj = JSON.parse(addr);
        } catch (e) {
            // Si falla el parseo, se asume que es una dirección de texto plano
            return addr;
        }
    }

    // Lógica prioritaria: Si tiene full_text, usarlo
    if (addressObj.full_text) {
        return addressObj.full_text;
    }

    // Fallback: Armar dirección si es objeto estructurado
    if (typeof addressObj === 'object') {
        const parts = [
            addressObj.street,
            addressObj.exterior_number,
            addressObj.neighborhood,
            addressObj.municipality,
            addressObj.state
        ].filter(Boolean); // Filtra nulos/undefined/vacíos

        if (parts.length > 0) return parts.join(', ');
    }

    return 'Formato desconocido';
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

// --- Acciones ---
const confirmDelete = () => { showDeleteModal.value = true; };
const confirmReturn = () => { showReturnModal.value = true; };

const deleteOrder = () => {
    router.delete(route('orders.destroy', props.order.id));
};

const returnOrder = () => {
    router.put(route('orders.return', props.order.id), {}, {
        onSuccess: () => showReturnModal.value = false
    });
};

// --- Temas ---
const tableThemeOverrides = {
  common: { primaryColor: '#007AFF', borderRadius: '12px' },
  DataTable: {
    thColor: '#f9fafb', thTextColor: '#6b7280', thFontWeight: '700',
    tdColor: '#ffffff', tdColorHover: '#F9FAFB', borderColor: '#f3f4f6',
    tdPaddingMedium: '16px 24px'
  }
};

const stepperThemeOverrides = {
    common: { primaryColor: '#007AFF' },
    Steps: {
        stepHeaderFontSizeMedium: '14px',
        stepContentFontSizeMedium: '12px'
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
:deep(.n-step-content-header__title) {
    font-weight: 700;
}
</style>