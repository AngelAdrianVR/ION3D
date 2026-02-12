<template>
  <AppLayout :title="client.name">
    <template #header>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="transform transition-transform active:scale-95">
                    <Back :route="route('clients.index')" />
                </div>
                <div>
                    <h2 class="font-bold text-2xl md:text-3xl text-gray-900 tracking-tight leading-none">
                        {{ client.name }}
                    </h2>
                    <span class="text-xs text-gray-400 font-mono tracking-wider flex items-center gap-2 mt-1">
                        <span v-if="client.tax_id" class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ client.tax_id }}</span>
                        <span v-else class="italic">Sin RFC registrado</span>
                        <span class="text-gray-300">|</span>
                        <span :class="client.deleted_at ? 'text-red-500' : 'text-green-500 font-medium'">
                            {{ client.deleted_at ? 'Inactivo' : 'Activo' }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Acciones Principales -->
            <div class="flex items-center gap-3">
                 <!-- Botón Abonar (PERMISO: clients.payment) -->
                 <button 
                    v-if="can('clients.payment')"
                    @click="openPaymentModal"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-full text-sm font-bold shadow-lg shadow-green-500/30 transition-all transform active:scale-95 flex items-center gap-2"
                 >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="hidden md:inline">Abonar</span>
                    <span class="md:hidden">Pago</span>
                 </button>

                 <!-- Botón Nuevo Cliente (PERMISO: clients.create) -->
                 <Link v-if="can('clients.create')" :href="route('clients.create')">
                    <button 
                        class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm active:scale-95"
                        title="Nuevo Cliente"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                 </Link>

                 <!-- Editar (PERMISO: clients.edit) -->
                 <button 
                    v-if="can('clients.edit')"
                    @click="editClient"
                    class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm active:scale-95"
                    title="Editar Cliente"
                 >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                 </button>

                 <!-- Eliminar (PERMISO: clients.delete) -->
                 <button 
                    v-if="can('clients.delete')"
                    @click="showDeleteModal = true"
                    class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm active:scale-95"
                    title="Eliminar Cliente"
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
            
            <!-- SECCIÓN SUPERIOR: DATOS Y FINANZAS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Columna 1: Contacto y Dirección (1/3) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Tarjeta Info -->
                    <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl p-6 relative overflow-hidden">
                        
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600 font-bold text-2xl border border-white shadow-sm">
                                {{ getInitials(client.name) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Datos de Contacto</h3>
                                <p class="text-xs text-gray-500">Información personal</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Email</p>
                                    <p class="text-sm text-gray-700 font-medium break-all">{{ client.email || 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-500 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Teléfono</p>
                                    <p class="text-sm text-gray-700 font-medium">{{ client.phone || 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Dirección</p>
                                    <p class="text-sm text-gray-700 font-medium leading-snug">
                                        {{ formattedAddress }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Compras Totales</p>
                                <p class="font-bold text-gray-800">{{ stats.total_purchases }}</p>
                            </div>
                             <div>
                                <p class="text-xs text-gray-400 mb-1">Última Compra</p>
                                <p class="font-bold text-gray-800">{{ stats.last_purchase }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Columna 2: Situación Financiera (2/3) - PROTEGIDO CON PERMISO clients.debt -->
                <div class="lg:col-span-2" v-if="can('clients.debt')">
                    <div class="bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl p-6 h-full flex flex-col">
                        
                        <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Situación Financiera
                        </h3>

                        <!-- Tarjetas de Saldo -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            
                            <!-- Deuda Actual -->
                            <div class="p-5 rounded-2xl border flex flex-col justify-between transition-colors"
                                :class="client.current_balance > 0 ? 'bg-red-50 border-red-100' : 'bg-green-50 border-green-100'"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-xs font-bold uppercase tracking-wider" :class="client.current_balance > 0 ? 'text-red-500' : 'text-green-600'">Saldo Pendiente</p>
                                    <div v-if="client.current_balance > 0" class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(client.current_balance) }}</p>
                                <p class="text-xs text-gray-500 mt-1" v-if="client.current_balance > 0">Requiere pago</p>
                                <p class="text-xs text-green-600 mt-1" v-else>Al corriente</p>
                            </div>

                            <!-- Límite -->
                            <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex flex-col justify-between">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Límite de Crédito</p>
                                <p class="text-2xl font-bold text-gray-700">{{ formatCurrency(client.credit_limit) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Línea autorizada</p>
                            </div>

                             <!-- Disponible -->
                            <div class="p-5 rounded-2xl bg-blue-50/50 border border-blue-100 flex flex-col justify-between">
                                <p class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Crédito Disponible</p>
                                <p class="text-2xl font-bold text-blue-700">{{ formatCurrency(availableCredit) }}</p>
                                
                                <!-- Mini Progress Bar -->
                                <div class="w-full bg-blue-100 rounded-full h-1.5 mt-2 overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" :style="{ width: `${creditUsagePercent}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mt-auto bg-yellow-50/50 p-4 rounded-xl border border-yellow-100">
                             <h4 class="text-xs font-bold text-yellow-600 uppercase mb-1">Notas Internas</h4>
                             <p class="text-sm text-gray-700 italic">{{ client.notes || 'Sin notas adicionales.' }}</p>
                        </div>
                    </div>
                </div>
                 <!-- Fallback si no tiene permisos financieros -->
                <div class="lg:col-span-2 flex items-center justify-center bg-gray-50 rounded-3xl border border-dashed border-gray-200 min-h-[200px]" v-else>
                   <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <p class="text-gray-400 text-sm font-medium">Información financiera restringida</p>
                   </div>
                </div>
            </div>

            <!-- SECCIÓN INFERIOR: TABS DE HISTORIAL -->
            <div class="bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl overflow-hidden min-h-[400px]">
                
                <n-tabs type="line" size="large" animated>
                    
                    <!-- TAB 1: ESTADO DE CUENTA (PERMISO: clients.debt) -->
                    <n-tab-pane name="ledger" tab="Estado de Cuenta" v-if="can('clients.debt')">
                         <div class="p-4 overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100">
                                        <th class="px-6 py-4">Fecha</th>
                                        <th class="px-6 py-4">Descripción</th>
                                        <th class="px-6 py-4">Tipo</th>
                                        <th class="px-6 py-4 text-right">Monto</th>
                                        <th class="px-6 py-4 text-right">Saldo</th>
                                        <th class="px-6 py-4">Usuario</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr v-if="ledger.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                            No hay movimientos registrados.
                                        </td>
                                    </tr>
                                    <tr 
                                        v-for="mov in ledger" 
                                        :key="mov.id"
                                        class="hover:bg-gray-50/80 transition-colors border-b border-gray-50 last:border-0"
                                    >
                                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap font-mono text-xs">{{ mov.date }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-700">{{ mov.description }}</td>
                                        <td class="px-6 py-4">
                                            <span 
                                                class="px-2 py-1 rounded text-xs font-bold"
                                                :class="mov.type === 'Abono' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600'"
                                            >
                                                {{ mov.type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold" :class="mov.type === 'Abono' ? 'text-green-600' : 'text-red-600'">
                                            {{ mov.type === 'Abono' ? '-' : '+' }}{{ formatCurrency(mov.amount) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-800 font-bold">
                                            {{ formatCurrency(mov.balance_after) }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500">{{ mov.user_name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                    </n-tab-pane>

                    <!-- TAB 2: HISTORIAL DE VENTAS (PERMISO: orders.index) -->
                    <n-tab-pane name="sales" tab="Historial de Ventas" v-if="can('orders.index')">
                        <div class="p-4 overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100">
                                        <th class="px-6 py-4">Folio/ID</th>
                                        <th class="px-6 py-4">Fecha</th>
                                        <th class="px-6 py-4">Resumen Artículos</th>
                                        <th class="px-6 py-4 text-center">Cant. Items</th>
                                        <th class="px-6 py-4 text-right">Total</th>
                                        <th class="px-6 py-4 text-center">Estado Pago</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr v-if="orders.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                            Este cliente aún no ha realizado compras.
                                        </td>
                                    </tr>
                                    <tr 
                                        v-for="order in orders" 
                                        :key="order.id"
                                        class="hover:bg-gray-50/80 transition-colors border-b border-gray-50 last:border-0 group cursor-pointer"
                                    >
                                        <td class="px-6 py-4 font-bold text-indigo-600">#{{ order.id }}</td>
                                        <td class="px-6 py-4 text-gray-500 text-xs">{{ order.date }}</td>
                                        <td class="px-6 py-4 text-gray-700">
                                            {{ order.summary }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500">{{ order.items_count }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900">{{ formatCurrency(order.total) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span 
                                                class="px-2 py-1 rounded text-xs font-bold border"
                                                :class="{
                                                    'bg-green-50 text-green-600 border-green-100': order.status === 'Pagado',
                                                    'bg-yellow-50 text-yellow-600 border-yellow-100': order.status === 'Pendiente',
                                                    'bg-orange-50 text-orange-600 border-orange-100': order.status === 'Crédito',
                                                }"
                                            >
                                                {{ order.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </n-tab-pane>

                </n-tabs>
            </div>

        </n-config-provider>
      </div>
    </div>

    <!-- Modales -->

    <!-- Modal Pago (Abono) -->
    <n-modal v-model:show="showPaymentModalVar" transform-origin="center">
      <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Registrar Abono</h3>
            <button @click="showPaymentModalVar = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Saldo Actual:</span>
                <span class="font-bold text-red-600">{{ formatCurrency(client.current_balance) }}</span>
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
                        placeholder="Ej: Abono parcial..."
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

    <!-- Confirmación Eliminar -->
    <n-modal v-model:show="showDeleteModal" transform-origin="center">
        <div class="bg-white/90 backdrop-blur-xl w-80 rounded-2xl p-6 text-center shadow-2xl">
            <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Eliminar cliente?</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">
            Se enviará a la papelera y no podrá generar nuevas compras.
            </p>
            <div class="grid grid-cols-2 gap-3">
                <button @click="showDeleteModal = false" class="py-2.5 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="deleteClient" class="py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition-colors">Eliminar</button>
            </div>
        </div>
    </n-modal>

  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';
import { 
  NConfigProvider, NTabs, NTabPane, NModal
} from 'naive-ui';

// 1. INICIALIZAR PERMISOS
const page = usePage();
// Helper para verificar permisos
const can = (permission) => page.props.auth.permissions.includes(permission);

const props = defineProps({
    client: Object,
    ledger: Array,
    orders: Array,
    stats: Object,
});

// Estado Modales
const showDeleteModal = ref(false);
const showPaymentModalVar = ref(false);

const paymentForm = useForm({
    amount: '',
    type: 'Abono',
    payment_method: 'Efectivo',
    description: ''
});

// Computed
const availableCredit = computed(() => {
    return Math.max(0, parseFloat(props.client.credit_limit) - parseFloat(props.client.current_balance));
});

const creditUsagePercent = computed(() => {
    if (parseFloat(props.client.credit_limit) === 0) return 0;
    return (parseFloat(props.client.current_balance) / parseFloat(props.client.credit_limit)) * 100;
});

const formattedAddress = computed(() => {
    const c = props.client;
    const parts = [c.street, c.exterior_number, c.neighborhood, c.municipality, c.state].filter(Boolean);
    return parts.length > 0 ? parts.join(', ') : 'Dirección no registrada';
});

// Métodos
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(value);
};

const getInitials = (name) => {
    return name ? name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??';
};

const editClient = () => {
    if(can('clients.edit')) router.get(route('clients.edit', props.client.id));
};

const deleteClient = () => {
    if(can('clients.delete')) router.delete(route('clients.destroy', props.client.id));
};

// Lógica Pago
const openPaymentModal = () => {
    if(can('clients.payment')) {
        paymentForm.reset();
        paymentForm.description = `Abono a cuenta de ${props.client.name}`;
        showPaymentModalVar.value = true;
    }
};

const submitPayment = () => {
    paymentForm.post(route('clients.payment.store', props.client.id), {
        onSuccess: () => {
            showPaymentModalVar.value = false;
            paymentForm.reset();
        }
    });
};

// Tema
const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF',
    borderRadius: '12px',
  },
  Tabs: {
      tabTextColorActiveLine: '#007AFF',
      barColor: '#007AFF',
      tabFontWeightActive: '700'
  }
};
</script>

<style scoped>
/* Ocultar bordes de la tabla nativa para look clean */
table {
    border-spacing: 0;
}
</style>