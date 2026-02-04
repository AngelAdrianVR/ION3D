<script setup>
import AppLayoutLanding from '@/Layouts/AppLayoutLanding.vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import ServicesSection from '@/Components/Landing/ServicesSection.vue';
import ProcessSection from '@/Components/Landing/ProcessSection.vue';
import StatsSection from '@/Components/Landing/StatsSection.vue';
import GallerySection from '@/Components/Landing/GallerySection.vue';
import ContactSection from '@/Components/Landing/ContactSection.vue';
import ScrollToTop from '@/Components/Landing/ScrollToTop.vue';
import { Head } from '@inertiajs/vue3';

// Definimos las props que vienen desde el LandingController
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    // Datos dinámicos de BD
    services: {
        type: Array,
        default: () => []
    },
    portfolio: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Inicio - ION3D" />
    
    <AppLayoutLanding>
        <!-- Sección 1: Hero (Impacto Visual) -->
        <HeroSection />

        <!-- Sección 2: Estadísticas (Autoridad) -->
        <StatsSection />

        <!-- Sección 3: Servicios (Información) -->
        <div id="servicios">
            <!-- Pasamos los servicios obtenidos de la BD -->
            <ServicesSection :services="services" />
        </div>

        <!-- Sección 4: Proceso (Educación) -->
        <div id="proceso">
            <ProcessSection />
        </div>

        <!-- Sección 5: Galería (Demostración) -->
        <div id="galeria">
            <!-- Pasamos los items del portafolio obtenidos de la BD -->
            <GallerySection :portfolio-items="portfolio" />
        </div>

        <!-- Sección 6: Contacto (Conversión) -->
        <div id="contacto">
            <ContactSection />
        </div>

        <!-- Utilidad: Botón Scroll -->
        <ScrollToTop />
    </AppLayoutLanding>
</template>