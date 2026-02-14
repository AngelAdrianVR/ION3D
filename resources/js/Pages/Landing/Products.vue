<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    NConfigProvider, 
    NButton, 
} from 'naive-ui';
import { onMounted, ref } from 'vue';

// Recibimos "products" como Objeto (LengthAwarePaginator) en lugar de Array
const props = defineProps({
    products: Object 
});

// --- Configuración de Tema ---
const themeOverrides = {
    common: {
        primaryColor: '#2f4b59',
        primaryColorHover: '#3e6070',
        primaryColorPressed: '#1f333d'
    },
    Card: {
        borderRadius: '16px',
        borderColor: '#e2e8f0'
    }
};

const showContent = ref(false);

onMounted(() => {
    setTimeout(() => {
        showContent.value = true;
    }, 100);
});

// Formateador de moneda MXN
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(amount);
};
</script>

<template>
    <AppLayoutLanding>
        <Head title="Productos" />
        
        <NConfigProvider :theme-overrides="themeOverrides">
            <div class="min-h-screen bg-slate-50 relative overflow-hidden">
                
                <!-- 1. HEADER HERO -->
                <div class="relative bg-[#2f4b59] pt-32 pb-20 md:pt-40 md:pb-24 rounded-b-[50px] shadow-2xl z-0 overflow-hidden">
                    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-[#4cc9f0] opacity-10 blur-[120px] rounded-full animate-pulse-slow"></div>
                    <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-[#ffffff] opacity-5 blur-[100px] rounded-full"></div>
                    <div class="absolute inset-0 opacity-10" 
                        style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;">
                    </div>

                    <div class="relative z-10 max-w-7xl mx-auto px-6 text-center">
                        <h2 class="text-[#4cc9f0] font-bold tracking-widest uppercase text-sm mb-3">Catálogo</h2>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
                            Hardware y Materiales <br />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">para Digitalización 3D</span>
                        </h1>
                        <p class="text-slate-300 text-lg max-w-2xl mx-auto">
                            Equipamiento de alta gama y consumibles seleccionados por nuestros expertos para tus proyectos de escaneo e impresión.
                        </p>
                    </div>
                </div>

                <!-- 2. GRID DE PRODUCTOS -->
                <div class="relative z-10 max-w-7xl mx-auto px-6 py-16 -mt-10">
                    
                    <!-- Estado Vacío -->
                    <div v-if="products.data.length === 0" class="bg-white rounded-3xl p-12 text-center shadow-lg border border-slate-100">
                        <div class="inline-flex p-4 bg-slate-100 rounded-full text-slate-400 mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700">No hay productos disponibles por ahora</h3>
                        <p class="text-slate-500 mt-2">Estamos actualizando nuestro inventario. Vuelve pronto.</p>
                    </div>

                    <!-- Grid -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 transition-all duration-1000 transform"
                         :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        
                        <!-- Iteramos sobre products.data debido a la paginación -->
                        <div v-for="product in products.data" :key="product.id" class="group h-full">
                            <div class="bg-white rounded-[20px] shadow-lg shadow-slate-200/50 hover:shadow-2xl hover:shadow-[#2f4b59]/10 border border-slate-100 overflow-hidden flex flex-col h-full transition-all duration-300 hover:-translate-y-1">
                                
                                <!-- Imagen -->
                                <div class="relative h-64 overflow-hidden bg-slate-100 group">
                                    <img :src="product.image" :alt="product.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    
                                    <!-- Badges -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        <span v-if="product.is_out_of_stock" class="bg-red-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                            Agotado
                                        </span>
                                        <span v-else-if="product.is_low_stock" class="bg-orange-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                            ¡Últimas piezas!
                                        </span>
                                        <span v-else class="bg-green-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                            Disponible
                                        </span>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <div class="p-6 flex flex-col flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-xs font-mono text-slate-400 uppercase tracking-wide">{{ product.sku || 'SKU-GEN' }}</p>
                                    </div>
                                    
                                    <h3 class="text-xl font-bold text-[#2f4b59] mb-2 leading-tight group-hover:text-[#4cc9f0] transition-colors">
                                        {{ product.name }}
                                    </h3>
                                    
                                    <p class="text-slate-500 text-sm mb-6 flex-grow line-clamp-3 leading-relaxed">
                                        {{ product.description }}
                                    </p>

                                    <!-- Footer Card -->
                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-slate-400 font-medium">Precio</span>
                                            <span class="text-xl font-bold text-[#2f4b59]">{{ formatCurrency(product.price) }}</span>
                                        </div>
                                        
                                        <Link href="/contacto" v-if="!product.is_out_of_stock">
                                            <button class="px-5 py-2.5 bg-slate-50 hover:bg-[#2f4b59] text-[#2f4b59] hover:text-white rounded-xl font-bold text-sm transition-all duration-300 border border-slate-200 hover:border-[#2f4b59] flex items-center gap-2">
                                                <span>Cotizar</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </button>
                                        </Link>
                                        <button v-else disabled class="px-5 py-2.5 bg-slate-100 text-slate-400 rounded-xl font-bold text-sm cursor-not-allowed border border-slate-200">
                                            No disponible
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. PAGINACIÓN -->
                    <div v-if="products.data.length > 0 && products.links.length > 3" class="mt-12 flex justify-center">
                        <div class="flex flex-wrap items-center gap-2 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                            <template v-for="(link, key) in products.links" :key="key">
                                <div v-if="link.url === null" 
                                     class="px-3 py-1 text-slate-300 text-sm" 
                                     v-html="link.label">
                                </div>
                                <Link v-else 
                                      :href="link.url"
                                      class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                                      :class="link.active 
                                        ? 'bg-[#2f4b59] text-white shadow-md' 
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-[#2f4b59]'"
                                      v-html="link.label">
                                </Link>
                            </template>
                        </div>
                    </div>

                </div>

                <!-- 4. CTA INFERIOR -->
                <div class="max-w-7xl mx-auto px-6 pb-20">
                    <div class="bg-gradient-to-r from-[#2f4b59] to-[#1f333d] rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-2xl">
                        <!-- Decoración -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                        
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="text-white">
                                <h3 class="text-2xl md:text-3xl font-bold mb-2">¿Buscas algo específico?</h3>
                                <p class="text-slate-300">Podemos conseguir hardware especializado bajo pedido.</p>
                            </div>
                            <Link href="/contacto">
                                <n-button type="primary" size="large" class="!bg-[#4cc9f0] !text-[#0f172a] !font-bold !rounded-xl !h-12 !px-8 hover:!bg-white hover:!text-[#2f4b59]">
                                    Contactar Ventas
                                </n-button>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </NConfigProvider>
    </AppLayoutLanding>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { opacity: 0.1; transform: scale(1); }
    50% { opacity: 0.2; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 6s infinite ease-in-out;
}
</style>