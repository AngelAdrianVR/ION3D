<script>
export default {
    data() {
        return {
            isVisible: false,
            scrollProgress: 0
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
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            
            // Calcular porcentaje
            this.scrollProgress = (scrollTop / docHeight) * 100;
            this.isVisible = scrollTop > 300;
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}
</script>

<template>
    <button 
        @click="scrollToTop"
        class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center z-40 transition-all duration-300 group hover:-translate-y-1"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10 pointer-events-none'"
    >
        <!-- Progreso Circular -->
        <svg class="absolute inset-0 -rotate-90 w-full h-full p-1" viewBox="0 0 36 36">
            <path class="text-slate-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
            <path class="text-[#2f4b59]" :stroke-dasharray="`${scrollProgress}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
        </svg>

        <!-- Flecha -->
        <svg class="w-5 h-5 text-[#2f4b59] relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</template>