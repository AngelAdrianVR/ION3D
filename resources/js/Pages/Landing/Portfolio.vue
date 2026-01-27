<script>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import { Head } from '@inertiajs/vue3';
import { NTabs, NTabPane } from 'naive-ui';

export default {
    name: 'PortfolioView',
    components: {
        AppLayoutLanding,
        Head,
        NTabs,
        NTabPane
    },
    props: {
        category: {
            type: String,
            default: 'comercial'
        }
    },
    data() {
        return {
            activeTab: this.category || 'comercial',
            // Datos simulados de galería
            galleryItems: {
                'comercial': [
                    { id: 1, title: 'Campaña Nike', img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=500' },
                    { id: 2, title: 'Producto 360', img: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=500' },
                    { id: 3, title: 'Joyería VR', img: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&q=80&w=500' }
                ],
                'arte': [
                    { id: 4, title: 'Escultura Digital', img: 'https://images.unsplash.com/photo-1558865869-c93f6f8482af?auto=format&fit=crop&q=80&w=500' },
                    { id: 5, title: 'Figura Coleccionable', img: 'https://images.unsplash.com/photo-1606041011872-596597976b25?auto=format&fit=crop&q=80&w=500' },
                    { id: 6, title: 'Busto Artístico', img: 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=500' }
                ],
                'casos': [
                    { id: 7, title: 'Prótesis Médica', img: 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&q=80&w=500' },
                    { id: 8, title: 'Patrimonio Cultural', img: 'https://images.unsplash.com/photo-1599593256050-13f570087c53?auto=format&fit=crop&q=80&w=500' }
                ]
            }
        }
    },
    watch: {
        category(newVal) {
            if (newVal) this.activeTab = newVal;
        },
        activeTab(newVal) {
            if (newVal !== this.category) {
                this.$inertia.visit(`/portafolio/${newVal}`, { preserveState: true, replace: true });
            }
        }
    }
}
</script>

<template>
    <AppLayoutLanding>
        <Head title="Portafolio" />

        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-[#2f4b59] mb-4">Portafolio</h1>
                <p class="text-slate-600">Explora nuestros trabajos recientes.</p>
            </div>

            <!-- Tabs de Navegación -->
            <div class="flex justify-center mb-12">
                <n-tabs v-model:value="activeTab" type="segment" class="max-w-md w-full custom-tabs">
                    <n-tab-pane name="comercial" tab="Comercial" />
                    <n-tab-pane name="arte" tab="Arte & Figuras" />
                    <n-tab-pane name="casos" tab="Casos de Éxito" />
                </n-tabs>
            </div>

            <!-- Grid de Galería con Animación -->
            <transition-group 
                tag="div" 
                class="grid grid-cols-1 md:grid-cols-3 gap-8"
                enter-active-class="transition duration-500 ease-out"
                enter-from-class="opacity-0 translate-y-10"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="absolute opacity-0"
            >
                <div v-for="item in galleryItems[activeTab]" :key="item.id" class="group relative rounded-2xl overflow-hidden aspect-square shadow-md cursor-pointer">
                    <img :src="item.img" :alt="item.title" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <!-- Overlay Hover -->
                    <div class="absolute inset-0 bg-[#2f4b59]/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center flex-col p-6 text-center">
                        <h3 class="text-white text-xl font-bold translate-y-4 group-hover:translate-y-0 transition-transform duration-300">{{ item.title }}</h3>
                        <p class="text-blue-200 text-sm mt-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">Ver Proyecto</p>
                    </div>
                </div>
            </transition-group>
        </div>
    </AppLayoutLanding>
</template>

<style scoped>
:deep(.n-tabs-rail) {
    border-radius: 9999px;
    background-color: #f1f5f9;
}
:deep(.n-tabs-tab--active) {
    background-color: white !important;
    color: #2f4b59 !important;
    font-weight: 700;
}
</style>