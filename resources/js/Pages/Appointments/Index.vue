<template>
  <AppLayout title="Citas">
    <template v-slot:header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Título -->
        <h2 class="font-bold text-3xl text-gray-900 tracking-tight flex items-center gap-3">
          Agenda de Citas
          <span class="bg-[#2f4b59] text-white text-xs px-2 py-1 rounded-full shadow-sm font-normal">
            {{ appointments.total }} registros
          </span>
        </h2>
        
        <!-- Buscador -->
        <div class="flex items-center gap-4 w-full md:w-auto">
          <n-config-provider :theme-overrides="searchThemeOverrides">
            <n-input 
              v-model:value="search" 
              placeholder="Buscar por nombre, teléfono o fecha" 
              class="w-full md:w-[400px] ios-search"
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
            
            <!-- VISTA ESCRITORIO (TABLA) -->
            <div class="hidden md:block p-2">
              <n-data-table
                :columns="columns"
                :data="appointments.data"
                :bordered="false"
                :row-props="rowProps"
                :single-line="false"
                class="ios-table"
              />
            </div>

            <!-- VISTA MÓVIL (TARJETAS) -->
            <div class="md:hidden p-4 space-y-4">
              <div 
                v-for="appt in appointments.data" 
                :key="appt.id" 
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100/50 relative overflow-hidden transition-all active:scale-[0.98]"
                @click="openDetailModal(appt)"
              >
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <div :class="['w-3 h-3 rounded-full shadow-sm', getStatusColor(appt.status)]"></div>
                    <h4 class="text-base text-gray-900 font-bold">
                      {{ appt.guest_name }}
                    </h4>
                  </div>
                  <span class="text-xs text-gray-400 font-medium bg-gray-50 px-2 py-1 rounded-lg">
                    {{ formatDateShort(appt.start_time) }}
                  </span>
                </div>
                
                <p class="text-sm text-gray-500 mb-4 flex items-center gap-2">
                   <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                   {{ formatTime(appt.start_time) }} - {{ formatTime(appt.end_time) }}
                </p>

                <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                   <span class="text-xs text-gray-400 flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                      {{ appt.guest_phone }}
                   </span>
                   
                   <div class="flex gap-2">
                      <!-- BOTÓN EDITAR (Móvil) - Requiere appointments.manage -->
                      <button 
                        v-if="can('appointments.manage')"
                        @click.stop="openEditModal(appt)" 
                        class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-colors"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                      </button>
                      
                      <!-- BOTÓN ELIMINAR (Móvil) - Requiere appointments.delete -->
                      <button 
                        v-if="can('appointments.delete')"
                        @click.stop="confirmDelete(appt)" 
                        class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full hover:bg-red-50 hover:text-red-500 transition-colors"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      </button>
                   </div>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-6 border-t border-gray-100 bg-white/50 flex justify-center md:justify-end" v-if="appointments.links.length > 3">
              <n-pagination
                v-model:page="currentPage"
                :page-count="appointments.last_page"
                :on-update:page="handlePageChange"
              />
            </div>
          </div>

          <!-- ========================= -->
          <!-- 1. MODAL DETALLE (SHOW)   -->
          <!-- ========================= -->
          <n-modal v-model:show="showDetail" transform-origin="center">
            <div 
              class="bg-white rounded-2xl p-0 w-full max-w-lg shadow-2xl overflow-hidden"
              style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 90%; margin: 0;"
            >
              <!-- Header -->
              <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Detalle de la Cita</h3>
                <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
              </div>

              <!-- Body -->
              <div class="p-6 space-y-6" v-if="selectedAppt">
                
                <!-- Info Principal -->
                <div class="flex items-center gap-4">
                  <div 
                    class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md bg-gradient-to-br from-[#2f4b59] to-[#3e6070]"
                  >
                    {{ getInitials(selectedAppt.guest_name) }}
                  </div>
                  <div>
                    <h4 class="text-xl font-bold text-gray-900">{{ selectedAppt.guest_name }}</h4>
                    <p class="text-sm text-gray-500 flex items-center gap-1.5 mt-1">
                       <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                       {{ selectedAppt.guest_phone }}
                    </p>
                  </div>
                </div>

                <!-- Detalles Grid -->
                <div class="grid grid-cols-2 gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Fecha</span>
                        <p class="text-gray-800 font-semibold">{{ formatDate(selectedAppt.start_time) }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Horario</span>
                        <p class="text-gray-800 font-semibold">
                            {{ formatTime(selectedAppt.start_time) }} - {{ formatTime(selectedAppt.end_time) }}
                        </p>
                    </div>
                </div>

                <!-- Notas -->
                <div v-if="selectedAppt.internal_notes">
                    <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Notas / Servicio</span>
                    <div class="bg-yellow-50 text-yellow-800 p-3 rounded-lg text-sm mt-1 border border-yellow-100">
                        {{ selectedAppt.internal_notes }}
                    </div>
                </div>

                <!-- Acciones Rápidas de Estatus - Requiere appointments.manage -->
                <div v-if="can('appointments.manage')">
                    <span class="text-xs font-bold uppercase text-gray-400 tracking-wider block mb-2">Cambiar Estatus</span>
                    <div class="flex flex-wrap gap-2">
                        <button @click="updateStatus(selectedAppt, 'Confirmada')" :class="['px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors', selectedAppt.status === 'Confirmada' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 hover:border-blue-500']">Confirmado</button>
                        <button @click="updateStatus(selectedAppt, 'Pendiente')" :class="['px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors', selectedAppt.status === 'Pendiente' ? 'bg-gray-600 text-white border-gray-600' : 'bg-white text-gray-600 hover:border-gray-500']">Pendiente</button>
                        <button @click="updateStatus(selectedAppt, 'Completada')" :class="['px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors', selectedAppt.status === 'Completada' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 hover:border-green-500']">Completado</button>
                        <button @click="updateStatus(selectedAppt, 'Cancelada')" :class="['px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors', selectedAppt.status === 'Cancelada' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 hover:border-red-500']">Cancelado</button>
                        <button @click="updateStatus(selectedAppt, 'No Asistió')" :class="['px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors', selectedAppt.status === 'No Asistió' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-600 hover:border-orange-500']">No Asistió</button>
                    </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="px-7 py-4 bg-gray-50/50 flex justify-end gap-3" v-if="selectedAppt">
                 <!-- Botón Editar - Requiere appointments.manage -->
                 <button v-if="can('appointments.manage')" @click="openEditModal(selectedAppt)" class="text-blue-600 font-medium hover:underline text-sm mr-auto">Editar información</button>
                 <button @click="showDetail = false" class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">Cerrar</button>
              </div>
            </div>
          </n-modal>


          <!-- ========================= -->
          <!-- 2. MODAL EDICIÓN (EDIT)   -->
          <!-- ========================= -->
          <n-modal v-model:show="showEditModal" transform-origin="center">
             <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl" style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); margin: 0; width: 90%;">
                <h3 class="font-bold text-lg text-gray-900 mb-4">Editar Cita</h3>
                <form @submit.prevent="submitEdit">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Invitado</label>
                            <input v-model="editForm.guest_name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input v-model="editForm.guest_phone" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora Inicio</label>
                                <input v-model="editForm.start_time" type="datetime-local" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas Internas</label>
                            <textarea v-model="editForm.internal_notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 shadow-md">Guardar Cambios</button>
                    </div>
                </form>
             </div>
          </n-modal>


          <!-- ========================= -->
          <!-- 3. MODAL BORRAR (DESTROY) -->
          <!-- ========================= -->
          <n-modal v-model:show="showDeleteModal" transform-origin="center">
            <div 
              class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl"
              style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); margin: 0;"
            >
              <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                 <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">¿Cancelar Cita?</h3>
              <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                Vas a eliminar permanentemente la cita de <b>{{ apptToDelete?.guest_name }}</b>.
              </p>
              <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                  Volver
                </button>
                <button @click="deleteAppointment" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">
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
import { router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { NConfigProvider, NDataTable, NInput, NTag, NPagination, NModal } from 'naive-ui';
import { debounce } from 'lodash';

// 1. OBTENER PAGE PROPS (Permisos)
const page = usePage();

// 2. HELPER PARA VERIFICAR PERMISOS
// Revisa si el permiso existe en el array 'permissions' que viene de HandleInertiaRequests
const can = (permissionName) => {
    return page.props.auth?.permissions?.includes(permissionName) || false;
};

const props = defineProps({
  appointments: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const currentPage = ref(props.appointments.current_page);

// State Modales
const showDetail = ref(false);
const selectedAppt = ref(null);

const showEditModal = ref(false);
const editForm = useForm({
    id: null,
    guest_name: '',
    guest_phone: '',
    start_time: '',
    internal_notes: ''
});

const showDeleteModal = ref(false);
const apptToDelete = ref(null);

// HELPERS
const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '##';
const stringToColor = (str) => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) { hash = str.charCodeAt(i) + ((hash << 5) - hash); }
  const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
  return '#' + '00000'.substring(0, 6 - c.length) + c;
}

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' });
}
const formatDateShort = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
}
const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute:'2-digit', hour12: true });
}

// LÓGICA DE ESTATUS
const getStatusLabel = (status) => status; 
const getStatusType = (status) => {
    const map = {
        'Pendiente': 'default',     
        'Confirmada': 'info',       
        'Cancelada': 'error',       
        'Completada': 'success',    
        'No Asistió': 'warning',    
    };
    return map[status] || 'default';
}

const getStatusColor = (status) => {
    const map = {
        'Pendiente': 'bg-gray-400',
        'Confirmada': 'bg-blue-500',
        'Cancelada': 'bg-red-500',
        'Completada': 'bg-green-500',
        'No Asistió': 'bg-orange-500',
    };
    return map[status] || 'bg-gray-300';
}

// ACCIONES
const openDetailModal = (appt) => {
    selectedAppt.value = appt;
    showDetail.value = true;
};

const openEditModal = (appt) => {
    if (!can('appointments.manage')) return; // Seguridad extra

    const date = new Date(appt.start_time);
    const pad = (n) => n < 10 ? '0'+n : n;
    const localIso = date.getFullYear() + '-' + pad(date.getMonth()+1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());

    editForm.id = appt.id;
    editForm.guest_name = appt.guest_name;
    editForm.guest_phone = appt.guest_phone;
    editForm.start_time = localIso;
    editForm.internal_notes = appt.internal_notes;
    
    showDetail.value = false; 
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!can('appointments.manage')) return;

    editForm.put(route('appointments.update', editForm.id), {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
};

const updateStatus = (appt, newStatus) => {
    if (!can('appointments.manage')) return;

    router.put(route('appointments.update-status', appt.id), { status: newStatus }, {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedAppt.value && selectedAppt.value.id === appt.id) {
                selectedAppt.value.status = newStatus;
            }
        }
    });
};

const confirmDelete = (appt) => {
    if (!can('appointments.delete')) return;

    apptToDelete.value = appt;
    showDeleteModal.value = true;
};

const deleteAppointment = () => {
    if (apptToDelete.value) {
        router.delete(route('appointments.destroy', apptToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
                apptToDelete.value = null;
                if (selectedAppt.value && selectedAppt.value.id === apptToDelete.value?.id) {
                    showDetail.value = false;
                }
            }
        });
    }
};

const handlePageChange = (page) => {
    router.get(route('appointments.index'), { page, search: search.value }, { preserveState: true });
};

watch(() => props.appointments.current_page, (newPage) => {
    currentPage.value = newPage;
});

watch(search, debounce((value) => {
    router.get(route('appointments.index'), { search: value }, { preserveState: true, replace: true });
}, 300));


// CONFIGURACIÓN TABLA
const rowProps = (row) => {
  return {
    class: `group hover:bg-gray-50/80 cursor-pointer transition-colors duration-200`,
    onClick: () => openDetailModal(row)
  };
};

// Iconos
const IconPencil = () => h('svg', { class: 'w-4 h-4', fill:'none', stroke:'currentColor', viewBox:'0 0 24 24'}, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', 'stroke-width':'2', d:'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'})]);
const IconTrash = () => h('svg', { class: 'w-4 h-4', fill:'none', stroke:'currentColor', viewBox:'0 0 24 24'}, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', 'stroke-width':'2', d:'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'})]);
const IconEye = () => h('svg', { class: 'w-4 h-4', fill:'none', stroke:'currentColor', viewBox:'0 0 24 24'}, [h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', 'stroke-width':'2', d:'M15 12a3 3 0 11-6 0 3 3 0 016 0z'}), h('path', { 'stroke-linecap':'round', 'stroke-linejoin':'round', 'stroke-width':'2', d:'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'})]);

const columns = [
  {
    title: 'CLIENTE',
    key: 'guest_name',
    width: 250,
    render(row) {
      return h('div', { class: 'flex items-center gap-3 py-2' }, [
        h('div', { 
            class: 'w-10 h-10 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm',
            style: { backgroundColor: stringToColor(row.guest_name) }
        }, getInitials(row.guest_name)),
        h('div', { class: 'flex flex-col' }, [
            h('span', { class: 'text-sm font-bold text-gray-900' }, row.guest_name),
            h('span', { class: 'text-xs text-gray-400' }, row.guest_phone)
        ])
      ]);
    }
  },
  {
    title: 'FECHA Y HORA',
    key: 'start_time',
    width: 200,
    render(row) {
        return h('div', { class: 'flex flex-col' }, [
            h('span', { class: 'text-sm text-gray-700 font-medium' }, formatDate(row.start_time)),
            h('span', { class: 'text-xs text-gray-400' }, formatTime(row.start_time) + ' - ' + formatTime(row.end_time))
        ]);
    }
  },
  {
    title: 'SERVICIO / NOTAS',
    key: 'internal_notes',
    render(row) {
      return h('span', { class: 'text-gray-500 text-sm line-clamp-1 italic' }, row.internal_notes || 'Sin notas');
    }
  },
  {
    title: 'ESTATUS',
    key: 'status',
    width: 120,
    render(row) {
      return h(NTag, { 
        type: getStatusType(row.status), 
        size: 'small', 
        round: true, 
        bordered: false,
        style: { fontWeight: '600' }
      }, { default: () => getStatusLabel(row.status) });
    }
  },
  {
    title: '',
    key: 'actions',
    width: 140,
    render(row) {
      // LOGICA DE RENDERIZADO CONDICIONAL PARA BOTONES DE TABLA
      const buttons = [];

      // 1. Botón Ver (Siempre visible si pueden ver el índice)
      buttons.push(h('button', { 
          class: 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-gray-400 hover:text-blue-600 border border-gray-100 shadow-sm transition-all',
          onClick: (e) => { e.stopPropagation(); openDetailModal(row); },
          title: 'Ver Detalle'
        }, [h(IconEye)])
      );

      // 2. Botón Editar (Solo si tiene permiso appointments.manage)
      if (can('appointments.manage')) {
        buttons.push(h('button', { 
            class: 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-gray-400 hover:text-indigo-600 border border-gray-100 shadow-sm transition-all',
            onClick: (e) => { e.stopPropagation(); openEditModal(row); },
            title: 'Editar'
          }, [h(IconPencil)])
        );
      }

      // 3. Botón Eliminar (Solo si tiene permiso appointments.delete)
      if (can('appointments.delete')) {
        buttons.push(h('button', { 
            class: 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-gray-400 hover:text-red-500 border border-gray-100 shadow-sm transition-all',
            onClick: (e) => { e.stopPropagation(); confirmDelete(row); },
            title: 'Eliminar'
          }, [h(IconTrash)])
        );
      }

      return h('div', { class: 'flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300' }, buttons);
    }
  }
];

// TEMAS
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
    tdColor: 'transparent',
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