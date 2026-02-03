<script>
import { useForm } from '@inertiajs/vue3';
import { 
    NModal, 
    NCard, 
    NForm, 
    NFormItem, 
    NInput, 
    NDatePicker, 
    NSelect,
    NButton,
    NSpin,
    createDiscreteApi 
} from 'naive-ui';
import axios from 'axios';

export default {
    name: 'AppointmentModal',
    components: {
        NModal, NCard, NForm, NFormItem, NInput, NDatePicker, NSelect, NButton, NSpin
    },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        themeOverrides: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ['update:show', 'close'],
    setup() {
        const form = useForm({
            guest_name: '',
            guest_phone: '',
            date: null,     
            start_time: null, 
            service_type: null 
        });
        return { form };
    },
    data() {
        return {
            selectedTimeSlot: null,
            loadingSlots: false,
            availableSlots: [], 
            
            // Variables para el bloqueo de días
            loadingConfig: false,
            blockedWeekDays: [], // Ej: [0, 6] (Domingo, Sábado)
            blockedDates: [],    // Ej: ['2023-12-25']

            serviceOptions: [
                { label: 'Escaneo 3D de Personas', value: 'Escaneo 3D de Personas' },
                { label: 'Impresión Full Color', value: 'Impresión Full Color' },
                { label: 'Modelado Digital', value: 'Modelado Digital' },
                { label: 'Consultoría para Eventos', value: 'Consultoría para Eventos' }
            ]
        };
    },
    computed: {
        internalShow: {
            get() { return this.show; },
            set(val) { this.$emit('update:show', val); }
        }
    },
    watch: {
        'form.date'(newDate) {
            this.selectedTimeSlot = null;
            if (newDate) {
                this.generateTimeSlots(newDate);
            } else {
                this.availableSlots = [];
            }
        },
        // Cuando se abre el modal, cargamos la configuración de días cerrados
        internalShow(val) {
            if (val) {
                this.fetchDisabledDays();
            }
        }
    },
    methods: {
        async fetchDisabledDays() {
            this.loadingConfig = true;
            try {
                // Asegúrate de tener esta ruta definida en tu backend
                const response = await axios.get(route('appointments.disabled-days'));
                this.blockedWeekDays = response.data.week_days || [];
                this.blockedDates = response.data.dates || [];
            } catch (error) {
                console.error("Error cargando configuración de calendario:", error);
            } finally {
                this.loadingConfig = false;
            }
        },

        disablePreviousDate(ts) {
            const date = new Date(ts);
            const now = Date.now();

            // 1. Bloquear fechas pasadas (ayer hacia atrás)
            if (ts < now - 86400000) return true;

            // 2. Bloquear días de la semana cerrados (BusinessHours is_closed)
            // getDay(): 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
            const dayOfWeek = date.getDay();
            if (this.blockedWeekDays.includes(dayOfWeek)) {
                return true;
            }

            // 3. Bloquear fechas específicas (AvailabilityExceptions is_closed)
            // Formateamos la fecha local a YYYY-MM-DD para comparar
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const dateString = `${year}-${month}-${day}`;

            if (this.blockedDates.includes(dateString)) {
                return true;
            }

            return false;
        },

        async generateTimeSlots(dateTimestamp) {
            this.loadingSlots = true;
            this.availableSlots = [];

            try {
                const dateObj = new Date(dateTimestamp);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;

                const response = await axios.get(route('appointments.check'), {
                    params: { date: formattedDate }
                });

                this.availableSlots = response.data;
            } catch (error) {
                console.error("Error disponibilidad:", error);
                const { message } = createDiscreteApi(['message'], {
                    configProviderProps: { themeOverrides: this.themeOverrides }
                });
                message.error('Error de conexión al verificar horarios.');
            } finally {
                this.loadingSlots = false;
            }
        },
        selectSlot(slot) {
            if (slot.status === 'busy') return;
            this.selectedTimeSlot = slot.time;
        },
        submitAppointment() {
            if (!this.form.guest_name || !this.form.guest_phone || !this.form.date || !this.selectedTimeSlot || !this.form.service_type) {
                const { message } = createDiscreteApi(['message'], {
                    configProviderProps: { themeOverrides: this.themeOverrides }
                });
                message.warning('Completa todos los campos y selecciona hora.');
                return;
            }

            // 1. Crear el objeto fecha base con la zona horaria del navegador
            const dateObj = new Date(this.form.date);
            const [hours, minutes] = this.selectedTimeSlot.split(':');
            dateObj.setHours(parseInt(hours), parseInt(minutes), 0);

            // 2. CORRECCIÓN DE TIMEZONE
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const h = String(dateObj.getHours()).padStart(2, '0');
            const m = String(dateObj.getMinutes()).padStart(2, '0');
            const s = String(dateObj.getSeconds()).padStart(2, '0');
            
            const localDateString = `${year}-${month}-${day} ${h}:${m}:${s}`;

            this.form.transform((data) => ({
                guest_name: data.guest_name,
                guest_phone: data.guest_phone,
                start_time: localDateString, 
                internal_notes: `Servicio: ${data.service_type}`
            })).post(route('appointments.store'), {
                onSuccess: () => {
                    this.internalShow = false; 
                    this.form.reset();
                    this.selectedTimeSlot = null;
                    this.availableSlots = [];
                    const { message } = createDiscreteApi(['message'], {
                        configProviderProps: { themeOverrides: this.themeOverrides }
                    });
                    message.success('¡Cita agendada con éxito!');
                },
                onError: (errors) => {
                    const { message } = createDiscreteApi(['message'], {
                        configProviderProps: { themeOverrides: this.themeOverrides }
                    });
                    const firstError = Object.values(errors)[0];
                    message.error(firstError || 'Error al agendar.');
                    
                    if (this.form.date) this.generateTimeSlots(this.form.date);
                }
            });
        }
    }
}
</script>

<template>
    <n-modal v-model:show="internalShow">
        <n-card
            style="width: 800px; max-width: 95vw;"
            :bordered="false"
            size="huge"
            role="dialog"
            aria-modal="true"
            class="bg-white/95 backdrop-blur-2xl shadow-2xl relative overflow-hidden"
        >
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#4cc9f0]/10 to-transparent rounded-bl-full pointer-events-none"></div>
            
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#2f4b59] rounded-xl flex items-center justify-center text-white shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#2f4b59]">Agendar Visita</h3>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Estudio ION3D</p>
                    </div>
                </div>
            </template>
            
            <div class="mt-2">
                <p class="text-slate-600 mb-6 text-sm">
                    Selecciona una fecha para ver la disponibilidad real.
                </p>

                <n-form ref="formRef" :model="form" label-placement="top" size="large">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <n-form-item label="Nombre Completo" path="guest_name" show-require-mark>
                            <n-input v-model:value="form.guest_name" placeholder="Ej. Juan Pérez" />
                        </n-form-item>
                        <n-form-item label="Teléfono / WhatsApp" path="guest_phone" show-require-mark>
                            <n-input v-model:value="form.guest_phone" placeholder="Ej. 55 1234 5678" />
                        </n-form-item>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-4">
                        <!-- Calendario -->
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#2f4b59]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                1. Selecciona Fecha
                            </span>
                            <n-form-item path="date" :show-label="false">
                                <n-spin :show="loadingConfig" size="small">
                                    <n-date-picker 
                                        v-model:value="form.date" 
                                        panel
                                        type="date" 
                                        class="w-full shadow-sm rounded-xl overflow-hidden border border-slate-200"
                                        :is-date-disabled="disablePreviousDate"
                                        :actions="null"
                                    />
                                </n-spin>
                            </n-form-item>
                        </div>

                        <!-- Semáforo de Horarios -->
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#2f4b59]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                2. Selecciona Hora
                            </span>
                            
                            <div class="bg-white rounded-xl border border-slate-200 p-4 h-full flex flex-col min-h-[250px]">
                                <!-- Estado Vacío -->
                                <div v-if="!form.date" class="flex-1 flex flex-col items-center justify-center text-slate-400 text-center p-4">
                                    <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-xs">Elige un día para ver disponibilidad</p>
                                </div>
                                <!-- Loading -->
                                <div v-else-if="loadingSlots" class="flex-1 flex items-center justify-center">
                                    <n-spin size="medium" />
                                </div>
                                <!-- Cerrado -->
                                <div v-else-if="availableSlots.length === 0" class="flex-1 flex flex-col items-center justify-center text-red-400 text-center p-4">
                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <p class="text-xs font-bold">Sin disponibilidad</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Cerrado o agenda llena.</p>
                                </div>
                                <!-- Grid -->
                                <div v-else class="space-y-4">
                                    <div class="grid grid-cols-3 gap-2 max-h-[220px] overflow-y-auto pr-1 custom-scrollbar">
                                        <button 
                                            v-for="slot in availableSlots" 
                                            :key="slot.time"
                                            @click="selectSlot(slot)"
                                            :disabled="slot.status === 'busy'"
                                            class="px-2 py-2 rounded-lg text-sm font-medium transition-all duration-200 border text-center relative"
                                            :class="[
                                                slot.status === 'busy' 
                                                    ? 'bg-red-50 text-red-300 border-red-100 cursor-not-allowed decoration-red-300' 
                                                    : (selectedTimeSlot === slot.time 
                                                        ? 'bg-[#2f4b59] text-white border-[#2f4b59] shadow-md transform scale-105' 
                                                        : 'bg-white text-slate-600 border-slate-200 hover:border-[#2f4b59] hover:text-[#2f4b59]')
                                            ]"
                                        >
                                            {{ slot.time }}
                                        </button>
                                    </div>
                                    <!-- Leyenda -->
                                    <div class="flex items-center justify-center gap-4 text-[10px] text-slate-500 border-t border-slate-100 pt-3">
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full border border-slate-300 bg-white"></span> Libre</div>
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#2f4b59]"></span> Tu selección</div>
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span> Ocupado</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <n-form-item label="Servicio de Interés" path="service_type" show-require-mark>
                        <n-select v-model:value="form.service_type" :options="serviceOptions" placeholder="Selecciona una opción" />
                    </n-form-item>
                </n-form>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 mt-4">
                    <n-button @click="internalShow = false" quaternary type="error">Cancelar</n-button>
                    <n-button 
                        type="primary" 
                        :loading="form.processing"
                        :disabled="!selectedTimeSlot"
                        @click="submitAppointment"
                        class="shadow-lg shadow-[#2f4b59]/30"
                    >
                        Confirmar Cita
                    </n-button>
                </div>
            </template>
        </n-card>
    </n-modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>