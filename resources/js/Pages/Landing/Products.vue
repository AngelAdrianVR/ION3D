<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    NConfigProvider, 
    NButton, 
    NModal,
} from 'naive-ui';
import { onMounted, ref, reactive } from 'vue';

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

// --- Estado para carrusel de imágenes por tarjeta ---
// Guarda el índice actual de imagen para cada producto: { [productId]: currentIndex }
const imageIndices = reactive({});

const getCurrentImage = (product) => {
    const images = product.images || [];
    if (images.length === 0) return null;
    const idx = imageIndices[product.id] ?? 0;
    return images[idx]?.url || null;
};

const nextImage = (product) => {
    const images = product.images || [];
    if (images.length <= 1) return;
    const current = imageIndices[product.id] ?? 0;
    imageIndices[product.id] = (current + 1) % images.length;
};

const prevImage = (product) => {
    const images = product.images || [];
    if (images.length <= 1) return;
    const current = imageIndices[product.id] ?? 0;
    imageIndices[product.id] = (current - 1 + images.length) % images.length;
};

// --- Modal de Detalles ---
const showDetailModal = ref(false);
const selectedProduct = ref(null);

const openDetailModal = (product) => {
    selectedProduct.value = product;
    showDetailModal.value = true;
};

// Imagen seleccionada en el modal
const modalSelectedImage = ref(null);

const openModalImage = (url) => {
    modalSelectedImage.value = url;
};

// Modal de imagen a pantalla completa
const showFullImage = ref(false);
const fullImageUrl = ref(null);

const openFullImage = (url) => {
    fullImageUrl.value = url;
    showFullImage.value = true;
};

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
                            Todo lo que Necesitas <br />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Para Transformar tu Espacio</span>
                        </h1>
                        <p class="text-slate-300 text-lg max-w-2xl mx-auto">
                            Explora una colección de productos diseñados para trabajar como un solo sistema y crear espacios únicos que evolucionan contigo.
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
                                
                                <!-- Imagen con navegación -->
                                <div class="relative h-64 overflow-hidden bg-slate-100 group">
                                    <!-- Imagen o Placeholder -->
                                    <img 
                                        v-if="getCurrentImage(product)"
                                        :src="getCurrentImage(product)" 
                                        :alt="product.name" 
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    />
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-medium">Producto sin imagen</span>
                                    </div>
                                    
                                    <!-- Flechas de navegación de imágenes (solo si hay más de 1) -->
                                    <template v-if="(product.images || []).length > 1">
                                        <button 
                                            @click.stop="prevImage(product)"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
                                            title="Imagen anterior"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                        </button>
                                        <button 
                                            @click.stop="nextImage(product)"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
                                            title="Imagen siguiente"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                        <!-- Indicador de posición (dots) -->
                                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span 
                                                v-for="(img, i) in product.images" 
                                                :key="i"
                                                class="w-1.5 h-1.5 rounded-full transition-all"
                                                :class="(imageIndices[product.id] ?? 0) === i ? 'bg-white scale-110' : 'bg-white/50'"
                                            ></span>
                                        </div>
                                    </template>
                                    
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
                                    
                                    <p class="text-slate-500 text-sm mb-4 flex-grow line-clamp-3 leading-relaxed">
                                        {{ product.description }}
                                    </p>

                                    <!-- Footer Card -->
                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto gap-2">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-slate-400 font-medium">Precio</span>
                                            <span class="text-xl font-bold text-[#2f4b59]">{{ formatCurrency(product.price) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5">
                                            <!-- Botón Ver Detalles -->
                                            <button 
                                                @click.stop="openDetailModal(product)"
                                                class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-[#4cc9f0] text-slate-500 hover:text-white rounded-xl transition-all duration-300 border border-slate-200 hover:border-[#4cc9f0]"
                                                title="Ver detalles"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <a 
                                                v-if="!product.is_out_of_stock"
                                                :href="`https://wa.me/5213343249116?text=${encodeURIComponent('Hola! Me interesa comprar el producto: ' + product.name)}`"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-1.5 shadow-md shadow-green-500/20"
                                            >
                                                <span>Comprar</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            </a>
                                            <button v-else disabled class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl font-bold text-sm cursor-not-allowed border border-slate-200">
                                                No disponible
                                            </button>
                                        </div>
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
                                <h3 class="text-2xl md:text-3xl font-bold mb-2">Comenzar mi proyecto.</h3>
                                <p class="text-slate-300">Nuestro equipo te guiará paso a paso y te acompañaremos en cada etapa para diseñar, instalar y evolucionar.</p>
                            </div>
                            <Link href="/contacto">
                                <n-button type="primary" size="large" class="!bg-[#4cc9f0] !text-[#0f172a] !font-bold !rounded-xl !h-12 !px-8 hover:!bg-white hover:!text-[#2f4b59]">
                                    Contactar Ventas
                                </n-button>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- 5. MODAL DE DETALLES DEL PRODUCTO -->
                <NModal v-model:show="showDetailModal" transform-origin="center">
                    <div v-if="selectedProduct" class="bg-white w-[95%] max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl shadow-2xl" @click.stop>
                        
                        <!-- Header del Modal -->
                        <div class="sticky top-0 bg-white z-10 p-6 border-b border-slate-100 flex items-start justify-between rounded-t-3xl">
                            <div>
                                <p class="text-xs font-mono text-slate-400 uppercase tracking-wide">{{ selectedProduct.sku || 'SKU-GEN' }}</p>
                                <h3 class="text-2xl font-bold text-[#2f4b59]">{{ selectedProduct.name }}</h3>
                            </div>
                            <button 
                                @click="showDetailModal = false"
                                class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-6">
                            
                            <!-- Galería de Imágenes -->
                            <div v-if="selectedProduct.images && selectedProduct.images.length > 0">
                                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Galería de Imágenes</h4>
                                
                                <!-- Imagen Principal (ampliada) -->
                                <div class="rounded-2xl overflow-hidden bg-slate-100 mb-3 aspect-video">
                                    <img 
                                        :src="modalSelectedImage || selectedProduct.images[0].url" 
                                        :alt="selectedProduct.name"
                                        class="w-full h-full object-contain cursor-zoom-in"
                                        @click="modalSelectedImage && openFullImage(modalSelectedImage)"
                                    />
                                </div>

                                <!-- Miniaturas -->
                                <div class="flex gap-2 overflow-x-auto pb-1">
                                    <div 
                                        v-for="(img, i) in selectedProduct.images" 
                                        :key="img.id"
                                        @click="modalSelectedImage = img.url"
                                        class="w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 cursor-pointer transition-all hover:scale-105"
                                        :class="(modalSelectedImage || selectedProduct.images[0].url) === img.url ? 'border-[#4cc9f0] shadow-md' : 'border-slate-200 hover:border-slate-400'"
                                    >
                                        <img :src="img.url" class="w-full h-full object-cover" alt="Miniatura" />
                                    </div>
                                </div>
                            </div>

                            <!-- Video -->
                            <div v-if="selectedProduct.video_url">
                                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Video del Producto</h4>
                                <div class="rounded-2xl overflow-hidden bg-black">
                                    <video 
                                        :src="selectedProduct.video_url" 
                                        class="w-full aspect-video" 
                                        controls 
                                        preload="metadata"
                                        controlsList="nodownload"
                                    >
                                        Tu navegador no soporta la reproducción de video.
                                    </video>
                                </div>
                            </div>

                            <!-- Descripción Completa -->
                            <div v-if="selectedProduct.description">
                                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Descripción</h4>
                                <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ selectedProduct.description }}</p>
                            </div>

                            <!-- Info Precio y Stock -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 rounded-2xl p-4">
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Precio</p>
                                    <p class="text-2xl font-bold text-[#2f4b59]">{{ formatCurrency(selectedProduct.price) }}</p>
                                </div>
                                <div class="rounded-2xl p-4" :class="selectedProduct.is_out_of_stock ? 'bg-red-50' : selectedProduct.is_low_stock ? 'bg-orange-50' : 'bg-green-50'">
                                    <p class="text-xs font-bold uppercase tracking-wider mb-1" :class="selectedProduct.is_out_of_stock ? 'text-red-400' : selectedProduct.is_low_stock ? 'text-orange-400' : 'text-green-500'">Stock</p>
                                    <p class="text-2xl font-bold" :class="selectedProduct.is_out_of_stock ? 'text-red-600' : selectedProduct.is_low_stock ? 'text-orange-600' : 'text-green-700'">
                                        {{ selectedProduct.is_out_of_stock ? 'Agotado' : selectedProduct.is_low_stock ? 'Bajo' : 'Disponible' }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        <!-- Footer del Modal -->
                        <div class="sticky bottom-0 bg-white border-t border-slate-100 p-4 rounded-b-3xl flex justify-end gap-3">
                            <button 
                                @click="showDetailModal = false"
                                class="px-6 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors"
                            >
                                Cerrar
                            </button>
                            <a 
                                v-if="!selectedProduct.is_out_of_stock"
                                :href="`https://wa.me/5213343249116?text=${encodeURIComponent('Me interesa comprar el producto: ' + selectedProduct.name)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition-colors flex items-center gap-2 shadow-md shadow-green-500/20"
                            >
                                Comprar por WhatsApp
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>

                    </div>
                </NModal>

                <!-- Modal de Imagen a Pantalla Completa -->
                <NModal v-model:show="showFullImage" transform-origin="center">
                    <div class="bg-transparent p-0 outline-none flex justify-center items-center" style="max-height: 90vh; max-width: 90vw;">
                        <img v-if="fullImageUrl" :src="fullImageUrl" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border-4 border-white/20" alt="Vista previa">
                    </div>
                </NModal>

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