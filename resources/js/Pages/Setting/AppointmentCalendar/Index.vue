<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    NCard, NButton, NIcon, NTag, NDataTable, NModal, NForm, NFormItem, NInput, NGrid, NGi, 
    NTabs, NTabPane, NSwitch, NSpace, NDivider, createDiscreteApi, NTimePicker, NDatePicker, NPopconfirm, NAlert
} from 'naive-ui';
import { 
    CalendarOutline, TimeOutline, SaveOutline, AddOutline, TrashOutline, 
    AlertCircleOutline, CalendarNumberOutline 
} from '@vicons/ionicons5';
import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
    businessHours: Array,
    exceptions: Array,
});

const { notification } = createDiscreteApi(['notification']);

// --- PESTAÑA 1: HORARIOS SEMANALES ---
// Transformar datos para manipulación local
const daysMap = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

const formHours = useForm({
    hours: props.businessHours.map(h => ({
        id: h.id,
        day_of_week: h.day_of_week,
        day_name: daysMap[h.day_of_week],
        is_closed: !!h.is_closed,
        open_time: h.open_time, // String '09:00:00'
        close_time: h.close_time // String '18:00:00'
    }))
});

const submitHours = () => {
    formHours.put(route('business-hours.update'), {
        onSuccess: () => notification.success({ content: 'Horarios actualizados correctamente', duration: 3000 }),
        onError: () => notification.error({ content: 'Error al guardar horarios', duration: 3000 })
    });
};

// --- PESTAÑA 2: EXCEPCIONES (FERIADOS) ---
const showExceptionModal = ref(false);

const exceptionColumns = [
    { 
        title: 'Fecha', 
        key: 'date',
        render(row) {
            return format(parseISO(row.date), 'dd MMM yyyy', { locale: es });
        }
    },
    { title: 'Motivo', key: 'reason' },
    { 
        title: 'Estado', 
        key: 'is_closed',
        render(row) {
            if (row.is_closed) {
                return h(NTag, { type: 'error', bordered: false }, { default: () => 'Cerrado' });
            }
            return h(NTag, { type: 'info', bordered: false }, { 
                default: () => `Abierto (${row.open_time?.substring(0,5)} - ${row.close_time?.substring(0,5)})` 
            });
        }
    },
    {
        title: 'Acciones',
        key: 'actions',
        render(row) {
            return h(NPopconfirm, {
                onPositiveClick: () => deleteException(row)
            }, {
                trigger: () => h(NButton, { size: 'small', quaternary: true, circle: true, type: 'error' }, { icon: () => h(NIcon, null, { default: () => h(TrashOutline) }) }),
                default: () => '¿Eliminar esta excepción?'
            });
        }
    }
];

const formException = useForm({
    date: null, // Timestamp o string dependiendo del datepicker
    reason: '',
    is_closed: true,
    open_time: null,
    close_time: null
});

const openCreateException = () => {
    formException.reset();
    showExceptionModal.value = true;
};

const submitException = () => {
    // Convertir timestamp de NaiveUI a formato YYYY-MM-DD
    if (formException.date) {
        formException.date = format(new Date(formException.date), 'yyyy-MM-dd');
    }
    
    // Formatear horas si están seteadas
    if (formException.open_time && !formException.is_closed) {
       // NaiveUI date picker da timestamps
       // Aquí asumimos string si viene del backend o timestamp si es nuevo
       // Simple check: si es numero (timestamp), formatear
    }

    formException.post(route('availability-exceptions.store'), {
        onSuccess: () => {
            showExceptionModal.value = false;
            notification.success({ content: 'Día especial agregado', duration: 3000 });
            formException.reset();
        },
        onError: () => notification.error({ content: 'Verifica los datos', duration: 3000 })
    });
};

const deleteException = (row) => {
    router.delete(route('availability-exceptions.destroy', row.id), {
        onSuccess: () => notification.success({ content: 'Excepción eliminada' })
    });
};

import { h } from 'vue'; 

</script>

<template>
    <AppLayout title="Configuración de Agenda">
        <template #header>
            <div class="lg:flex-col items-center justify-between w-11/12 mx-auto">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                        <n-icon class="text-indigo-600"><CalendarOutline /></n-icon>
                        <p>Configuración de Agenda</p>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Gestiona tus horarios de apertura y días no laborales.</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <n-card :bordered="false" class="shadow-sm rounded-2xl">
                    <n-tabs type="line" animated>
                        
                        <!-- PESTAÑA 1: HORARIO SEMANAL -->
                        <n-tab-pane name="weekly" tab="Horario Habitual">
                            <template #tab>
                                <div class="flex items-center gap-2">
                                    <n-icon><TimeOutline /></n-icon> Horario Semanal
                                </div>
                            </template>

                            <div class="p-4">
                                <n-alert type="info" class="mb-6" title="Información">
                                    Define los horarios estándar de la semana. Desactiva el interruptor si el negocio está cerrado ese día (ej. Domingo).
                                </n-alert>

                                <div class="space-y-4">
                                    <!-- Iterar días -->
                                    <div 
                                        v-for="(day, index) in formHours.hours" 
                                        :key="day.id"
                                        class="flex flex-col md:flex-row md:items-center justify-between p-4 rounded-lg border transition-colors"
                                        :class="day.is_closed ? 'bg-gray-50 border-gray-100' : 'bg-white border-indigo-100 shadow-sm'"
                                    >
                                        <!-- Nombre Día y Switch -->
                                        <div class="flex items-center gap-4 w-40">
                                            <n-switch v-model:value="day.is_closed" :unchecked-value="false" :checked-value="true">
                                                <template #checked>Cerrado</template>
                                                <template #unchecked>Abierto</template>
                                            </n-switch>
                                            <span class="font-bold text-gray-700" :class="{'text-gray-400': day.is_closed}">{{ day.day_name }}</span>
                                        </div>

                                        <!-- Selectores de Hora -->
                                        <div class="flex items-center gap-4 mt-3 md:mt-0" v-if="!day.is_closed">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500 uppercase">Abre:</span>
                                                <n-time-picker 
                                                    v-model:formatted-value="day.open_time" 
                                                    value-format="HH:mm:ss" 
                                                    format="HH:mm" 
                                                    placeholder="Apertura" 
                                                    class="w-32"
                                                />
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500 uppercase">Cierra:</span>
                                                <n-time-picker 
                                                    v-model:formatted-value="day.close_time" 
                                                    value-format="HH:mm:ss" 
                                                    format="HH:mm" 
                                                    placeholder="Cierre" 
                                                    class="w-32"
                                                />
                                            </div>
                                        </div>
                                        <div v-else class="flex-1 text-center md:text-left md:pl-10 text-gray-400 text-sm italic mt-2 md:mt-0">
                                            No se aceptarán citas este día.
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end border-t pt-4">
                                    <n-button type="primary" size="large" @click="submitHours" :loading="formHours.processing">
                                        <template #icon><n-icon><SaveOutline /></n-icon></template>
                                        Guardar Horarios
                                    </n-button>
                                </div>
                            </div>
                        </n-tab-pane>

                        <!-- PESTAÑA 2: EXCEPCIONES -->
                        <n-tab-pane name="exceptions" tab="Días Feriados y Excepciones">
                            <template #tab>
                                <div class="flex items-center gap-2">
                                    <n-icon><CalendarNumberOutline /></n-icon> Días Especiales
                                </div>
                            </template>

                            <div class="p-4">
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800">Calendario de Excepciones</h3>
                                        <p class="text-sm text-gray-500">Agrega días festivos o días con horario especial.</p>
                                    </div>
                                    <n-button type="primary" secondary @click="openCreateException">
                                        <template #icon><n-icon><AddOutline /></n-icon></template>
                                        Agregar Día
                                    </n-button>
                                </div>

                                <n-data-table
                                    :columns="exceptionColumns"
                                    :data="exceptions"
                                    :bordered="false"
                                    class="mb-4"
                                />
                                
                                <div v-if="exceptions.length === 0" class="text-center py-10 text-gray-400">
                                    No hay días especiales configurados.
                                </div>
                            </div>
                        </n-tab-pane>

                    </n-tabs>
                </n-card>

                <!-- MODAL: CREAR EXCEPCIÓN -->
                <n-modal v-model:show="showExceptionModal" preset="card" title="Agregar Día Especial" class="w-full max-w-lg">
                    <n-form class="mt-4">
                        <n-form-item label="Selecciona la fecha">
                            <n-date-picker v-model:value="formException.date" type="date" class="w-full" />
                        </n-form-item>
                        
                        <n-form-item label="Motivo (Ej. Navidad, Inventario)">
                            <n-input v-model:value="formException.reason" placeholder="Describe la razón" />
                        </n-form-item>

                        <n-form-item label="¿El negocio estará cerrado?">
                            <n-switch v-model:value="formException.is_closed">
                                <template #checked>Sí, Cerrado todo el día</template>
                                <template #unchecked>No, Horario Especial</template>
                            </n-switch>
                        </n-form-item>

                        <n-collapse-transition :show="!formException.is_closed">
                            <div class="bg-gray-50 p-4 rounded-lg mb-4 grid grid-cols-2 gap-4">
                                <n-form-item label="Hora Apertura" :show-label="true">
                                    <n-time-picker v-model:formatted-value="formException.open_time" value-format="HH:mm" format="HH:mm" />
                                </n-form-item>
                                <n-form-item label="Hora Cierre" :show-label="true">
                                    <n-time-picker v-model:formatted-value="formException.close_time" value-format="HH:mm" format="HH:mm" />
                                </n-form-item>
                            </div>
                        </n-collapse-transition>
                    </n-form>

                    <template #footer>
                        <div class="flex justify-end gap-2">
                            <n-button @click="showExceptionModal = false">Cancelar</n-button>
                            <n-button type="primary" @click="submitException" :loading="formException.processing">Guardar</n-button>
                        </div>
                    </template>
                </n-modal>

            </div>
        </div>
    </AppLayout>
</template>