<script setup>
defineProps({
    products: {
        type: Array,
        default: () => [] // Valor por defecto para evitar el error de 'undefined'
    }
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-red-50/30">
            <h3 class="font-bold text-lg text-red-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Stock Bajo
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            <div v-for="product in products" :key="product.id" class="p-4 flex items-center justify-between hover:bg-gray-50/50 transition">
                <div class="flex-1 pr-4">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-md">
                        {{ product.stock_quantity }} / {{ product.alert_threshold }}
                    </span>
                </div>
            </div>
            
            <div v-if="products.length === 0" class="p-6 text-center">
                <p class="text-sm text-green-600 font-medium">¡Inventario saludable!</p>
            </div>
        </div>
    </div>
</template>