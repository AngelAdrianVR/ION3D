<script>
import { Link } from '@inertiajs/vue3';
import { NConfigProvider, NGlobalStyle, NButton } from 'naive-ui';

export default {
    name: 'AppLayoutLanding',
    components: {
        Link,
        NConfigProvider,
        NGlobalStyle,
        NButton
    },
    data() {
        return {
            isScrolled: false,
            mobileMenuOpen: false,
            activeDropdown: null, // Controla qué dropdown está visible
            // Nuevo color primario: #2f4b59
            themeOverrides: {
                common: {
                    primaryColor: '#2f4b59', 
                    primaryColorHover: '#3e6070',
                    primaryColorPressed: '#1f333d',
                    fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif'
                },
                Button: {
                    borderRadiusMedium: '8px',
                    borderRadiusLarge: '24px', // Más redondeado para el estilo cápsula
                    fontWeight: '600',
                    colorFocusPrimary: '#3e607033',
                    borderFocusPrimary: '1px solid #2f4b59'
                }
            },
            // Opciones de los menús desplegables
            menuOptions: {
                servicios: [
                    { label: 'Escaneo 3D de Personas', href: '/servicios/escaneo-personas', icon: 'scan' },
                    { label: 'Impresión Full Color', href: '/servicios/impresion', icon: 'print' },
                    { label: 'Modelado Digital', href: '/servicios/modelado', icon: 'cube' },
                    { label: 'Eventos y Bodas', href: '/servicios/eventos', icon: 'party' },
                ],
                galeria: [
                    { label: 'Portafolio Comercial', href: '/galeria/comercial', icon: 'briefcase' },
                    { label: 'Arte y Figuras', href: '/galeria/arte', icon: 'art' },
                    { label: 'Casos de Éxito', href: '/galeria/casos', icon: 'star' },
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
            // Un umbral un poco mayor para dar espacio al diseño inicial
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
        }
    }
}
</script>

<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <n-global-style />
        
        <div class="min-h-screen flex flex-col relative bg-slate-50 font-sans text-slate-800 selection:bg-[#2f4b59] selection:text-white overflow-x-hidden">
            
            <!-- Fondo Decorativo Sutil -->
            <div class="fixed inset-0 pointer-events-none z-0 opacity-[0.3]" 
                 style="background-image: radial-gradient(#2f4b59 0.5px, transparent 0.5px); background-size: 24px 24px;">
            </div>

            <!-- ========================================== -->
            <!-- BARRA DE NAVEGACIÓN DINÁMICA (MORPHING)    -->
            <!-- ========================================== -->
            <!-- Contenedor externo que centra todo -->
            <div class="fixed top-0 left-0 right-0 z-50 flex justify-center transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.25,1)]"
                 :class="[isScrolled ? 'pt-4' : 'pt-6']">
                
                <!-- La Barra de Navegación en sí -->
                <nav 
                    :class="[
                        'transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.25,1)] flex items-center',
                        isScrolled 
                            ? 'w-auto bg-white/90 backdrop-blur-xl border border-white/20 shadow-2xl shadow-[#2f4b59]/10 rounded-full px-2 py-2 gap-2' 
                            : 'w-full max-w-7xl px-6 bg-transparent border-transparent gap-8'
                    ]"
                >
                    <!-- 1. LOGO -->
                    <!-- En scroll: se integra a la cápsula. Sin scroll: pegado a la izquierda (mr-auto) -->
                    <Link href="/" 
                        class="flex items-center gap-2 group relative shrink-0 transition-all duration-500"
                        :class="[isScrolled ? 'pl-2' : 'mr-auto']"
                    >
                        <div class="relative w-9 h-9 bg-[#2f4b59] rounded-full flex items-center justify-center text-white overflow-hidden shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <!-- Icono -->
                            <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2 1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                            </svg>
                            <!-- Brillo interno animado -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <!-- Texto del logo: Se oculta en modo 'scrolled' para ahorrar espacio o se deja pequeño -->
                        <div class="flex flex-col leading-none transition-all duration-300" 
                             :class="[isScrolled ? 'w-0 opacity-0 overflow-hidden' : 'w-auto opacity-100']">
                            <span class="text-lg font-bold tracking-tight text-[#2f4b59]">
                                ION<span class="text-slate-400">3D</span>
                            </span>
                        </div>
                    </Link>

                    <!-- 2. MENÚ CENTRAL -->
                    <!-- Siempre visible en desktop, cambia estilo de contenedor en scroll -->
                    <div class="hidden md:flex items-center">
                        <div class="flex items-center" :class="[isScrolled ? 'gap-1' : 'gap-6 bg-white/50 px-6 py-2 rounded-full border border-white/40 backdrop-blur-sm shadow-sm']">
                            
                            <!-- Link Simple: Proceso -->
                            <Link href="/#proceso" class="relative px-4 py-2 text-sm font-medium text-slate-600 hover:text-[#2f4b59] transition-colors rounded-full hover:bg-slate-100/50">
                                Proceso
                            </Link>

                            <!-- Dropdown: Servicios -->
                            <div class="relative group" @mouseenter="setActiveDropdown('servicios')" @mouseleave="clearActiveDropdown">
                                <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium text-slate-600 hover:text-[#2f4b59] transition-colors rounded-full hover:bg-slate-100/50 outline-none">
                                    Servicios
                                    <svg class="w-3 h-3 transition-transform duration-300" :class="activeDropdown === 'servicios' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <!-- Menú Flotante -->
                                <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                                    <div v-show="activeDropdown === 'servicios'" class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-64 bg-white/95 backdrop-blur-xl border border-slate-100 rounded-2xl shadow-xl p-2 z-50 overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-b from-[#2f4b59]/5 to-transparent pointer-events-none"></div>
                                        <Link v-for="(item, idx) in menuOptions.servicios" :key="idx" :href="item.href" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 transition-colors group/item">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-[#2f4b59] flex items-center justify-center group-hover/item:bg-[#2f4b59] group-hover/item:text-white transition-colors">
                                                <!-- Iconos simples simulados -->
                                                <svg v-if="item.icon === 'scan'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v4.25m0 0l3-3.5m-3 3.5l-3-3.5" /></svg>
                                                <svg v-else-if="item.icon === 'print'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" /></svg>
                                            </div>
                                            <span class="text-sm font-medium text-slate-700">{{ item.label }}</span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>

                            <!-- Dropdown: Galería -->
                            <div class="relative group" @mouseenter="setActiveDropdown('galeria')" @mouseleave="clearActiveDropdown">
                                <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium text-slate-600 hover:text-[#2f4b59] transition-colors rounded-full hover:bg-slate-100/50 outline-none">
                                    Galería
                                    <svg class="w-3 h-3 transition-transform duration-300" :class="activeDropdown === 'galeria' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <!-- Menú Flotante -->
                                <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                                    <div v-show="activeDropdown === 'galeria'" class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-56 bg-white/95 backdrop-blur-xl border border-slate-100 rounded-2xl shadow-xl p-2 z-50 overflow-hidden">
                                        <Link v-for="(item, idx) in menuOptions.galeria" :key="idx" :href="item.href" class="block px-4 py-2.5 rounded-xl hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700 hover:text-[#2f4b59]">
                                            {{ item.label }}
                                        </Link>
                                    </div>
                                </transition>
                            </div>
                            
                            <!-- Link Simple: Contacto -->
                             <Link href="/contacto" class="relative px-4 py-2 text-sm font-medium text-slate-600 hover:text-[#2f4b59] transition-colors rounded-full hover:bg-slate-100/50">
                                Contacto
                            </Link>
                        </div>
                    </div>

                    <!-- 3. BOTÓN DE ACCIÓN -->
                    <!-- Scroll: Se une a la derecha. No Scroll: Pegado a la derecha extrema (ml-auto) -->
                    <div class="transition-all duration-500" :class="[isScrolled ? 'pr-1' : 'ml-auto']">
                        <n-button type="primary" :round="true" :size="isScrolled ? 'medium' : 'large'" 
                            class="shadow-lg shadow-[#2f4b59]/20 transition-all duration-300">
                            <template #icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </template>
                            <span v-if="!isScrolled">Agendar Cita</span>
                            <span v-else>Agendar</span>
                        </n-button>
                    </div>

                    <!-- Botón Hamburguesa (Móvil) -->
                    <button @click="toggleMobileMenu" class="md:hidden ml-auto p-2 text-slate-700 hover:text-[#2f4b59] transition-colors bg-white/50 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </nav>
            </div>

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
            <main class="flex-grow pt-32 relative z-10">
                <slot />
            </main>

            <!-- ========================================== -->
            <!-- FOOTER CON ANIMACIÓN "LIGHTS"              -->
            <!-- ========================================== -->
            <footer class="bg-slate-900 text-white pt-20 pb-10 relative overflow-hidden mt-auto">
                
                <!-- Animación de Luces (Borde Superior) -->
                <!-- Una línea de gradiente que se mueve infinitamente -->
                <div class="absolute top-0 left-0 right-0 h-[2px] w-full overflow-hidden">
                    <div class="absolute inset-0 w-[200%] bg-gradient-to-r from-transparent via-[#4cc9f0] to-transparent animate-aurora opacity-70"></div>
                </div>
                
                <!-- Efecto de brillo ambiental detrás -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-[#2f4b59] opacity-20 blur-[100px] pointer-events-none rounded-full"></div>

                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
                        
                        <!-- Columna 1: Marca (4 columnas) -->
                        <div class="md:col-span-4 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                                    <svg class="w-6 h-6 text-[#4cc9f0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                </div>
                                <span class="text-2xl font-bold tracking-tight">ION<span class="text-[#4cc9f0]">3D</span></span>
                            </div>
                            <p class="text-slate-400 leading-relaxed">
                                Redefiniendo los límites entre lo físico y lo digital. Tecnología de escaneo 3D de precisión milimétrica para preservación digital y manufactura aditiva.
                            </p>
                            <div class="flex gap-4">
                                <!-- Social Icons Minimalistas -->
                                <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#2f4b59] hover:border-[#4cc9f0] hover:text-[#4cc9f0] transition-all duration-300 group">
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                                </a>
                                <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-[#2f4b59] hover:border-[#4cc9f0] hover:text-[#4cc9f0] transition-all duration-300 group">
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Columna 2: Links (2 cols) -->
                        <div class="md:col-span-2">
                            <h4 class="text-white font-semibold mb-6">Explorar</h4>
                            <ul class="space-y-3 text-sm text-slate-400">
                                <li><Link href="/" class="hover:text-[#4cc9f0] transition-colors">Inicio</Link></li>
                                <li><Link href="/nosotros" class="hover:text-[#4cc9f0] transition-colors">Estudio</Link></li>
                                <li><Link href="/proceso" class="hover:text-[#4cc9f0] transition-colors">Proceso</Link></li>
                                <li><Link href="/faq" class="hover:text-[#4cc9f0] transition-colors">Preguntas</Link></li>
                            </ul>
                        </div>

                        <!-- Columna 3: Servicios (3 cols) -->
                        <div class="md:col-span-3">
                            <h4 class="text-white font-semibold mb-6">Soluciones</h4>
                            <ul class="space-y-3 text-sm text-slate-400">
                                <li><Link href="/s/escaneo" class="hover:text-[#4cc9f0] transition-colors">Escaneo Biométrico</Link></li>
                                <li><Link href="/s/minime" class="hover:text-[#4cc9f0] transition-colors">Mini-Me (Figuras)</Link></li>
                                <li><Link href="/s/nft" class="hover:text-[#4cc9f0] transition-colors">Activos Digitales / NFT</Link></li>
                                <li><Link href="/s/corporativo" class="hover:text-[#4cc9f0] transition-colors">Eventos Corporativos</Link></li>
                            </ul>
                        </div>

                        <!-- Columna 4: Newsletter (3 cols) -->
                        <div class="md:col-span-3">
                            <h4 class="text-white font-semibold mb-6">Futuro al día</h4>
                            <p class="text-slate-400 text-sm mb-4">Suscríbete y recibe las últimas novedades en tecnología 3D.</p>
                            <div class="relative">
                                <input type="email" placeholder="correo@ejemplo.com" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-[#4cc9f0] focus:ring-1 focus:ring-[#4cc9f0] transition-all">
                                <button class="absolute right-1 top-1 bottom-1 bg-[#2f4b59] hover:bg-[#4cc9f0] text-white px-3 rounded-md transition-colors duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                        <p>© 2026 ION3D Studio. Todos los derechos reservados.</p>
                        <div class="flex gap-6">
                            <Link href="/legal" class="hover:text-white transition-colors">Aviso de Privacidad</Link>
                            <Link href="/legal" class="hover:text-white transition-colors">Términos y Condiciones</Link>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </n-config-provider>
</template>

<style>
/* Animación de Aurora para el Footer */
@keyframes aurora {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0%); }
}
.animate-aurora {
    animation: aurora 5s linear infinite;
}
</style>