<template>
  <AppLayout title="Usuarios">
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Título grande y limpio estilo iOS -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
          Usuarios
        </h2>
        
        <!-- Acciones: Buscador redondeado y Botón de acción -->
        <div class="flex items-center gap-4 w-full md:w-auto">
          <n-config-provider :theme-overrides="searchThemeOverrides">
            <n-input 
              v-model:value="search" 
              placeholder="Buscar" 
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

          <Link :href="route('users.create')">
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
                :data="users.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL: Tarjetas estilo Widget -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="user in users.data" 
                :key="user.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98]"
                @click="goToShow(user)"
              >
                <!-- Header de la Tarjeta -->
                <div class="flex items-start justify-between">
                  <div class="flex items-center gap-4">
                    <n-avatar 
                      round 
                      :size="52" 
                      :src="user.profile_photo_url"
                      fallback-src="" 
                      class="border-2 border-white shadow-sm font-bold text-lg object-cover"
                      :style="{ backgroundColor: stringToColor(user.name), color: 'white' }"
                      @error="(e) => e.target.style.display = 'none'" 
                    >
                      <!-- Fallback a iniciales si la imagen falla o no existe -->
                      {{ getInitials(user.name) }}
                    </n-avatar>
                    <div>
                      <h4 class="text-base font-bold text-gray-900 leading-tight">{{ user.name }}</h4>
                      <p class="text-sm text-gray-500 font-medium">{{ user.email }}</p>
                    </div>
                  </div>
                  <!-- Icono Dots SVG -->
                  <div class="text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                  </div>
                </div>

                <!-- Footer de la tarjeta con Tags y Acciones -->
                <div class="mt-5 flex items-center justify-between">
                  <n-tag :type="getRoleType(user.roles?.[0]?.name)" round :bordered="false" class="px-3 py-1 font-semibold text-xs">
                    {{ user.roles?.[0]?.name || 'Usuario' }}
                  </n-tag>
                  
                  <div class="flex gap-2">
                     <button 
                      @click.stop="editUser(user)"
                      class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-600 rounded-full hover:bg-blue-100 hover:text-blue-600 transition-colors"
                     >
                       <!-- Icono Edit SVG -->
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                       </svg>
                     </button>
                     <button 
                      @click.stop="confirmDelete(user)"
                      class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-600 rounded-full hover:bg-red-100 hover:text-red-600 transition-colors"
                     >
                       <!-- Icono Trash SVG -->
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                     </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Paginación estilo iOS -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="users.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="users.last_page"
                :on-update:page="handlePageChange"
              />
            </div>

          </div>

          <!-- Modal Confirmación estilo iOS Alert -->
          <n-modal
            v-model:show="showDeleteModal"
            transform-origin="center"
          >
            <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
              <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                 </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar usuario?</h3>
              <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                Esta acción no se puede deshacer. ¿Estás seguro de eliminar a <b>{{ userToDelete?.name }}</b>?
              </p>
              <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                  Cancelar
                </button>
                <button @click="deleteUser" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">
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
import { ref, watch, h } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NDataTable, NInput, NTag, NAvatar, 
  NPagination, NModal 
} from 'naive-ui';
import { debounce } from 'lodash';

const props = defineProps({
  users: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.users.current_page);
const showDeleteModal = ref(false);
const userToDelete = ref(null);

// UTILIDADES
const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'UA';

// Función para generar colores pastel aleatorios consistentes basados en el nombre
const stringToColor = (str) => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash);
  }
  const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
  return '#' + '00000'.substring(0, 6 - c.length) + c;
}

const getRoleType = (role) => {
  switch(role) {
    case 'Super admin': return 'error';     // Rojo suave
    case 'Vendedor': return 'success';      // Verde suave
    case 'Diseñador': return 'info';        // Azul suave
    default: return 'default';              // Gris
  }
};

// Componentes SVG Render Functions para usar dentro de h() en la tabla
const IconEdit = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })
]);

const IconTrash = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
]);

// COLUMNAS TABLA (Escritorio)
const columns = [
  {
    title: 'NOMBRE',
    key: 'name',
    width: 280,
    render(row) {
      return h('div', { class: 'flex items-center gap-4 py-1' }, [
        h(NAvatar, { 
          round: true, 
          size: 44, 
          src: row.profile_photo_url, // Se usa la propiedad del modelo
          // En caso de fallo de carga o nulo, NaiveUI mostrará el contenido del slot
          style: { backgroundColor: stringToColor(row.name), color: 'white', fontWeight: 'bold', objectFit: 'cover' } 
        }, { default: () => getInitials(row.name) }),
        h('div', { class: 'flex flex-col' }, [
            h('span', { class: 'font-bold text-gray-900 text-sm' }, row.name),
        ])
      ]);
    }
  },
  {
    title: 'CORREO ELECTRÓNICO',
    key: 'email',
    render(row) {
      return h('span', { class: 'text-gray-500 font-medium' }, row.email);
    }
  },
  {
    title: 'ROL',
    key: 'roles',
    width: 150,
    render(row) {
      const role = row.roles?.[0]?.name || 'Usuario';
      // Tags estilo iOS: fondo muy suave, texto del mismo color pero oscuro
      return h(NTag, { 
        type: getRoleType(role), 
        size: 'small', 
        round: true, 
        bordered: false,
        style: { fontWeight: '600' }
      }, { default: () => role });
    }
  },
  {
    title: '',
    key: 'actions',
    width: 120,
    render(row) {
      return h('div', { class: 'flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0' }, [
        h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm',
          onClick: (e) => { e.stopPropagation(); editUser(row); }
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
    onClick: () => goToShow(row)
  };
};

// ACCIONES
const goToShow = (user) => router.get(route('users.show', user.id));
const editUser = (user) => router.get(route('users.edit', user.id));
const confirmDelete = (user) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};
const deleteUser = () => {
  if (userToDelete.value) {
    router.delete(route('users.destroy', userToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; userToDelete.value = null; }
    });
  }
};
const handlePageChange = (page) => router.get(route('users.index'), { page, search: search.value }, { preserveState: true });

watch(search, debounce((value) => {
  router.get(route('users.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// TEMAS NAIVE UI PERSONALIZADOS (IOS STYLE)

const searchThemeOverrides = {
  Input: {
    borderRadius: '50px', // Pill shape
    color: '#F2F2F7', // iOS System Gray 6
    colorFocus: '#FFFFFF',
    border: '1px solid transparent',
    borderFocus: '1px solid #e5e7eb',
    boxShadowFocus: '0 4px 12px rgba(0,0,0,0.05)',
    placeholderColor: '#9CA3AF',
    textColor: '#1F2937',
    heightLarge: '44px', // Standard iOS touch height
  }
};

const tableThemeOverrides = {
  common: {
    primaryColor: '#007AFF', // iOS Blue
    borderRadius: '12px',
  },
  DataTable: {
    thColor: '#ffffff',
    thTextColor: '#8E8E93', // iOS Label Color Secondary
    thFontWeight: '700',
    tdColor: '#ffffff',
    tdColorHover: '#F2F2F7',
    borderColor: 'transparent', // Sin bordes duros
    tdPaddingMedium: '16px 24px', // Más aire
    borderRadius: '0px',
  },
  Pagination: {
    itemTextColor: '#8E8E93',
    itemTextColorActive: '#007AFF',
    itemBorder: 'none',
    itemBorderActive: 'none',
    itemColorActive: 'rgba(0, 122, 255, 0.1)',
    itemBorderRadius: '50%', // Circular pagination
  }
};
</script>

<style scoped>
/* Ajustes finos para la tipografía de la tabla */
:deep(.ios-table .n-data-table-th) {
  text-transform: uppercase;
  font-size: 0.7rem;
  letter-spacing: 0.08em;
  border-bottom: 1px solid #f3f4f6 !important; /* Borde muy sutil solo abajo del header */
}

:deep(.ios-table .n-data-table-td) {
  font-size: 0.9rem;
  color: #1f2937;
  border-bottom: 1px solid #f9fafb !important; /* Separadores ultra sutiles */
}

/* Eliminar borde del último item */
:deep(.ios-table .n-data-table-tr:last-child .n-data-table-td) {
  border-bottom: none !important;
}

/* Fix para el input de búsqueda de Naive UI para que se vea totalmente redondo y limpio */
:deep(.ios-search .n-input__input-el) {
  font-weight: 500;
}
</style>