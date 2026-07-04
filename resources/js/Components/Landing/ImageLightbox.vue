<script>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

export default {
    name: 'ImageLightbox',
    props: {
        modelValue: { type: Boolean, default: false },
        images: { type: Array, required: true },
        startIndex: { type: Number, default: 0 },
        // Colores de fondo: array de objetos { from, to } o strings CSS
        backgrounds: { type: Array, default: () => [] },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const currentIndex = ref(props.startIndex);
        const direction = ref(1);
        const isVisible = ref(false);

        const total = computed(() => props.images.length);
        const currentImage = computed(() => props.images[currentIndex.value]);

        // Gradiente para la imagen actual
        const bgStyle = computed(() => {
            const bg = props.backgrounds[currentIndex.value];
            if (!bg) return { background: 'radial-gradient(ellipse at center, #1e293b 0%, #0f172a 100%)' };
            return {
                background: `radial-gradient(ellipse at center, ${bg.from} 0%, ${bg.to} 100%)`,
            };
        });

        const open = (index = 0) => {
            currentIndex.value = index;
            direction.value = 1;
            isVisible.value = true;
            emit('update:modelValue', true);
            document.body.style.overflow = 'hidden';
        };

        const close = () => {
            isVisible.value = false;
            emit('update:modelValue', false);
            document.body.style.overflow = '';
        };

        const goNext = () => {
            direction.value = 1;
            currentIndex.value = (currentIndex.value + 1) % total.value;
        };

        const goPrev = () => {
            direction.value = -1;
            currentIndex.value = (currentIndex.value - 1 + total.value) % total.value;
        };

        const onBackdropClick = (e) => {
            if (e.target === e.currentTarget) close();
        };

        const onKey = (e) => {
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') goPrev();
            if (e.key === 'ArrowRight') goNext();
        };

        // Touch swipe
        let tsX = 0, teX = 0;
        const onTouchS = (e) => { tsX = e.touches[0].clientX; };
        const onTouchM = (e) => { teX = e.touches[0].clientX; };
        const onTouchE = () => {
            const dx = tsX - teX;
            if (Math.abs(dx) > 50) dx > 0 ? goNext() : goPrev();
        };

        watch(() => props.modelValue, (val) => {
            if (val) {
                currentIndex.value = props.startIndex;
                open(props.startIndex);
            } else {
                close();
            }
        });

        watch(() => props.startIndex, (val) => {
            if (props.modelValue) currentIndex.value = val;
        });

        onMounted(() => {
            if (props.modelValue) open(props.startIndex);
            window.addEventListener('keydown', onKey);
        });

        onBeforeUnmount(() => {
            window.removeEventListener('keydown', onKey);
        });

        return {
            currentIndex, direction, isVisible, total, currentImage,
            bgStyle, close, goNext, goPrev,
            onBackdropClick, onTouchS, onTouchM, onTouchE,
        };
    },
};
</script>

<template>
    <Teleport to="body">
        <Transition name="lb-fade">
            <div
                v-if="isVisible"
                class="lb-backdrop"
                @click="onBackdropClick"
                @touchstart.passive="onTouchS"
                @touchmove.passive="onTouchM"
                @touchend="onTouchE"
            >
                <!-- Fondo dinámico con gradiente -->
                <div class="lb-bg" :style="bgStyle"></div>

                <!-- Partículas decorativas -->
                <div class="lb-particles" aria-hidden="true">
                    <span class="lb-dot lb-dot--1"></span>
                    <span class="lb-dot lb-dot--2"></span>
                    <span class="lb-dot lb-dot--3"></span>
                    <span class="lb-dot lb-dot--4"></span>
                    <span class="lb-dot lb-dot--5"></span>
                </div>

                <!-- Header -->
                <div class="lb-header">
                    <div class="lb-counter">
                        <span class="lb-counter-curr">{{ String(currentIndex + 1).padStart(2, '0') }}</span>
                        <span class="lb-counter-sep">/</span>
                        <span class="lb-counter-total">{{ String(total).padStart(2, '0') }}</span>
                    </div>
                    <button class="lb-close" @click.stop="close" aria-label="Cerrar">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>

                <!-- Imagen principal -->
                <div class="lb-stage">
                    <Transition :name="direction === 1 ? 'lb-slide-next' : 'lb-slide-prev'" :duration="450">
                        <div :key="currentIndex" class="lb-image-wrap">
                            <img
                                :src="currentImage"
                                :alt="'Imagen ' + (currentIndex + 1)"
                                class="lb-image"
                                draggable="false"
                            />
                        </div>
                    </Transition>
                </div>

                <!-- Flechas -->
                <button v-if="total > 1" class="lb-arrow lb-arrow--l" @click.stop="goPrev" aria-label="Anterior">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                </button>
                <button v-if="total > 1" class="lb-arrow lb-arrow--r" @click.stop="goNext" aria-label="Siguiente">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>

                <!-- Thumbnails inferiores -->
                <div v-if="total > 1" class="lb-thumbs">
                    <button
                        v-for="(img, idx) in images"
                        :key="idx"
                        :class="['lb-thumb', { 'lb-thumb--active': idx === currentIndex }]"
                        @click.stop="direction = idx > currentIndex ? 1 : -1; currentIndex = idx"
                    >
                        <img :src="img" class="lb-thumb-img" draggable="false" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════
   BACKDROP
   ═══════════════════════════════════════════════════════════ */
.lb-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* ── Fondo gradiente ────────────────────────────────── */
.lb-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    transition: background 0.8s ease;
}

/* ── Partículas ─────────────────────────────────────── */
.lb-particles { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
.lb-dot {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
.lb-dot--1 { width: 300px; height: 300px; top: -10%; left: -5%; filter: blur(60px); }
.lb-dot--2 { width: 200px; height: 200px; bottom: -5%; right: -3%; filter: blur(50px); background: rgba(255,255,255,0.04); }
.lb-dot--3 { width: 120px; height: 120px; top: 40%; left: 60%; filter: blur(30px); background: rgba(255,255,255,0.05); }
.lb-dot--4 { width: 80px; height: 80px; top: 20%; left: 25%; filter: blur(20px); background: rgba(255,255,255,0.03); }
.lb-dot--5 { width: 160px; height: 160px; bottom: 20%; left: 40%; filter: blur(40px); background: rgba(255,255,255,0.04); }

/* ═══════════════════════════════════════════════════════════
   HEADER
   ═══════════════════════════════════════════════════════════ */
.lb-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 20;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
}

.lb-counter {
    font-family: 'SF Mono', 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 13px;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.5);
}
.lb-counter-curr { color: #fff; font-weight: 700; font-size: 16px; }
.lb-counter-sep  { margin: 0 2px; }
.lb-counter-total{ font-weight: 500; }

.lb-close {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(8px);
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}
.lb-close:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border-color: rgba(255,255,255,0.3);
}

/* ═══════════════════════════════════════════════════════════
   STAGE
   ═══════════════════════════════════════════════════════════ */
.lb-stage {
    position: relative;
    z-index: 10;
    width: 90vw;
    max-width: 1100px;
    height: 65vh;
    max-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lb-image-wrap {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lb-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    user-select: none;
    -webkit-user-drag: none;
    filter: drop-shadow(0 20px 60px rgba(0,0,0,0.5));
}

/* ── Transiciones ───────────────────────────────────── */
.lb-slide-next-enter-active,
.lb-slide-next-leave-active,
.lb-slide-prev-enter-active,
.lb-slide-prev-leave-active {
    transition: all 450ms cubic-bezier(0.22, 1, 0.36, 1);
}

.lb-slide-next-enter-from { transform: translateX(50px); opacity: 0; filter: blur(8px); }
.lb-slide-next-leave-to   { transform: translateX(-50px); opacity: 0; filter: blur(8px); }

.lb-slide-prev-enter-from { transform: translateX(-50px); opacity: 0; filter: blur(8px); }
.lb-slide-prev-leave-to   { transform: translateX(50px); opacity: 0; filter: blur(8px); }

/* ═══════════════════════════════════════════════════════════
   FLECHAS
   ═══════════════════════════════════════════════════════════ */
.lb-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 20;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(12px);
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}
.lb-arrow--l { left: 24px; }
.lb-arrow--r { right: 24px; }
.lb-arrow:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border-color: rgba(255,255,255,0.3);
    transform: translateY(-50%) scale(1.06);
}
.lb-arrow:active { transform: translateY(-50%) scale(0.94); }

/* ═══════════════════════════════════════════════════════════
   THUMBNAILS
   ═══════════════════════════════════════════════════════════ */
.lb-thumbs {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 20;
    display: flex;
    gap: 10px;
}

.lb-thumb {
    width: 56px;
    height: 40px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    padding: 0;
    background: none;
    transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
    opacity: 0.45;
}
.lb-thumb:hover { opacity: 0.8; }
.lb-thumb--active {
    opacity: 1;
    border-color: rgba(255,255,255,0.8);
    box-shadow: 0 0 20px rgba(255,255,255,0.15);
}

.lb-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    user-select: none;
}

/* ═══════════════════════════════════════════════════════════
   TRANSICIÓN FADE (backdrop)
   ═══════════════════════════════════════════════════════════ */
.lb-fade-enter-active { transition: opacity 0.4s ease; }
.lb-fade-leave-active { transition: opacity 0.35s ease; }
.lb-fade-enter-from,
.lb-fade-leave-to { opacity: 0; }

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 768px) {
    .lb-stage { width: 94vw; height: 55vh; }
    .lb-arrow { width: 40px; height: 40px; }
    .lb-arrow--l { left: 8px; }
    .lb-arrow--r { right: 8px; }
    .lb-thumb { width: 40px; height: 30px; border-radius: 6px; }
    .lb-header { padding: 0.75rem 1rem; }
}
</style>
