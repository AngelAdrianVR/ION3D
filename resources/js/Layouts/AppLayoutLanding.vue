<script>
import { Link } from '@inertiajs/vue3';
import { 
    NConfigProvider, 
    NGlobalStyle, 
    NButton, 
    NMessageProvider
} from 'naive-ui';
import AppointmentModal from '@/Components/MyComponents/AppointmentModal.vue'; 

export default {
    name: 'AppLayoutLanding',
    components: {
        Link,
        NConfigProvider,
        NGlobalStyle,
        NButton,
        NMessageProvider,
        AppointmentModal
    },
    data() {
        return {
            isScrolled: false,
            mobileMenuOpen: false,
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
                }
            },
            
            // Definimos los items principales para usarlos en el menú móvil
            mainNavItems: [
                { label: 'Inicio', href: '/inicio', icon: 'home' },
                { label: 'Proceso', href: '/proceso', icon: 'cpu' },
                { label: 'Servicios', href: '/servicios', icon: 'grid' },
                { label: 'Galería', href: '/portafolio', icon: 'image' },
                { label: 'Contacto', href: '/contacto', icon: 'mail' },
            ]
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
            // Bloquear scroll del body cuando el menú está abierto
            if (this.mobileMenuOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        },
        isActive(routePrefix) {
            return this.$page.url.startsWith(routePrefix);
        },
        // Icono helper para el menú móvil
        getIconPath(name) {
            const paths = {
                home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                cpu: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                grid: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                image: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                mail: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
            };
            return paths[name] || '';
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
                                ? 'w-[95%] md:w-auto bg-white/60 backdrop-blur-md border border-slate-200 shadow-2xl shadow-[#2f4b59]/10 rounded-full px-4 py-2 gap-2 justify-between md:justify-start' 
                                : 'w-full max-w-7xl px-6 bg-transparent border-transparent gap-8 justify-between md:justify-start'
                        ]"
                    >
                        <!-- 1. LOGO -->
                        <Link href="/inicio" 
                            class="flex items-center gap-2 group relative shrink-0 transition-all duration-500"
                            :class="[isScrolled ? 'pl-0' : 'mr-auto']"
                        >
                            <div class="relative size-10 p-1 bg-[#2f4b59] rounded-full flex items-center justify-center text-white overflow-hidden shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <img src="@/../../public/images/orion-logo-blanco.png" alt="">
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            <div class="flex flex-col leading-none transition-all duration-300" 
                                 :class="[isScrolled ? 'hidden lg:flex' : 'flex']">
                                <span class="text-xl font-bold tracking-tight text-[#2f4b59]">
                                    ORION
                                    <!-- <span class="text-slate-400">3D</span> -->
                                </span>
                            </div>
                        </Link>

                        <!-- 2. MENÚ CENTRAL (Desktop) -->
                        <div class="hidden md:flex items-center">
                            <div class="flex items-center transition-all duration-300" 
                                 :class="[isScrolled ? 'gap-1' : 'gap-6 bg-white/50 px-6 py-2 rounded-full border border-white/10 backdrop-blur-sm shadow-sm']">
                                
                                <Link href="/proceso" 
                                    class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                    :class="isActive('/proceso') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Proceso
                                </Link>

                                <Link href="/servicios"
                                     class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                     :class="isActive('/servicios') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Servicios
                                </Link>

                                <!-- GALERÍA (Sin Dropdown) -->
                                <Link href="/portafolio"
                                    class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                    :class="isActive('/portafolio') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Galería
                                </Link>
                                
                                 <Link href="/contacto" 
                                    class="relative px-4 py-2 text-sm font-medium transition-colors rounded-full hover:bg-[#2f4b59]/20"
                                    :class="isActive('/contacto') ? 'text-[#2f4b59] font-bold bg-gray-200/80' : 'text-slate-600 hover:text-[#2f4b59]'">
                                    Contacto
                                </Link>
                            </div>
                        </div>

                        <!-- 3. BOTÓN DE ACCIÓN (Desktop) -->
                        <div class="hidden md:block transition-all duration-500" :class="[isScrolled ? 'pr-1' : 'ml-auto']">
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

                        <!-- Botón Menú Móvil -->
                        <button @click="toggleMobileMenu" class="md:hidden p-2 text-slate-700 hover:text-[#2f4b59] transition-colors bg-white/50 rounded-full border border-slate-200 z-50 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </nav>
                </div>

                <!-- ========================================== -->
                <!-- MODAL DE AGENDAMIENTO                      -->
                <!-- ========================================== -->
                <AppointmentModal 
                    v-model:show="showAppointmentModal" 
                    :theme-overrides="themeOverrides"
                />

                <!-- ========================================== -->
                <!-- MENÚ MÓVIL (REDDISEÑADO)                   -->
                <!-- ========================================== -->
                <!-- Backdrop oscuro -->
                <transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm md:hidden" @click="toggleMobileMenu"></div>
                </transition>

                <!-- Panel deslizante -->
                <transition enter-active-class="transition-transform duration-300 ease-out" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-300 ease-in" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                    <div v-if="mobileMenuOpen" class="fixed top-0 right-0 bottom-0 w-[85%] max-w-sm z-50 bg-slate-50/95 backdrop-blur-2xl shadow-2xl md:hidden border-l border-white/50 flex flex-col">
                        
                        <!-- Header del Menú -->
                        <div class="h-24 flex items-center justify-between px-6 border-b border-slate-200">
                            <span class="text-xl font-bold text-[#2f4b59]">MENÚ</span>
                            <!-- El botón cerrar ya está controlado por el botón del navbar que queda encima (z-index) o se puede agregar uno aquí -->
                        </div>

                        <!-- Lista de Enlaces -->
                        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
                            <Link v-for="(item, idx) in mainNavItems" :key="idx" 
                                :href="item.href"
                                @click="toggleMobileMenu"
                                class="flex items-center gap-4 px-4 py-4 rounded-xl transition-all duration-300 group relative overflow-hidden"
                                :class="isActive(item.href) 
                                    ? 'bg-gradient-to-r from-[#4cc9f0]/10 to-transparent text-[#0ea5e9] shadow-sm' 
                                    : 'text-slate-600 hover:bg-white hover:shadow-sm'"
                            >
                                <!-- Indicador Activo -->
                                <div v-if="isActive(item.href)" class="absolute left-0 top-1/2 -translate-y-1/2 h-8 w-1 bg-[#4cc9f0] rounded-r-full shadow-[0_0_10px_#4cc9f0]"></div>

                                <!-- Icono -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                     :class="isActive(item.href) ? 'bg-white text-[#0ea5e9]' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-[#2f4b59]'">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(item.icon)" />
                                     </svg>
                                </div>
                                
                                <!-- Texto -->
                                <span class="font-bold text-lg tracking-tight">{{ item.label }}</span>

                                <!-- Flecha -->
                                <svg class="w-5 h-5 ml-auto opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </Link>

                            <!-- Botón Agendar en Móvil -->
                            <div class="pt-6 px-2">
                                <button @click="showAppointmentModal = true; toggleMobileMenu()" class="w-full bg-[#2f4b59] text-white font-bold py-4 rounded-xl shadow-lg shadow-[#2f4b59]/30 active:scale-95 transition-transform flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Agendar Cita Ahora
                                </button>
                            </div>
                        </div>

                        <!-- Footer del Menú (Tech Touch) -->
                        <div class="p-6 border-t border-slate-200 bg-slate-100/50">
                            <div class="flex items-center gap-2 text-xs font-mono text-slate-400">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                SYSTEM ONLINE v2.4
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- ========================================== -->
                <!-- CONTENIDO (SLOT)                           -->
                <!-- ========================================== -->
                <main class="flex-grow pt-20 relative z-10">
                    <slot />
                </main>

                <!-- ========================================== -->
                <!-- FOOTER TECNOLÓGICO                         -->
                <!-- ========================================== -->
                <footer class="bg-[#0f172a] text-white pt-20 pb-10 relative overflow-hidden mt-auto border-t-0">
                    
                    <!-- LUZ DE BORDE (Running Light) -->
                    <div class="absolute top-0 left-0 right-0 h-[4px] w-full bg-[#1e293b] overflow-hidden">
                        <div class="absolute inset-0 w-[30%] bg-gradient-to-r from-transparent via-[#4cc9f0] to-transparent animate-scanline h-full blur-[2px]"></div>
                    </div>

                    <!-- Fondos y Efectos -->
                    <div class="absolute inset-0 pointer-events-none overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-5"></div>
                        <div class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] bg-[#4cc9f0]/10 blur-[120px] rounded-full"></div>
                        <div class="absolute top-[10%] left-[-10%] w-[400px] h-[400px] bg-[#2f4b59]/20 blur-[100px] rounded-full"></div>
                    </div>

                    <div class="max-w-7xl mx-auto px-6 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
                            
                            <!-- Columna 1: Marca -->
                            <div class="md:col-span-4 space-y-6">
                                <div class="flex items-center gap-3">
                                    <div @click="$inertia.visit(route('login'))" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/10 shadow-[0_0_15px_rgba(76,201,240,0.2)]">
                                        <img src="@/../../public/images/orion-logo-blanco.png" alt="" class="w-6 h-6">
                                    </div>
                                    <span class="text-2xl font-bold tracking-tight text-white">
                                        ORION
                                        <!-- <span class="text-[#4cc9f0]">3D</span> -->
                                    </span>
                                </div>
                                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                    Pioneros en la digitalización humana. Fusionamos tecnología de escaneo de vanguardia con arte para crear réplicas físicas eternas.
                                </p>
                                <div class="flex gap-4 pt-2">
                                    <!-- Social Icons Placeholders -->
                                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#4cc9f0] hover:text-[#0f172a] transition-all duration-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                                    </a>
                                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#4cc9f0] hover:text-[#0f172a] transition-all duration-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Columna 2: Enlaces -->
                            <div class="md:col-span-2 space-y-6">
                                <h4 class="text-white font-bold uppercase tracking-wider text-xs border-b border-white/10 pb-2 inline-block">Explorar</h4>
                                <ul class="space-y-3 text-sm text-slate-400">
                                    <li><Link href="/inicio" class="hover:text-[#4cc9f0] transition-colors">Inicio</Link></li>
                                    <li><Link href="/proceso" class="hover:text-[#4cc9f0] transition-colors">Nuestro Proceso</Link></li>
                                    <li><Link href="/servicios" class="hover:text-[#4cc9f0] transition-colors">Paquetes</Link></li>
                                    <li><Link href="/contacto" class="hover:text-[#4cc9f0] transition-colors">Contacto</Link></li>
                                </ul>
                            </div>

                            <!-- Columna 3: Servicios -->
                            <div class="md:col-span-3 space-y-6">
                                <h4 class="text-white font-bold uppercase tracking-wider text-xs border-b border-white/10 pb-2 inline-block">Servicios</h4>
                                <ul class="space-y-3 text-sm text-slate-400">
                                    <li><Link href="/servicios/escaneo-personas" class="hover:text-[#4cc9f0] transition-colors">Escaneo de Personas</Link></li>
                                    <li><Link href="/servicios/impresion" class="hover:text-[#4cc9f0] transition-colors">Impresión Full Color</Link></li>
                                    <li><Link href="/servicios/modelado" class="hover:text-[#4cc9f0] transition-colors">Modelado & Diseño</Link></li>
                                </ul>
                            </div>

                            <!-- Columna 4: Contacto -->
                            <div class="md:col-span-3 space-y-6">
                                <h4 class="text-white font-bold uppercase tracking-wider text-xs border-b border-white/10 pb-2 inline-block">Contacto</h4>
                                <ul class="space-y-4 text-sm text-slate-400">
                                    <li class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-[#4cc9f0] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Av. Vallarta 2440, Guadalajara, JAL, México.</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#4cc9f0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>contacto@ion3d.com</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#4cc9f0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>+52 (33) 1234 5678</span>
                                    </li>
                                </ul>
                            <figure class="mt-4 cursor-pointer">
                                <a class="flex justify-end items-center" href="https://app.dtw.com.mx/" target="_blank">
                                    <p class="text-white text-xl">BY</p>
                                    <img class="w-20 lg:w-[30%]" src="@/../../public/images/DTW_logo_blanco.png" alt="">
                                </a>
                            </figure>
                            </div>
                        </div>

                        <!-- Bottom Footer -->
                        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500 font-mono">
                            <p>&copy; 2026 ORION Studio. Todos los derechos reservados.</p>
                            <div class="flex gap-6">
                                <a href="#" class="hover:text-white transition-colors">Política de privacidad</a>
                                <a href="#" class="hover:text-white transition-colors">Términos de servicio</a>
                            </div>
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

@keyframes scanline {
    0% { left: -30%; }
    100% { left: 100%; }
}
.animate-scanline {
    animation: scanline 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
</style>