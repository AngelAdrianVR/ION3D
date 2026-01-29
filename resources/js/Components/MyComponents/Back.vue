<template>
  <button
    @click="handleClick"
    type="button"
    class="group flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-full shadow-sm text-gray-500 transition-all duration-200 ease-in-out hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 active:scale-95"
    title="Regresar"
  >
    <!-- Icono de Flecha (Animado al hover) -->
    <svg 
      xmlns="http://www.w3.org/2000/svg" 
      class="w-5 h-5 transition-transform duration-200 group-hover:-translate-x-1" 
      fill="none" 
      viewBox="0 0 24 24" 
      stroke="currentColor" 
      stroke-width="2.5"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
  </button>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
  // Nombre de la ruta específica (ej: 'users.index')
  route: {
    type: String,
    default: null,
  },
  // Parámetros opcionales para la ruta (ej: { user: 1 })
  params: {
    type: Object,
    default: () => ({}),
  }
});

const handleClick = () => {
  if (props.route) {
    // Intenta usar la función route() de Ziggy si está disponible
    try {
      // @ts-ignore
      router.visit(route(props.route, props.params));
    } catch (error) {
      // Fallback si no se usa Ziggy o es una URL directa
      console.warn('Ruta no encontrada o Ziggy no configurado, intentando navegación directa.');
      router.visit(props.route);
    }
  } else {
    // Comportamiento por defecto: Regresar en el historial
    window.history.back();
  }
};
</script>