<template>
  <AppLayout title="Punto de Venta">
    <!-- HEADER POS -->
    <template #header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Punto de Venta
                </h2>
                <span v-if="activeSession" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Caja Abierta: {{ activeSession.id }}
                    
                    <!-- Botón de Cierre de Caja -->
                    <button 
                        @click="showCloseRegisterModal = true"
                        class="ml-2 px-2 py-0.5 bg-red-500 text-white rounded text-[10px] hover:bg-red-600 transition-colors"
                        title="Realizar Corte de Caja"
                    >
                        Cerrar Turno
                    </button>
                </span>
                <span v-else class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-200">
                    Caja Cerrada
                </span>
            </div>
        </div>
        <!-- Información del Vendedor -->
        <div class=" hidden md:block">
            <p class="text-xs text-gray-400 font-bold uppercase">Atendiendo</p>
            <p class="text-sm font-bold text-gray-700">{{ user.name }}</p>
        </div>
    </template>
    <n-config-provider :theme-overrides="iosThemeOverrides">
        

        <div class="h-[calc(100vh-140px)] flex flex-col md:flex-row overflow-hidden bg-gray-50">
            
            <!-- COLUMNA IZQUIERDA: CATÁLOGO -->
            <div class="w-full md:w-7/12 lg:w-3/4 flex flex-col h-full border-r border-gray-200">
                
                <!-- Buscador y Filtros -->
                <div class="p-4 bg-white border-b border-gray-100 shadow-sm z-10">
                    <div class="flex gap-4 mb-4">
                        <div class="relative flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-medium"
                                placeholder="Buscar producto, SKU o servicio..."
                                autofocus
                            >
                        </div>
                    </div>
                    
                    <!-- Tabs Rápidas -->
                    <div class="flex gap-2 overflow-x-auto pb-1 custom-scrollbar">
                        <button 
                            @click="filterType = 'all'" 
                            class="px-4 py-1.5 rounded-full text-sm font-bold transition-all whitespace-nowrap"
                            :class="filterType === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        >Todos</button>
                        <button 
                            @click="filterType = 'product'" 
                            class="px-4 py-1.5 rounded-full text-sm font-bold transition-all whitespace-nowrap"
                            :class="filterType === 'product' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'"
                        >Productos</button>
                        <button 
                            @click="filterType = 'service'" 
                            class="px-4 py-1.5 rounded-full text-sm font-bold transition-all whitespace-nowrap"
                            :class="filterType === 'service' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-600 hover:bg-orange-100'"
                        >Servicios / Paquetes</button>
                    </div>
                </div>

                <!-- Grid de Productos -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <div v-if="filteredCatalog.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        <p>No se encontraron items.</p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div 
                            v-for="item in filteredCatalog" 
                            :key="item.id + item.type"
                            @click="addToCart(item)"
                            class="group bg-white rounded-2xl p-3 shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-300 cursor-pointer transition-all active:scale-95 flex flex-col justify-between h-full relative overflow-hidden"
                            :class="{'opacity-50 pointer-events-none': item.type === 'product' && item.stock_quantity <= 0}"
                        >
                             <!-- Badge Tipo -->
                             <div class="absolute top-2 right-2 z-10">
                                <span v-if="item.type === 'service'" class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-600 border border-orange-200 shadow-sm">Servicio</span>
                                <span v-else class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-600 border border-indigo-200 shadow-sm">Producto</span>
                             </div>

                             <!-- Imagen del Producto -->
                             <div class="w-full aspect-square rounded-xl overflow-hidden mb-3 bg-gray-100 relative">
                                <img 
                                    v-if="item.image_url" 
                                    :src="item.image_url" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                    alt="Product Image"
                                >
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                
                                <!-- Badge de Opciones (Si tiene precios múltiples) -->
                                <div v-if="item.pricing_options && item.pricing_options.length > 0" class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-[10px] text-center py-1 backdrop-blur-sm">
                                    {{ item.pricing_options.length }} Opciones Disp.
                                </div>
                             </div>

                             <div>
                                <h3 class="font-bold text-gray-800 text-sm leading-tight mb-1 line-clamp-2">{{ item.name }}</h3>
                                <p class="text-xs text-gray-400 mb-2">{{ item.sku }}</p>
                             </div>

                             <div class="mt-auto pt-2 border-t border-gray-50">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-lg text-gray-900">{{ formatCurrency(item.sale_price) }}</span>
                                    <span v-if="item.type === 'product'" class="text-[10px] px-1.5 py-0.5 bg-gray-100 rounded text-gray-500 font-mono border border-gray-200">
                                        Stock: {{ item.stock_quantity }}
                                    </span>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: CARRITO -->
            <div class="w-full md:w-5/12 lg:w-1/4 bg-white flex flex-col h-full shadow-2xl z-20">
                
                <div class="p-4 border-b border-gray-100 bg-white">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Carrito de Venta
                    </h3>
                    <p class="text-xs text-gray-400">{{ cart.length }} items agregados</p>
                </div>

                <!-- Lista de Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-if="cart.length === 0" class="text-center py-10 text-gray-400 border-2 border-dashed border-gray-100 rounded-xl mx-4">
                        <p class="text-sm">El carrito está vacío</p>
                        <p class="text-xs mt-1">Selecciona productos para comenzar</p>
                    </div>

                    <div v-for="(item, index) in cart" :key="index" class="flex gap-3 items-center p-2 rounded-xl bg-white border border-gray-100 hover:border-blue-200 shadow-sm relative group transition-all">
                        
                        <!-- Cantidad -->
                        <div class="flex flex-col items-center gap-1 shrink-0">
                            <button @click="incrementItem(index)" class="w-6 h-6 rounded bg-gray-50 text-blue-600 border border-blue-100 hover:bg-blue-50 flex items-center justify-center text-xs font-bold transition-colors">+</button>
                            <span class="font-mono font-bold text-sm">{{ item.quantity }}</span>
                            <button @click="decrementItem(index)" class="w-6 h-6 rounded bg-gray-50 text-gray-500 border border-gray-200 hover:bg-gray-100 flex items-center justify-center text-xs font-bold transition-colors">-</button>
                        </div>

                        <!-- Mini Imagen -->
                        <div class="h-12 w-12 rounded-lg bg-gray-100 shrink-0 overflow-hidden border border-gray-100">
                             <img v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover">
                             <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                             </div>
                        </div>

                        <!-- Detalles -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 leading-tight truncate">{{ item.name }}</p>
                            <!-- Mostrar variante si existe -->
                            <p v-if="item.variant_label" class="text-[10px] text-orange-600 font-bold bg-orange-50 inline-block px-1 rounded border border-orange-100 mt-0.5">
                                {{ item.variant_label }}
                            </p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">{{ formatCurrency(item.sale_price) }}</p>
                                <p class="text-sm font-bold text-blue-600">{{ formatCurrency(item.sale_price * item.quantity) }}</p>
                            </div>
                        </div>

                        <!-- Eliminar -->
                        <button @click="removeFromCart(index)" class="absolute -top-2 -right-2 bg-red-100 text-red-500 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-all hover:bg-red-200 shadow-sm transform hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Footer Totales -->
                <div class="p-4 bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgb(0,0,0,0.05)]">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500 text-sm">Subtotal</span>
                        <span class="font-bold text-gray-800">{{ formatCurrency(cartTotal) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6 pt-2 border-t border-dashed border-gray-200">
                        <span class="text-lg font-bold text-gray-900">Total a Pagar</span>
                        <span class="text-2xl font-bold text-indigo-600">{{ formatCurrency(cartTotal) }}</span>
                    </div>

                    <button 
                        @click="openCheckout"
                        :disabled="cart.length === 0 || !activeSession"
                        class="w-full py-3.5 bg-gray-900 text-white rounded-xl font-bold text-lg shadow-lg shadow-gray-900/20 hover:bg-gray-800 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                    >
                        <span>Cobrar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </button>
                </div>
            </div>

        </div>

        <!-- ================= MODALES ================= -->

        <!-- 1. MODAL APERTURA DE CAJA -->
        <n-modal v-model:show="showOpenRegisterModal" :mask-closable="false" :close-on-esc="false">
             <div class="bg-white rounded-3xl p-8 w-full max-w-md text-center shadow-2xl">
                 <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                 </div>
                 <h2 class="text-2xl font-bold text-gray-900 mb-2">Apertura de Caja</h2>
                 <p class="text-gray-500 mb-6">Debes abrir una caja para comenzar a vender.</p>

                 <div class="text-left space-y-4">
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">Seleccionar Caja</label>
                         <n-select v-model:value="openRegisterForm.cash_register_id" :options="registerOptions" placeholder="Seleccione una caja" />
                     </div>
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">Monto Inicial (Fondo)</label>
                         <n-input-number 
                            v-model:value="openRegisterForm.opening_amount" 
                            :show-button="false"
                            placeholder="0.00"
                            class="text-lg"
                         >
                            <template #prefix>$</template>
                         </n-input-number>
                     </div>
                 </div>

                 <button 
                    @click="submitOpenRegister"
                    class="w-full mt-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors"
                    :disabled="!openRegisterForm.cash_register_id"
                 >
                     Abrir Turno
                 </button>
             </div>
        </n-modal>

        <!-- 2. MODAL SELECCIÓN DE PRECIOS/OPCIONES -->
        <n-modal v-model:show="showOptionsModal">
            <div class="bg-white rounded-2xl p-6 w-96 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg text-gray-800">Seleccionar Opción</h3>
                    <button @click="showOptionsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-500 mb-4">{{ currentOptionItem?.name }}</p>

                <div class="flex flex-col gap-2">
                    <button
                        v-for="(opt, idx) in currentOptionItem?.pricing_options"
                        :key="idx"
                        @click="handleOptionSelect(opt)"
                        class="flex justify-between items-center p-3 rounded-xl border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all text-left group"
                    >
                        <span class="font-bold text-gray-700 group-hover:text-blue-700">{{ opt.label }}</span>
                        <span class="font-bold text-blue-600">{{ formatCurrency(opt.price) }}</span>
                    </button>
                </div>
            </div>
        </n-modal>

        <!-- 3. MODAL COBRO (CHECKOUT) -->
        <n-modal v-model:show="showCheckoutModal" transform-origin="center">
            <!-- CORRECCIÓN MOVIL: scroll en el contenedor principal en móvil -->
            <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl flex flex-col md:flex-row h-[90vh] md:h-auto overflow-y-auto md:overflow-hidden">
                
                <!-- Columna Izq: Datos Venta -->
                <div class="w-full md:w-1/2 p-6 md:p-8 bg-gray-50 flex flex-col md:overflow-y-auto shrink-0">
                    <h3 class="font-bold text-xl text-gray-900 mb-6">Detalles de Venta</h3>
                    
                    <!-- Selección de Cliente -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cliente</label>
                        
                        <div class="flex gap-2">
                             <div class="flex-1">
                                <n-select
                                    v-model:value="paymentForm.client_id"
                                    filterable
                                    placeholder="Buscar cliente (o dejar vacío para Público General)"
                                    :options="searchedClients"
                                    remote
                                    clearable
                                    @search="handleClientSearch"
                                    @update:value="onClientSelect"
                                />
                             </div>
                             <button @click="showQuickClientModal = true" class="px-3 bg-white border border-gray-200 rounded hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-colors" title="Nuevo Cliente Rápido">
                                 +
                             </button>
                        </div>
                        
                        <!-- Info Cliente Seleccionado -->
                        <div v-if="selectedClientData" class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-100 text-sm">
                            <p class="font-bold text-blue-800">{{ selectedClientData.name }}</p>
                            <div class="flex justify-between mt-1">
                                <span class="text-blue-600">Crédito Disp:</span>
                                <span class="font-bold">{{ formatCurrency(selectedClientData.available_credit) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración Envío -->
                    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-gray-700 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                ¿Requiere Envío?
                            </span>
                            <n-switch v-model:value="paymentForm.requires_shipping" />
                        </div>

                        <div v-if="paymentForm.requires_shipping" class="space-y-3 pt-3 border-t border-gray-100">
                            <div>
                                <label class="text-xs text-gray-500">Costo de Envío</label>
                                <n-input-number v-model:value="paymentForm.shipping_cost" size="small">
                                    <template #prefix>$</template>
                                </n-input-number>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Dirección de Entrega</label>
                                <textarea 
                                    v-model="shippingAddressString" 
                                    class="w-full text-sm p-2 rounded border-gray-200 bg-gray-50"
                                    rows="3"
                                    placeholder="Calle, Número, Colonia, CP..."
                                ></textarea>
                                <p class="text-[10px] text-gray-400 mt-1">* Se guardará como dirección de este pedido.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Notas de Venta</label>
                        <textarea v-model="paymentForm.notes" class="w-full rounded-xl border-gray-200 text-sm" rows="2" placeholder="Notas internas..."></textarea>
                    </div>
                </div>

                <!-- Columna Der: Totales y Pago -->
                <div class="w-full md:w-1/2 p-6 md:p-8 bg-white flex flex-col justify-between shrink-0">
                    <div>
                        <h3 class="font-bold text-xl text-gray-900 mb-6">Pago</h3>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal Productos</span>
                                <span>{{ formatCurrency(cartTotal) }}</span>
                            </div>
                            <div v-if="paymentForm.requires_shipping" class="flex justify-between text-gray-600">
                                <span>Costo de Envío</span>
                                <span>{{ formatCurrency(paymentForm.shipping_cost || 0) }}</span>
                            </div>
                            <div class="flex justify-between text-2xl font-bold text-gray-900 pt-4 border-t border-gray-100">
                                <span>Total</span>
                                <span>{{ formatCurrency(grandTotal) }}</span>
                            </div>
                        </div>

                        <!-- Método de Pago -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <button 
                                v-for="method in ['Efectivo', 'Tarjeta', 'Transferencia', 'Crédito']"
                                :key="method"
                                @click="paymentForm.payment_method = method"
                                class="py-3 px-2 rounded-xl border-2 font-bold text-sm transition-all"
                                :class="paymentForm.payment_method === method 
                                    ? 'border-blue-500 bg-blue-50 text-blue-700' 
                                    : 'border-gray-100 text-gray-500 hover:border-gray-200'"
                            >
                                {{ method }}
                            </button>
                        </div>

                        <!-- Inputs Pago -->
                        <div v-if="paymentForm.payment_method === 'Efectivo'" class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Monto Recibido</label>
                            <n-input-number 
                                v-model:value="paymentForm.amount_received" 
                                size="large" 
                                class="text-xl"
                                :min="grandTotal"
                            >
                                <template #prefix>$</template>
                            </n-input-number>
                            
                            <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-100 flex justify-between items-center">
                                <span class="font-bold text-green-700">Cambio a Entregar:</span>
                                <span class="text-xl font-bold text-green-800">{{ formatCurrency(changeAmount) }}</span>
                            </div>
                        </div>
                        
                        <div v-if="paymentForm.payment_method === 'Crédito'" class="mb-6 p-4 bg-orange-50 rounded-xl border border-orange-100 text-sm text-orange-800">
                            <p class="font-bold mb-1">⚠ Venta a Crédito</p>
                            <p>El total se cargará a la cuenta del cliente seleccionado.</p>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button @click="showCheckoutModal = false" class="py-3.5 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200">
                            Cancelar
                        </button>
                        <button 
                            @click="submitSale"
                            :disabled="processingSale || (paymentForm.payment_method === 'Crédito' && !paymentForm.client_id)"
                            class="py-3.5 rounded-xl font-bold text-white bg-gray-900 shadow-lg shadow-gray-900/20 hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center"
                        >
                            <span v-if="!processingSale">Confirmar Venta</span>
                            <span v-else class="animate-pulse">Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </n-modal>

        <!-- 4. MODAL CLIENTE RÁPIDO -->
        <n-modal v-model:show="showQuickClientModal">
            <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl">
                <h3 class="font-bold text-lg mb-4">Nuevo Cliente Rápido</h3>
                <div class="space-y-3">
                    <input v-model="quickClientForm.name" class="w-full rounded-lg border-gray-300" placeholder="Nombre Completo *">
                    <input v-model="quickClientForm.phone" class="w-full rounded-lg border-gray-300" placeholder="Teléfono">
                    <input v-model="quickClientForm.email" class="w-full rounded-lg border-gray-300" placeholder="Email">
                    <input v-model="quickClientForm.tax_id" class="w-full rounded-lg border-gray-300" placeholder="RFC / NIT">
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="showQuickClientModal = false" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg font-bold">Cancelar</button>
                    <button @click="submitQuickClient" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">Guardar</button>
                </div>
            </div>
        </n-modal>

        <!-- 5. MODAL CIERRE DE CAJA -->
        <n-modal v-model:show="showCloseRegisterModal" :mask-closable="false">
             <div class="bg-white rounded-3xl p-8 w-full max-w-md text-center shadow-2xl">
                 <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                 </div>
                 <h2 class="text-2xl font-bold text-gray-900 mb-2">Corte de Caja</h2>
                 <p class="text-gray-500 mb-6">Ingresa el efectivo contado para cerrar el turno.</p>

                 <div class="text-left space-y-4">
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">Efectivo en Caja (Real)</label>
                         <n-input-number 
                            v-model:value="closeRegisterForm.closing_amount" 
                            :show-button="false"
                            placeholder="0.00"
                            class="text-lg"
                         >
                            <template #prefix>$</template>
                         </n-input-number>
                     </div>
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">Notas / Observaciones</label>
                         <textarea 
                            v-model="closeRegisterForm.notes" 
                            class="w-full rounded-xl border-gray-200 text-sm p-3" 
                            rows="3" 
                            placeholder="Diferencias, justificaciones, etc."
                         ></textarea>
                     </div>
                 </div>

                 <div class="flex gap-3 mt-8">
                     <button 
                        @click="showCloseRegisterModal = false"
                        class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-colors"
                     >
                         Cancelar
                     </button>
                     <button 
                        @click="submitCloseRegister"
                        class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-colors"
                     >
                         Cerrar Turno
                     </button>
                 </div>
             </div>
        </n-modal>

    </n-config-provider>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  NConfigProvider, NModal, NSelect, NInputNumber, NSwitch, createDiscreteApi
} from 'naive-ui';
import axios from 'axios';

// API para notificaciones fuera del setup de provider
const { message } = createDiscreteApi(['message']);

const props = defineProps({
    catalog: Array,
    activeSession: Object,
    availableRegisters: Array,
    user: Object
});

// --- ESTADO GENERAL ---
const searchQuery = ref('');
const filterType = ref('all'); // all, product, service
const cart = ref([]);
const processingSale = ref(false);

// --- ESTADO MODALES ---
const showOpenRegisterModal = ref(!props.activeSession);
const showCheckoutModal = ref(false);
const showQuickClientModal = ref(false);
const showOptionsModal = ref(false);
const showCloseRegisterModal = ref(false);

// --- ESTADO OPCIONES (SERVICIOS) ---
const currentOptionItem = ref(null);

// --- FORMULARIOS ---
const openRegisterForm = useForm({
    cash_register_id: null,
    opening_amount: 0
});

const closeRegisterForm = useForm({
    cash_register_session_id: props.activeSession ? props.activeSession.id : null,
    closing_amount: 0,
    notes: ''
});

const paymentForm = useForm({
    client_id: null,
    items: [],
    payment_method: 'Efectivo',
    amount_received: 0,
    shipping_cost: 0,
    shipping_address: {},
    requires_shipping: false,
    notes: ''
});

const quickClientForm = useForm({
    name: '', email: '', phone: '', tax_id: ''
});

// --- CLIENT SEARCH & DATA ---
const searchedClients = ref([]);
const selectedClientData = ref(null);
const shippingAddressString = ref('');

// --- COMPUTADOS ---
const filteredCatalog = computed(() => {
    let items = props.catalog;
    
    // Filtrar por tipo
    if (filterType.value !== 'all') {
        items = items.filter(i => i.type === filterType.value);
    }
    
    // Filtrar por búsqueda
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        items = items.filter(i => 
            i.name.toLowerCase().includes(q) || 
            (i.sku && i.sku.toLowerCase().includes(q))
        );
    }
    
    return items;
});

const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.sale_price * item.quantity), 0);
});

const grandTotal = computed(() => {
    return cartTotal.value + (paymentForm.requires_shipping ? (paymentForm.shipping_cost || 0) : 0);
});

const changeAmount = computed(() => {
    return Math.max(0, paymentForm.amount_received - grandTotal.value);
});

const registerOptions = computed(() => {
    return props.availableRegisters.map(r => ({ label: r.name + (r.location ? ` - ${r.location}` : ''), value: r.id }));
});

// --- MÉTODOS ---

const formatCurrency = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val);

// Carrito (Modificado para soportar opciones)
const addToCart = (item, selectedOption = null) => {
    // 1. Validar Stock (solo productos físicos sin opciones complejas por ahora)
    if (item.type === 'product' && item.stock_quantity <= 0) return;

    // 2. Verificar si tiene opciones de precio y no se ha seleccionado ninguna
    if (item.pricing_options && item.pricing_options.length > 0 && !selectedOption) {
        currentOptionItem.value = item;
        showOptionsModal.value = true;
        return;
    }

    // 3. Determinar precio y nombre final
    const finalPrice = selectedOption ? parseFloat(selectedOption.price) : item.sale_price;
    // Si hay opción, agregamos el label al nombre para mostrarlo en el carrito y ticket
    const variantLabel = selectedOption ? selectedOption.label : null;
    const finalName = variantLabel ? `${item.name} (${variantLabel})` : item.name;

    // 4. Buscar existencia en carrito (considerando variante)
    const existingIndex = cart.value.findIndex(c => 
        c.id === item.id && 
        c.type === item.type && 
        c.variant_label === variantLabel
    );

    if (existingIndex !== -1) {
        const existing = cart.value[existingIndex];
        // Validar stock maximo solo para productos
        if (item.type === 'product' && existing.quantity >= item.stock_quantity) return;
        
        existing.quantity++;
    } else {
        cart.value.push({ 
            ...item, 
            quantity: 1,
            sale_price: finalPrice, // Sobreescribimos precio con el de la opción
            name: finalName,         // Sobreescribimos nombre para visualización
            variant_label: variantLabel // Guardamos label original para lógica
        });
    }
};

const handleOptionSelect = (option) => {
    if (currentOptionItem.value) {
        addToCart(currentOptionItem.value, option);
        showOptionsModal.value = false;
        currentOptionItem.value = null;
    }
};

const incrementItem = (index) => {
    const item = cart.value[index];
    // Revalidar stock contra catalogo original
    const original = props.catalog.find(c => c.id === item.id && c.type === item.type);
    if (item.type === 'product' && item.quantity >= original.stock_quantity) return;
    item.quantity++;
};

const decrementItem = (index) => {
    if (cart.value[index].quantity > 1) {
        cart.value[index].quantity--;
    } else {
        removeFromCart(index);
    }
};

const removeFromCart = (index) => cart.value.splice(index, 1);

// Caja
const submitOpenRegister = () => {
    openRegisterForm.post(route('pos.open-register'), {
        onSuccess: () => showOpenRegisterModal.value = false
    });
};

// Cierre de Caja
const submitCloseRegister = () => {
    closeRegisterForm.cash_register_session_id = props.activeSession?.id;
    
    closeRegisterForm.post(route('pos.close-register'), {
        onSuccess: () => {
            showCloseRegisterModal.value = false;
            message.success('Caja cerrada correctamente');
        },
        onError: () => {
            message.error('Error al cerrar la caja');
        }
    });
};

// Checkout
const openCheckout = () => {
    // Reset form defaults
    paymentForm.amount_received = grandTotal.value; // Sugerir monto exacto
    // Resetear dirección si no hay cliente
    if (!paymentForm.client_id) shippingAddressString.value = '';
    showCheckoutModal.value = true;
};

// Búsqueda Cliente
const handleClientSearch = async (query) => {
    if (!query) return;
    const res = await axios.get(route('pos.search-clients', { query }));
    searchedClients.value = res.data.map(c => ({
        label: c.name,
        value: c.id,
        ...c
    }));
};

const onClientSelect = (value, option) => {
    selectedClientData.value = option || null;
    if (option && option.address) {
        // Prellenar dirección
        const addr = option.address;
        const parts = [addr.street, addr.exterior_number, addr.neighborhood, addr.zip_code].filter(Boolean);
        shippingAddressString.value = parts.join(', ');
    }
};

// Crear Cliente Rápido
const submitQuickClient = async () => {
    try {
        const res = await axios.post(route('pos.quick-client'), quickClientForm.data());
        showQuickClientModal.value = false;
        
        // Auto seleccionar al nuevo cliente
        const newClient = res.data.client;
        searchedClients.value = [{ label: newClient.name, value: newClient.id, ...newClient }];
        paymentForm.client_id = newClient.id;
        selectedClientData.value = { ...newClient, available_credit: 0 }; // Nuevo cliente sin crédito aun
        
        quickClientForm.reset();
        message.success('Cliente creado correctamente');
    } catch (error) {
        console.error(error);
        message.error('Error al crear cliente');
    }
};

// Enviar Venta
const submitSale = () => {
    processingSale.value = true;
    
    // Preparar JSON de dirección
    if (paymentForm.requires_shipping) {
        paymentForm.shipping_address = { full_text: shippingAddressString.value };
    }

    // Pasar items del carrito al form
    paymentForm.items = cart.value.map(i => ({
        id: i.id,
        class_type: i.class_type,
        quantity: i.quantity,
        sale_price: i.sale_price,
        name: i.name // Enviamos el nombre con la variante incluida
    }));

    paymentForm.post(route('pos.store'), {
        onSuccess: () => {
            showCheckoutModal.value = false;
            cart.value = [];
            paymentForm.reset();
            selectedClientData.value = null;
            processingSale.value = false;
            // Notificación de éxito
            message.success("Venta registrada correctamente");
        },
        onError: (errors) => {
            console.error(errors);
            processingSale.value = false;
            message.error("Error al procesar la venta. Verifique los datos.");
        }
    });
};

// Theme Overrides (Copiado de Show.vue)
const iosThemeOverrides = {
  common: {
    primaryColor: '#007AFF',
    borderRadius: '12px',
  },
  Input: {
      borderRadius: '10px'
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>