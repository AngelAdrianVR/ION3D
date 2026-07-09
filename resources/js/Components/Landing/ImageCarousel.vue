<script>
import { ref, computed, onMounted, onBeforeUnmount, reactive } from 'vue';
import ImageLightbox from './ImageLightbox.vue';

export default {
    name: 'ImageCarousel',
    components: { ImageLightbox },
    props: {
        images: { type: Array, required: true, validator: (v) => v.length > 0 },
        autoPlayInterval: { type: Number, default: 5000 },
        transitionDuration: { type: Number, default: 700 },
        lightboxBackgrounds: { type: Array, default: () => [] },
    },
    setup(props, { emit }) {
        const currentIndex = ref(0);
        const direction = ref(1);
        const progress = ref(0);
        const isPaused = ref(false);
        const isTransitioning = ref(false);
        const rootRef = ref(null);

        // ── Parallax / Tilt ──────────────────────────────────
        const tilt = reactive({ x: 0, y: 0 });
        const glowPos = reactive({ x: 50, y: 50 });

        const onMouseMove = (e) => {
            if (!rootRef.value) return;
            const rect = rootRef.value.getBoundingClientRect();
            const cx = (e.clientX - rect.left) / rect.width;   // 0…1
            const cy = (e.clientY - rect.top) / rect.height;   // 0…1
            tilt.x = (cy - 0.5) * 12;  // ±6deg
            tilt.y = (cx - 0.5) * -12;
            glowPos.x = cx * 100;
            glowPos.y = cy * 100;
        };

        // ── Lightbox ────────────────────────────────────────
        const lightboxOpen = ref(false);
        const lightboxStartIndex = ref(0);

        const openLightbox = () => {
            lightboxStartIndex.value = currentIndex.value;
            lightboxOpen.value = true;
        };

        const onStageClick = (e) => {
            if (e.target.closest('button')) return;
            openLightbox();
        };

        const onMouseLeave = () => {
            tilt.x = 0;
            tilt.y = 0;
            glowPos.x = 50;
            glowPos.y = 50;
        };

        // ── Navegación ───────────────────────────────────────
        let autoPlayTimer = null;
        let progressRAF = null;
        let progressStart = null;

        const total = computed(() => props.images.length);
        const currentImage = computed(() => props.images[currentIndex.value]);
        const prevImage = computed(() => props.images[(currentIndex.value - 1 + total.value) % total.value]);
        const nextImage = computed(() => props.images[(currentIndex.value + 1) % total.value]);

        const goTo = (index) => {
            if (isTransitioning.value || index === currentIndex.value) return;
            direction.value = index > currentIndex.value ? 1 : -1;
            triggerTransition(index);
        };

        const goNext = () => {
            direction.value = 1;
            triggerTransition((currentIndex.value + 1) % total.value);
        };

        const goPrev = () => {
            direction.value = -1;
            triggerTransition((currentIndex.value - 1 + total.value) % total.value);
        };

        const triggerTransition = (newIndex) => {
            isTransitioning.value = true;
            resetProgress();
            setTimeout(() => {
                currentIndex.value = newIndex;
                setTimeout(() => {
                    isTransitioning.value = false;
                }, 50);
            }, props.transitionDuration * 0.35);
        };

        // ── Progress ─────────────────────────────────────────
        const resetProgress = () => {
            progress.value = 0;
            progressStart = null;
            if (progressRAF) cancelAnimationFrame(progressRAF);
            if (props.autoPlayInterval > 0) startProgressRAF();
        };

        const startProgressRAF = () => {
            progressStart = performance.now();
            const tick = (ts) => {
                if (!progressStart) progressStart = ts;
                const elapsed = ts - progressStart;
                progress.value = Math.min((elapsed / props.autoPlayInterval) * 100, 100);
                if (progress.value >= 100) {
                    progress.value = 0;
                    progressStart = ts;
                    goNext();
                }
                progressRAF = requestAnimationFrame(tick);
            };
            progressRAF = requestAnimationFrame(tick);
        };

        const startAutoPlay = () => {
            stopAutoPlay();
            if (props.autoPlayInterval > 0 && total.value > 1) {
                autoPlayTimer = setInterval(() => {
                    if (!isPaused.value) goNext();
                }, props.autoPlayInterval);
                startProgressRAF();
            }
        };

        const stopAutoPlay = () => {
            clearInterval(autoPlayTimer);
            if (progressRAF) cancelAnimationFrame(progressRAF);
        };

        // ── Touch ────────────────────────────────────────────
        let tsX = 0, tsY = 0, teX = 0, teY = 0;
        let touchMoved = false;
        const onTouchS = (e) => {
            tsX = e.touches[0].clientX;
            tsY = e.touches[0].clientY;
            teX = tsX;
            teY = tsY;
            touchMoved = false;
        };
        const onTouchM = (e) => {
            teX = e.touches[0].clientX;
            teY = e.touches[0].clientY;
            if (Math.abs(teX - tsX) > 8 || Math.abs(teY - tsY) > 8) touchMoved = true;
        };
        const onTouchE = () => {
            const dx = tsX - teX;
            const dy = Math.abs(tsY - teY);
            if (touchMoved && Math.abs(dx) > 30 && Math.abs(dx) > dy) {
                // Swipe: navegar
                dx > 0 ? goNext() : goPrev();
                isPaused.value = false;
            } else if (!touchMoved) {
                // Tap: toggle pausa
                isPaused.value = !isPaused.value;
            }
        };

        const onKey = (e) => {
            if (e.key === 'ArrowLeft') { e.preventDefault(); goPrev(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); goNext(); }
        };

        onMounted(() => {
            startAutoPlay();
            window.addEventListener('keydown', onKey);
            emit('loaded');
        });
        onBeforeUnmount(() => {
            stopAutoPlay();
            window.removeEventListener('keydown', onKey);
        });

        return {
            currentIndex, direction, progress, isPaused, isTransitioning,
            total, currentImage, prevImage, nextImage,
            tilt, glowPos, rootRef,
            lightboxOpen, lightboxStartIndex,
            goTo, goNext, goPrev,
            onStageClick, openLightbox,
            onMouseMove, onMouseLeave,
            onTouchS, onTouchM, onTouchE,
            pause: () => { isPaused.value = true; },
            resume: () => { isPaused.value = false; },
        };
    },
};
</script>

<template>
    <div
        ref="rootRef"
        class="cr-root"
        @mouseenter="pause"
        @mouseleave="resume; onMouseLeave()"
        @mousemove="onMouseMove"
        @touchstart.passive="onTouchS"
        @touchmove.passive="onTouchM"
        @touchend="onTouchE"
        tabindex="0"
    >
        <!-- ═══ CAPA DE PROFUNDIDAD: miniaturas de fondo ═══ -->
        <div class="cr-depth-layer" aria-hidden="true">
            <div class="cr-depth-card cr-depth-card--prev" :class="{ 'cr-depth-card--swapping': isTransitioning }">
                <img :src="prevImage" class="cr-depth-img" draggable="false" />
            </div>
            <div class="cr-depth-card cr-depth-card--next" :class="{ 'cr-depth-card--swapping': isTransitioning }">
                <img :src="nextImage" class="cr-depth-img" draggable="false" />
            </div>
        </div>

        <!-- ═══ CARTA PRINCIPAL ═══ -->
        <div
            class="cr-main-stage"
            :style="{
                transform: `perspective(900px) rotateX(${tilt.x}deg) rotateY(${tilt.y}deg)`,
                transition: isTransitioning
                    ? `transform ${transitionDuration * 0.7}ms cubic-bezier(0.22, 1, 0.36, 1)`
                    : 'transform 0.15s ease-out',
            }"
            @click="onStageClick"
        >
            <!-- Brillo que sigue al mouse -->
            <div
                class="cr-glow"
                :style="{
                    background: `radial-gradient(circle at ${glowPos.x}% ${glowPos.y}%, rgba(255,255,255,0.35) 0%, transparent 60%)`,
                }"
            ></div>

            <!-- Marco geométrico decorativo -->
            <div class="cr-frame">
                <span class="cr-corner cr-corner--tl"></span>
                <span class="cr-corner cr-corner--tr"></span>
                <span class="cr-corner cr-corner--bl"></span>
                <span class="cr-corner cr-corner--br"></span>
            </div>

            <!-- Imagen con transición tipo "card-exchange" -->
            <div class="cr-card-slot">
                <Transition name="card-pop" :duration="transitionDuration">
                    <div :key="currentIndex" class="cr-card">
                        <img
                            :src="currentImage"
                            :alt="'Proyecto ' + (currentIndex + 1)"
                            class="cr-image"
                            draggable="false"
                        />
                    </div>
                </Transition>
            </div>

            <!-- Overlay de "escaneo" -->
            <div class="cr-scan-overlay" :class="{ 'cr-scan--active': isTransitioning }"></div>
        </div>

        <!-- ═══ PARTÍCULAS GEOMÉTRICAS DECORATIVAS ═══ -->
        <div class="cr-particles" aria-hidden="true">
            <span class="cr-particle cr-particle--1" :class="{ 'cr-particle--burst': isTransitioning }"></span>
            <span class="cr-particle cr-particle--2" :class="{ 'cr-particle--burst': isTransitioning }"></span>
            <span class="cr-particle cr-particle--3" :class="{ 'cr-particle--burst': isTransitioning }"></span>
            <span class="cr-particle cr-particle--4" :class="{ 'cr-particle--burst': isTransitioning }"></span>
            <span class="cr-particle cr-particle--5" :class="{ 'cr-particle--burst': isTransitioning }"></span>
            <span class="cr-particle cr-particle--6" :class="{ 'cr-particle--burst': isTransitioning }"></span>
        </div>

        <!-- ═══ FLECHAS ═══ -->
        <button v-if="total > 1" class="cr-arrow cr-arrow--l" @click.stop="goPrev" aria-label="Anterior">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
        </button>
        <button v-if="total > 1" class="cr-arrow cr-arrow--r" @click.stop="goNext" aria-label="Siguiente">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6" />
            </svg>
        </button>

        <!-- ═══ FOOTER: Nodos + Barra ═══ -->
        <div v-if="total > 1" class="cr-footer">
            <!-- Barra "láser" -->
            <div class="cr-laser-track">
                <div class="cr-laser-fill" :style="{ width: progress + '%' }">
                    <span class="cr-laser-head"></span>
                </div>
            </div>

            <!-- Indicadores tipo nodo -->
            <div class="cr-nodes">
                <button
                    v-for="(img, idx) in images"
                    :key="idx"
                    :class="['cr-node', { 'cr-node--on': idx === currentIndex }]"
                    @click="goTo(idx)"
                    :aria-label="'Ir a imagen ' + (idx + 1)"
                >
                    <span class="cr-node-dot"></span>
                    <span v-if="idx === currentIndex" class="cr-node-ring"></span>
                </button>
            </div>

            <!-- Contador -->
            <div class="cr-counter">
                <span class="cr-counter-curr">{{ String(currentIndex + 1).padStart(2, '0') }}</span>
                <span class="cr-counter-sep">/</span>
                <span class="cr-counter-total">{{ String(total).padStart(2, '0') }}</span>
            </div>
        </div>


        <!-- ═══ DESTELLO DE TRANSICIÓN ═══ -->
        <div class="cr-flash" :class="{ 'cr-flash--fire': isTransitioning }"></div>

        <!-- ═══ LIGHTBOX FULLSCREEN ═══ -->
        <ImageLightbox
            v-model="lightboxOpen"
            :images="images"
            :start-index="lightboxStartIndex"
            :backgrounds="lightboxBackgrounds"
        />
    </div>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════
   ROOT
   ═══════════════════════════════════════════════════════════ */
.cr-root {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: none;
    border-radius: inherit;
    cursor: pointer;
    background: radial-gradient(ellipse at center, rgba(248,250,252,0.6) 0%, rgba(241,245,249,0.3) 100%);
}

/* ═══════════════════════════════════════════════════════════
   CAPA DE PROFUNDIDAD (mini cards detrás)
   ═══════════════════════════════════════════════════════════ */
.cr-depth-layer {
    position: absolute;
    inset: 10% 8%;
    z-index: 1;
    pointer-events: none;
    perspective: 600px;
}

.cr-depth-card {
    position: absolute;
    width: 42%;
    height: 55%;
    border-radius: 1.25rem;
    overflow: hidden;
    opacity: 0.25;
    filter: blur(3px) brightness(0.9);
    transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    box-shadow: 0 8px 32px rgba(0,0,0,0.06);
}

.cr-depth-card--prev {
    top: 8%;
    left: 6%;
    transform: rotate(-4deg) scale(0.9);
}
.cr-depth-card--next {
    bottom: 8%;
    right: 6%;
    transform: rotate(4deg) scale(0.9);
}

.cr-depth-card--swapping.cr-depth-card--prev {
    transform: rotate(-8deg) scale(0.85) translateX(-15%);
    opacity: 0.12;
}
.cr-depth-card--swapping.cr-depth-card--next {
    transform: rotate(8deg) scale(0.85) translateX(15%);
    opacity: 0.35;
}

.cr-depth-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    user-select: none;
}

/* ═══════════════════════════════════════════════════════════
   STAGE PRINCIPAL
   ═══════════════════════════════════════════════════════════ */
.cr-main-stage {
    position: absolute;
    inset: 3%;
    z-index: 10;
    border-radius: 2rem;
    overflow: hidden;
    box-shadow:
        0 4px 8px rgba(0,0,0,0.04),
        0 16px 40px rgba(0,0,0,0.07),
        0 32px 80px rgba(0,0,0,0.05);
    background: #fff;
    will-change: transform;
    transform-style: preserve-3d;
}

/* ── Brillo interactivo ─────────────────────────────── */
.cr-glow {
    position: absolute;
    inset: 0;
    z-index: 5;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.4s;
    mix-blend-mode: soft-light;
}
.cr-root:hover .cr-glow { opacity: 1; }

/* ── Marco geométrico ───────────────────────────────── */
.cr-frame { position: absolute; inset: 12px; z-index: 8; pointer-events: none; }
.cr-corner {
    position: absolute;
    width: 22px;
    height: 22px;
    border-color: rgba(148,163,184,0.25);
    border-style: solid;
    transition: all 0.5s;
}
.cr-root:hover .cr-corner { border-color: rgba(100,116,139,0.5); }
.cr-corner--tl { top: 0; left: 0;   border-width: 2px 0 0 2px; border-radius: 6px 0 0 0; }
.cr-corner--tr { top: 0; right: 0;  border-width: 2px 2px 0 0; border-radius: 0 6px 0 0; }
.cr-corner--bl { bottom: 0; left: 0;  border-width: 0 0 2px 2px; border-radius: 0 0 0 6px; }
.cr-corner--br { bottom: 0; right: 0; border-width: 0 2px 2px 0; border-radius: 0 0 6px 0; }

/* ── Slot de la carta ───────────────────────────────── */
.cr-card-slot {
    position: absolute;
    inset: 0;
    z-index: 2;
}

.cr-card {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}

.cr-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    user-select: none;
    -webkit-user-drag: none;
}

/* ── Transición "card-pop" ──────────────────────────── */
.card-pop-enter-active {
    transition: all v-bind('transitionDuration + "ms"') cubic-bezier(0.22, 1, 0.36, 1);
}
.card-pop-leave-active {
    transition: all v-bind('transitionDuration * 0.6 + "ms"') cubic-bezier(0.55, 0, 1, 0.45);
}

.card-pop-enter-from {
    transform: scale(0.88);
    opacity: 0;
    filter: blur(6px) brightness(1.4);
}
.card-pop-leave-to {
    transform: scale(1.08);
    opacity: 0;
    filter: blur(3px) brightness(0.6);
}

/* ── Overlay de escaneo ─────────────────────────────── */
.cr-scan-overlay {
    position: absolute;
    inset: 0;
    z-index: 6;
    pointer-events: none;
    background: linear-gradient(
        to bottom,
        transparent 40%,
        rgba(59,130,246,0.15) 48%,
        rgba(59,130,246,0.25) 50%,
        rgba(59,130,246,0.15) 52%,
        transparent 60%
    );
    transform: translateY(-100%);
    opacity: 0;
    transition: opacity 0.3s;
}
.cr-scan--active {
    animation: scan-sweep 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes scan-sweep {
    0%   { transform: translateY(-100%); opacity: 0; }
    15%  { opacity: 1; }
    85%  { opacity: 1; }
    100% { transform: translateY(100%); opacity: 0; }
}

/* ═══════════════════════════════════════════════════════════
   PARTÍCULAS GEOMÉTRICAS
   ═══════════════════════════════════════════════════════════ */
.cr-particles { position: absolute; inset: 0; z-index: 4; pointer-events: none; }

.cr-particle {
    position: absolute;
    border-radius: 3px;
    background: rgba(59,130,246,0.2);
    transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.cr-particle--1 { width: 10px; height: 10px; top: 14%; left: 12%; transform: rotate(45deg); }
.cr-particle--2 { width: 6px;  height: 6px;  top: 20%; right: 15%; border-radius: 50%; background: rgba(99,102,241,0.25); }
.cr-particle--3 { width: 8px;  height: 8px;  bottom: 18%; left: 18%; transform: rotate(15deg); background: rgba(14,165,233,0.2); }
.cr-particle--4 { width: 5px;  height: 5px;  bottom: 25%; right: 12%; border-radius: 50%; background: rgba(168,85,247,0.25); }
.cr-particle--5 { width: 12px; height: 4px;  top: 40%; left: 8%; border-radius: 2px; background: rgba(59,130,246,0.15); }
.cr-particle--6 { width: 4px;  height: 12px; top: 35%; right: 8%; border-radius: 2px; background: rgba(99,102,241,0.18); }

.cr-particle--burst.cr-particle--1 { transform: rotate(45deg) translate(20px, -15px) scale(1.6); opacity: 0.6; }
.cr-particle--burst.cr-particle--2 { transform: translate(-18px, 12px) scale(1.8); opacity: 0.7; }
.cr-particle--burst.cr-particle--3 { transform: rotate(15deg) translate(-14px, -18px) scale(1.5); opacity: 0.55; }
.cr-particle--burst.cr-particle--4 { transform: translate(16px, 10px) scale(2); opacity: 0.65; }
.cr-particle--burst.cr-particle--5 { transform: translate(-22px, -8px) scaleX(1.8); opacity: 0.5; }
.cr-particle--burst.cr-particle--6 { transform: translate(14px, -20px) scaleY(2); opacity: 0.6; }

/* ═══════════════════════════════════════════════════════════
   FLECHAS
   ═══════════════════════════════════════════════════════════ */
.cr-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 30;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(226,232,240,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    opacity: 0;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.cr-arrow--l { left: 10px; }
.cr-arrow--r { right: 10px; }

.cr-root:hover .cr-arrow { opacity: 1; }
.cr-arrow:hover {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
    transform: translateY(-50%) scale(1.08);
}
.cr-arrow:active { transform: translateY(-50%) scale(0.94); }

/* ═══════════════════════════════════════════════════════════
   FOOTER
   ═══════════════════════════════════════════════════════════ */
.cr-footer {
    position: absolute;
    bottom: 14px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 30;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

/* ── Barra láser ────────────────────────────────────── */
.cr-laser-track {
    width: 140px;
    height: 2.5px;
    background: rgba(203,213,225,0.35);
    border-radius: 999px;
    overflow: hidden;
    backdrop-filter: blur(4px);
}
.cr-laser-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #6366f1);
    border-radius: 999px;
    position: relative;
    transition: width 0.1s linear;
}
.cr-laser-head {
    position: absolute;
    right: -2px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 8px 2px rgba(99,102,241,0.6);
}

/* ── Nodos ──────────────────────────────────────────── */
.cr-nodes {
    display: flex;
    align-items: center;
    gap: 4px;
}

.cr-node {
    position: relative;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.cr-node-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #94a3b8;
    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}

.cr-node:hover .cr-node-dot { background: #64748b; transform: scale(1.4); }
.cr-node--on .cr-node-dot {
    background: #3b82f6;
    width: 7px;
    height: 7px;
    box-shadow: 0 0 10px rgba(59,130,246,0.5);
}

.cr-node-ring {
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    border: 1.5px solid rgba(59,130,246,0.35);
    animation: node-pulse 2s ease-out infinite;
}

@keyframes node-pulse {
    0%   { transform: scale(0.8); opacity: 1; }
    100% { transform: scale(1.8); opacity: 0; }
}

/* ── Contador ───────────────────────────────────────── */
.cr-counter {
    display: flex;
    align-items: baseline;
    gap: 2px;
    font-family: 'SF Mono', 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 10px;
    letter-spacing: 0.08em;
}
.cr-counter-curr { color: #0f172a; font-weight: 700; font-size: 13px; }
.cr-counter-sep  { color: #94a3b8; margin: 0 1px; }
.cr-counter-total{ color: #94a3b8; font-weight: 500; }

/* ═══════════════════════════════════════════════════════════
   DESTELLO DE TRANSICIÓN
   ═══════════════════════════════════════════════════════════ */
.cr-flash {
    position: absolute;
    inset: 0;
    z-index: 20;
    pointer-events: none;
    background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.5) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.15s;
}
.cr-flash--fire {
    opacity: 0.35;
    transition: opacity 0s;
}

/* ═══════════════════════════════════════════════════════════
   INDICADOR DE PAUSA
   ═══════════════════════════════════════════════════════════ */
.cr-pause-indicator {
    position: absolute;
    top: 12px;
    right: 16px;
    z-index: 35;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(15,23,42,0.7);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
    pointer-events: none;
}
.cr-pause-indicator--visible {
    opacity: 1;
    transform: scale(1);
    animation: pause-breathe 2s ease-in-out infinite;
}

@keyframes pause-breathe {
    0%, 100% { box-shadow: 0 0 0 0 rgba(15,23,42,0.3); }
    50%      { box-shadow: 0 0 0 8px rgba(15,23,42,0); }
}
</style>
