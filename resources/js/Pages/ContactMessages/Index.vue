<template>
  <AppLayout title="Mensajes">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Título -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight flex items-center gap-3">
          Mensajes
          <span v-if="unreadCount > 0" class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full shadow-sm">
            {{ unreadCount }} nuevos
          </span>
        </h2>
        
        <!-- Buscador -->
        <div class="flex items-center gap-4 w-full md:w-auto">
          <n-config-provider :theme-overrides="searchThemeOverrides">
            <n-input 
              v-model:value="search" 
              placeholder="Buscar por nombre o contenido" 
              class="w-full md:w-[500px] ios-search"
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
          
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden">
            
            <!-- VISTA ESCRITORIO -->
            <div class="hidden md:block p-2">
              <n-data-table
                :columns="columns"
                :data="messages.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="msg in messages.data" 
                :key="msg.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98]"
                @click="openDetailModal(msg)"
              >
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <!-- Indicador de no leído -->
                    <div v-if="!msg.is_read" class="w-3 h-3 bg-blue-500 rounded-full shadow-sm shadow-blue-500/50"></div>
                    
                    <h4 :class="['text-base text-gray-900', !msg.is_read ? 'font-bold' : 'font-medium']">
                      {{ msg.name }}
                    </h4>
                  </div>
                  <span class="text-xs text-gray-400 font-medium">
                    {{ formatDate(msg.created_at) }}
                  </span>
                </div>
                
                <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                  {{ msg.message }}
                </p>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                  <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-500 font-medium">{{ msg.email }}</span>
                    <span v-if="msg.phone" class="text-xs text-gray-400 flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      {{ msg.phone }}
                    </span>
                  </div>
                  <button 
                    @click.stop="confirmDelete(msg)"
                    class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full hover:bg-red-50 hover:text-red-500 transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="messages.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="messages.last_page"
                :on-update:page="handlePageChange"
              />
            </div>
          </div>

          <!-- MODAL DETALLE DE MENSAJE -->
          <!-- Agregado style para centrar el contenido manualmente en pantalla -->
          <n-modal v-model:show="showDetail" transform-origin="center">
            <div 
              class="bg-white rounded-2xl p-0 w-full max-w-lg shadow-2xl overflow-hidden"
              style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 90%; margin: 0;"
            >
              <!-- Header Modal -->
              <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Detalle del Mensaje</h3>
                <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Body Modal -->
              <div class="p-6 space-y-4" v-if="selectedMessage">
                <!-- Info Remitente -->
                <div class="flex items-center gap-4 mb-6">
                  <div 
                    class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm"
                    :style="{ backgroundColor: stringToColor(selectedMessage.name) }"
                  >
                    {{ getInitials(selectedMessage.name) }}
                  </div>
                  <div>
                    <h4 class="text-lg font-bold text-gray-900">{{ selectedMessage.name }}</h4>
                    <p class="text-sm text-blue-600 font-medium">{{ selectedMessage.email }}</p>
                    <p v-if="selectedMessage.phone" class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      {{ selectedMessage.phone }}
                    </p>
                  </div>
                </div>

                <!-- Fecha -->
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                   Enviado el {{ formatDate(selectedMessage.created_at) }}
                </div>

                <!-- Contenido -->
                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed text-sm md:text-base border border-gray-100">
                  {{ selectedMessage.message }}
                </div>
              </div>

              <!-- Footer Modal -->
              <div class="px-7 py-4 bg-gray-50/50 flex justify-end gap-3" v-if="selectedMessage">
                <button 
                  v-if="!selectedMessage.is_read"
                  @click="markAsRead"
                  class="px-4 py-2.5 rounded-xl font-semibold bg-blue-600 text-white hover:bg-blue-50 shadow-lg shadow-blue-500/30 transition-all active:scale-95 text-sm flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Marcar como leído
                </button>
                <button 
                  v-else
                  class="px-5 py-2.5 rounded-xl font-medium text-gray-400 bg-gray-100 cursor-default text-sm flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Leído
                </button>
              </div>
            </div>
          </n-modal>

          <!-- MODAL CONFIRMACIÓN ELIMINAR -->
          <n-modal v-model:show="showDeleteModal" transform-origin="center">
            <div 
              class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl"
              style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); margin: 0;"
            >
              <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                 </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar mensaje?</h3>
              <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                Esta acción es irreversible. ¿Deseas eliminar el mensaje de <b>{{ messageToDelete?.name }}</b>?
              </p>
              <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                  Cancelar
                </button>
                <button @click="deleteMessage" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">
                  Eliminar
                </button>
              </div>
            </div>
          </n-modal>

        </n-config-provider>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch, computed, h } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { NConfigProvider, NDataTable, NInput, NTag, NPagination, NModal } from 'naive-ui';
import { debounce } from 'lodash';

const props = defineProps({
  messages: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.messages.current_page);

// State para Modales
const showDetail = ref(false);
const selectedMessage = ref(null);
const showDeleteModal = ref(false);
const messageToDelete = ref(null);

// Computed
const unreadCount = computed(() => {
  return props.messages.data.filter(m => !m.is_read).length;
});

// Helpers
const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'MSG';
const stringToColor = (str) => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) { hash = str.charCodeAt(i) + ((hash << 5) - hash); }
  const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
  return '#' + '00000'.substring(0, 6 - c.length) + c;
}

// FORMATO FECHA ACTUALIZADO
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    
    // Obtener componentes
    const day = date.getDate();
    const month = date.toLocaleDateString('es-ES', { month: 'short' });
    const year = date.getFullYear();
    const time = date.toLocaleTimeString('es-ES', { hour: '2-digit', minute:'2-digit', hour12: true });

    // Capitalizar mes (ej: jun -> Jun)
    const monthCapitalized = month.charAt(0).toUpperCase() + month.slice(1);

    return `${day}-${monthCapitalized}-${year} a las ${time}`;
}

// Iconos
const IconTrash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
]);
const IconEye = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' }),
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' })
]);

// Acciones Lógicas
const openDetailModal = (msg) => {
  selectedMessage.value = msg;
  showDetail.value = true;
};

const markAsRead = () => {
  if (!selectedMessage.value) return;
  router.put(route('contact-messages.mark-as-read', selectedMessage.value.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Actualizar localmente para feedback inmediato si Inertia no refresca todo
      selectedMessage.value.is_read = true;
      // Actualizar en la lista principal tambien
      const msgIndex = props.messages.data.findIndex(m => m.id === selectedMessage.value.id);
      if (msgIndex !== -1) props.messages.data[msgIndex].is_read = true;
    }
  });
};

const confirmDelete = (msg) => {
  messageToDelete.value = msg;
  showDeleteModal.value = true;
};

const deleteMessage = () => {
  if (messageToDelete.value) {
    router.delete(route('contact-messages.destroy', messageToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { 
        showDeleteModal.value = false; 
        messageToDelete.value = null;
        if(selectedMessage.value && selectedMessage.value.id === messageToDelete.value?.id) {
            showDetail.value = false;
        }
      }
    });
  }
};

const handlePageChange = (page) => router.get(route('contact-messages.index'), { page, search: search.value }, { preserveState: true });

watch(search, debounce((value) => {
  router.get(route('contact-messages.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// CONFIGURACIÓN TABLA
const rowProps = (row) => {
  return {
    class: `group hover:bg-gray-50/80 cursor-pointer transition-colors duration-200 ${!row.is_read ? 'bg-blue-50/30' : ''}`,
    onClick: () => openDetailModal(row)
  };
};

const columns = [
  {
    title: 'REMITENTE',
    width: 250,
    key: 'name',
    render(row) {
      return h('div', { class: 'flex items-center gap-3 py-1' }, [
        h('div', { 
            class: 'w-10 h-10 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm',
            style: { backgroundColor: stringToColor(row.name) }
        }, getInitials(row.name)),
        h('div', { class: 'flex flex-col' }, [
            h('span', { class: ['text-sm text-gray-900', !row.is_read ? 'font-bold' : 'font-medium'] }, row.name),
            h('span', { class: 'text-xs text-gray-400' }, row.email),
            // Se agrega el teléfono si existe
            row.phone ? h('span', { class: 'text-xs text-gray-400 mt-0.5' }, row.phone) : null
        ])
      ]);
    }
  },
  {
    title: 'MENSAJE',
    key: 'message',
    render(row) {
      return h('span', { class: ['text-gray-500 text-sm line-clamp-1', !row.is_read ? 'text-gray-800 font-medium' : ''] }, row.message);
    }
  },
  {
    title: 'FECHA',
    key: 'created_at',
    width: 200, // Aumentado para que quepa el nuevo formato
    render(row) {
      return h('span', { class: 'text-gray-400 text-xs font-medium' }, formatDate(row.created_at));
    }
  },
  {
    title: 'ESTADO',
    key: 'is_read',
    width: 100,
    render(row) {
      return h(NTag, { 
        type: row.is_read ? 'default' : 'info', 
        size: 'small', 
        round: true, 
        bordered: false,
        style: { fontWeight: '600' }
      }, { default: () => row.is_read ? 'Leído' : 'Nuevo' });
    }
  },
  {
    title: '',
    key: 'actions',
    width: 100,
    render(row) {
      return h('div', { class: 'flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300' }, [
        h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-gray-400 hover:text-blue-600 border border-gray-100 shadow-sm transition-all',
          onClick: (e) => { e.stopPropagation(); openDetailModal(row); }
        }, [h(IconEye)]),
        h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-gray-400 hover:text-red-500 border border-gray-100 shadow-sm transition-all',
          onClick: (e) => { e.stopPropagation(); confirmDelete(row); }
        }, [h(IconTrash)])
      ]);
    }
  }
];

// TEMAS (Iguales al Index de usuarios)
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
  common: { primaryColor: '#007AFF', borderRadius: '12px' },
  DataTable: {
    thColor: '#ffffff',
    thTextColor: '#8E8E93',
    thFontWeight: '700',
    tdColor: 'transparent', // Para que el bg-color del rowProp funcione
    tdColorHover: 'transparent',
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
  border-bottom: 1px solid #f9fafb !important;
}
:deep(.ios-table .n-data-table-tr:last-child .n-data-table-td) {
  border-bottom: none !important;
}
:deep(.ios-search .n-input__input-el) {
  font-weight: 500;
}
</style>