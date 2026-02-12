<script setup>
defineProps({
    orders: {
        type: Array,
        default: () => [] // Valor por defecto: array vacío para evitar errores de undefined
    }
});

const statusClasses = {
    'Pagado': 'bg-green-100 text-green-700',
    'Pendiente': 'bg-yellow-100 text-yellow-700',
    'Parcial': 'bg-blue-100 text-blue-700',
    'Crédito': 'bg-purple-100 text-purple-700',
};

const formatMoney = (amount) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-900">Ventas Recientes</h3>
            <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Ver todas</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">Folio / Cliente</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="text-right py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50/50 transition">
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 text-sm">{{ order.client_name }}</span>
                                <span class="text-xs text-gray-400">Hace {{ order.date }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span :class="['px-3 py-1 text-xs font-semibold rounded-full', statusClasses[order.status] || 'bg-gray-100 text-gray-600']">
                                {{ order.status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-gray-900 text-sm">{{ formatMoney(order.total) }}</span>
                        </td>
                    </tr>
                    <!-- Ahora 'orders' siempre será un array, así que .length no fallará -->
                    <tr v-if="orders.length === 0">
                        <td colspan="3" class="py-8 text-center text-gray-400 text-sm">No hay ventas recientes</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>