<script>
import { Link } from '@inertiajs/vue3';
import { 
    NConfigProvider, 
    NGlobalStyle, 
    NButton, 
    NMessageProvider
} from 'naive-ui';
// Importamos el nuevo componente
import AppointmentModal from '@/Components/MyComponents/AppointmentModal.vue'; 

export default {
    name: 'AppLayoutLanding',
    components: {
        Link,
        NConfigProvider,
        NGlobalStyle,
        NButton,
        NMessageProvider,
        AppointmentModal // Registro del componente
    },
    data() {
        return {
            isScrolled: false,
            mobileMenuOpen: false,
            activeDropdown: null,
            
            // Estado para abrir/cerrar el modal
            showAppointmentModal: false,

            themeOverrides: {
                common: {
                    primaryColor: '#2f4b59', 
                    primaryColorHover: '#3e6070',
                    primaryColorPressed: '#1f333d',
                    fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif'
                },
                Button: {
                    borderRadiusMedium: '8px',
                    borderRadiusLarge: '24px', 
                    fontWeight: '600',
                    colorFocusPrimary: '#3e607033',
                    borderFocusPrimary: '1px solid #2f4b59'
                },
                Card: { borderRadius: '16px' },
                Input: { borderRadius: '8px' }
            },
            
            menuOptions: {
                servicios: [
                    { label: 'Escaneo 3D de Personas', href: '/servicios/escaneo-personas', icon: 'scan' },
                    { label: 'Impresión Full Color', href: '/servicios/impresion', icon: 'print' },
                    { label: 'Modelado Digital', href: '/servicios/modelado', icon: 'cube' },
                    { label: 'Eventos y Bodas', href: '/servicios/eventos', icon: 'party' },
                ],
                galeria: [
                    { label: 'Portafolio Comercial', href: '/portafolio/comercial', icon: 'briefcase' },
                    { label: 'Arte y Figuras', href: '/portafolio/arte', icon: 'art' },
                    { label: 'Casos de Éxito', href: '/portafolio/casos', icon: 'star' },
                ]
            }
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        handleScroll() {
            this.isScrolled = window.scrollY > 50;
        },
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
        setActiveDropdown(name) {
            this.activeDropdown = name;
        },
        clearActiveDropdown() {
            this.activeDropdown = null;
        },
        isActive(routePrefix) {
            return this.$page.url.startsWith(routePrefix);
        }
    }
}
</script>

<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <n-global-style />
        <n-message-provider>
        
            <div class="min-h-screen flex flex-col relative bg-slate-100 font-sans text-slate-800 selection:bg-[#2f4b59] selection:text-white overflow-x-hidden">
                
                <!-- Fondo Decorativo Sutil -->
                <div class="fixed inset-0 pointer-events-none z-0 opacity-[0.3]" 
                     style="background-image: radial-gradient(#2f4b59 0.5px, transparent 0.5px); background-size: 24px 24px;">
                </div>

                <!-- ========================================== -->
                <!-- BARRA DE NAVEGACIÓN DINÁMICA               -->
                <!-- ========================================== -->
                <div class="fixed top-0 left-0 right-0 z-50 flex justify-center transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.25,1)]"
                     :class="[isScrolled ? 'pt-4' : 'pt-6']">
                    
                    <nav 
                        :class="[
                            'transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.25,1)] flex items-center',
                            isScrolled 
                                ? 'w-auto bg-white/60 backdrop-blur-md border border-slate-200 shadow-2xl shadow-[#2f4b59]/10 rounded-full px-2 py-2 gap-2' 
                                : 'w-full max-w-7xl px-6 bg-transparent border-transparent gap-8'
                        ]"
                    >
                        <!-- 1. LOGO -->
                        <Link href="/inicio" 
                            class="flex items-center gap-2 group relative shrink-0 transition-all duration-500"
                            :class="[isScrolled ? 'pl-2' : 'mr-auto']"
                        >
                            <div class="relative size-10 p-1 bg-[#2f4b59] rounded-full flex items-center justify-center text-white overflow-hidden shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <img src="@/../../public/images/orion-logo-blanco.png" alt="">
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            <div class="flex flex-col leading-none transition-all duration-300" 
                                 :class="[isScrolled ? 'w-0 opacity-0 overflow-hidden' : 'w-auto opacity-100']">
                                <span class="text-xl font-bold tracking-tight text-[#2f4b59]">
                                    ION<span class="text-slate-400">3D</span>
                                </span>
                            </div>
                        </Link>

                        <!-- 2. MENÚ CENTRAL -->
                        <div class="hidden md:flex items-center">
                            <div class="flex items-center transition-all duration-300" 
                                 :class="[isScrolled ? 'gap-1' : 'gap-6 bg-white/50 px-6 py-2 rounded-full border border-white/10 backdrop-blur-sm shadow-sm']">
                                
                                <Link href="/proceso" 
                                    class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                    :class="isActive('/proceso') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Proceso
                                </Link>

                                <div class="relative group" @mouseenter="setActiveDropdown('servicios')" @mouseleave="clearActiveDropdown">
                                    <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20 outline-none"
                                         :class="isActive('/servicios') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                        Servicios
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="activeDropdown === 'servicios' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                                        <div v-show="activeDropdown === 'servicios'" class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-64 bg-white/100 backdrop-blur-xl border border-white/40 rounded-2xl shadow-xl p-2 z-50 overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-b from-[#2f4b59]/5 to-transparent pointer-events-none"></div>
                                            <Link v-for="(item, idx) in menuOptions.servicios" :key="idx" :href="item.href" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-200/80 transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-[#2f4b59] flex items-center justify-center group-hover/item:bg-[#2f4b59] group-hover/item:text-white transition-colors">
                                                    <svg v-if="item.icon === 'scan'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v4.25m0 0l3-3.5m-3 3.5l-3-3.5" /></svg>
                                                    <svg v-else-if="item.icon === 'print'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" /></svg>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700">{{ item.label }}</span>
                                            </Link>
                                        </div>
                                    </transition>
                                </div>

                                <div class="relative group" @mouseenter="setActiveDropdown('galeria')" @mouseleave="clearActiveDropdown">
                                    <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20 outline-none"
                                        :class="isActive('/portafolio') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                        Galería
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="activeDropdown === 'galeria' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                                        <div v-show="activeDropdown === 'galeria'" class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-56 bg-white/100 backdrop-blur-xl border border-white/40 rounded-2xl shadow-xl p-2 z-50 overflow-hidden">
                                            <Link v-for="(item, idx) in menuOptions.galeria" :key="idx" :href="item.href" class="block px-4 py-2.5 rounded-xl hover:bg-gray-200/80 transition-colors text-sm font-medium text-slate-700 hover:text-[#2f4b59]">
                                                {{ item.label }}
                                            </Link>
                                        </div>
                                    </transition>
                                </div>
                                
                                 <Link href="/contacto" 
                                    class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                    :class="isActive('/contacto') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Contacto
                                </Link>
                            </div>
                        </div>

                        <!-- 3. BOTÓN DE ACCIÓN -->
                        <div class="transition-all duration-500" :class="[isScrolled ? 'pr-1' : 'ml-auto']">
                            <n-button 
                                type="primary" 
                                :round="true" 
                                :size="isScrolled ? 'medium' : 'large'" 
                                class="shadow-lg shadow-[#2f4b59]/20 transition-all duration-300"
                                @click="showAppointmentModal = true"
                            >
                                <template #icon>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </template>
                                <span v-if="!isScrolled">Agendar Cita</span>
                                <span v-else>Agendar</span>
                            </n-button>
                        </div>

                        <button @click="toggleMobileMenu" class="md:hidden ml-auto p-2 text-slate-700 hover:text-[#2f4b59] transition-colors bg-white/50 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </nav>
                </div>

                <!-- ========================================== -->
                <!-- COMPONENTE: MODAL DE AGENDAMIENTO          -->
                <!-- ========================================== -->
                <AppointmentModal 
                    v-model:show="showAppointmentModal" 
                    :theme-overrides="themeOverrides"
                />

                <!-- Menú Móvil Overlay -->
                <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-10" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-10">
                    <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 bg-white/95 backdrop-blur-xl pt-24 px-6 md:hidden flex flex-col gap-6 overflow-y-auto">
                        <Link href="/" class="text-2xl font-bold text-[#2f4b59]">Inicio</Link>
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-widest text-slate-400 font-bold">Servicios</h3>
                            <Link v-for="item in menuOptions.servicios" :key="item.label" :href="item.href" class="block text-lg font-medium text-slate-800">{{ item.label }}</Link>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-xs uppercase tracking-widest text-slate-400 font-bold">Galería</h3>
                            <Link v-for="item in menuOptions.galeria" :key="item.label" :href="item.href" class="block text-lg font-medium text-slate-800">{{ item.label }}</Link>
                        </div>
                    </div>
                </transition>

                <!-- ========================================== -->
                <!-- CONTENIDO (SLOT)                           -->
                <!-- ========================================== -->
                <main class="flex-grow pt-20 relative z-10">
                    <slot />
                </main>

                <!-- FOOTER (Se mantiene igual, solo resumido para visualización) -->
                <footer class="bg-slate-900 text-white pt-20 pb-10 relative overflow-hidden mt-auto">
                    <!-- ... contenido del footer ... -->
                    <div class="absolute top-0 left-0 right-0 h-[2px] w-full overflow-hidden">
                        <div class="absolute inset-0 w-[200%] bg-gradient-to-r from-transparent via-[#4cc9f0] to-transparent animate-aurora opacity-70"></div>
                    </div>
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-[#2f4b59] opacity-20 blur-[100px] pointer-events-none rounded-full"></div>

                    <div class="max-w-7xl mx-auto px-6 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
                            <!-- Columna 1 -->
                            <div class="md:col-span-4 space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                                        <img src="@/../../public/images/orion-logo-blanco.png" alt="">
                                    </div>
                                    <span class="text-xl font-bold tracking-tight text-[#6a96ac]">
                                        ION<span class="text-slate-200">3D</span>
                                    </span>
                                </div>
                                <p class="text-slate-400">Redefiniendo los límites entre lo físico y lo digital.</p>
                            </div>
                            <!-- Enlaces y demás columnas del footer se mantienen igual -->
                        </div>
                    </div>
                </footer>

            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<style>
@keyframes aurora {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0%); }
}
.animate-aurora {
    animation: aurora 5s linear infinite;
}
</style>