<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/inicio'" class="flex space-x-2 w-full items-center group relative shrink-0 transition-all duration-500" >
        <div class="relative size-16 p-1 bg-[#2f4b59] rounded-full flex items-center justify-center text-white overflow-hidden shadow-lg group-hover:scale-110 transition-transform duration-300">
            <!-- Icono -->
            <img src="@/../../public/images/orion-logo-blanco.png" alt="">
            <!-- Brillo interno animado -->
            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
        <div class="flex flex-col leading-none transition-all duration-300">
            <span class="text-2xl font-bold tracking-tight text-[#2f4b59]">
                ORION
                <!-- <span class="text-slate-400">3D</span> -->
            </span>
        </div>
    </Link>
</template>
