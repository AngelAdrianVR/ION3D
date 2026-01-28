<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, Head } from '@inertiajs/vue3';

// --- THREE.JS IMPORTS ---
import * as THREE from 'https://esm.sh/three@0.160.0';
import { GLTFLoader } from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader';

const emit = defineEmits(['finished']);

// --- ESTADO ---
const containerRef = ref(null);
const loadingProgress = ref(0);
const isLoaded = ref(false);
const isHidden = ref(false);

// Configuración
const MINIMUM_TIME = 3000;
const MODEL_URL = '/human_head.glb';

// Variables Three.js
let renderer, scene, camera, animationId;
let modelGroup;

// --- LÓGICA 3D ---
const initThreeJS = () => {
    if (!containerRef.value) return;

    // 1. Escena
    scene = new THREE.Scene();
    // No seteamos color de fondo a la escena para que sea transparente y se vea el CSS

    // 2. Cámara
    const w = containerRef.value.clientWidth;
    const h = containerRef.value.clientHeight;
    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 100);
    camera.position.set(0, 0, 4);

    // 3. Renderizador
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    
    containerRef.value.appendChild(renderer.domElement);

    // 4. Luces (Ajustadas para fondo claro)
    // Luz ambiental más fuerte para que no se vea negro el objeto sólido
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.8); 
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 1.5);
    keyLight.position.set(2, 2, 5);
    scene.add(keyLight);

    // Luz de borde azul suave
    const rimLight = new THREE.SpotLight(0x3b82f6, 3); // Azul estándar de Tailwind (blue-500)
    rimLight.position.set(-2, 1, -2);
    rimLight.lookAt(0, 0, 0);
    scene.add(rimLight);

    // 5. Cargar Modelo
    const loader = new GLTFLoader();

    loader.load(
        MODEL_URL,
        (gltf) => {
            const model = gltf.scene;
            
            // --- CAMBIO: ESCALA MÁS PEQUEÑA ---
            model.scale.set(0.25, 0.25, 0.25);

            // Centrado
            const box = new THREE.Box3().setFromObject(model);
            const center = box.getCenter(new THREE.Vector3());
            model.position.x += (model.position.x - center.x);
            model.position.y += (model.position.y - center.y);
            model.position.z += (model.position.z - center.z);

            // --- MATERIALES / TEXTURA ---
            model.traverse((child) => {
               if (child.isMesh) {
                   // Para que se vea sólido y reaccione a la luz correctamente:
                   child.material.needsUpdate = true;
                   
                   // OPCIONAL: Si el modelo original es muy oscuro, puedes forzar un color base claro:
                   // child.material.color.set(0xe2e8f0); // Gris claro
                   
                   // --- MODO MALLA (WIREFRAME) ---
                   // Descomenta el bloque de abajo para activar el modo "holograma/malla"
                   /* child.material.wireframe = true; 
                   child.material.color.set(0x3b82f6); // Azul
                   child.material.transparent = true;
                   child.material.opacity = 0.4;
                   */
               }
            });

            scene.add(model);
            modelGroup = model;
        },
        (xhr) => {
            if (xhr.lengthComputable) {
                // Cálculo de carga real
            }
        },
        (error) => {
            console.error('Error cargando modelo', error);
            loadingProgress.value = 100;
        }
    );

    // 6. Animación Loop
    const animate = () => {
        animationId = requestAnimationFrame(animate);
        if (modelGroup) {
            modelGroup.rotation.y += 0.005; // Rotación un poco más lenta y elegante
        }
        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', handleResize);
};

const handleResize = () => {
    if (!containerRef.value || !camera || !renderer) return;
    const w = containerRef.value.clientWidth;
    const h = containerRef.value.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
};

// --- CICLO DE VIDA ---
onMounted(() => {
    initThreeJS();

    const startTime = performance.now();
    const duration = MINIMUM_TIME; 

    const progressInterval = setInterval(() => {
        const elapsed = performance.now() - startTime;
        const p = Math.min((elapsed / duration) * 100, 100);
        
        loadingProgress.value = Math.floor(p);

        if (p >= 100) {
            clearInterval(progressInterval);
            finishLoading();
        }
    }, 20);
});

const finishLoading = () => {
    isLoaded.value = true;
    setTimeout(() => {
        emit('finished');
        setTimeout(() => {
            isHidden.value = true;
            router.visit('/inicio');
        }, 1000); 
    }, 500); 
};

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
    cancelAnimationFrame(animationId);
    if (renderer) renderer.dispose();
});
</script>

<template>
<Head title="Cargando..." />
    <!-- 
        Fondo cambiado a 'bg-slate-50' (blanco/gris muy claro) 
        Texto cambiado a 'text-slate-800' (oscuro)
    -->
    <div v-if="!isHidden"
         class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 text-slate-800 transition-all duration-1000 overflow-hidden"
         :class="{ 'opacity-0 pointer-events-none scale-105': isLoaded }">

        <!-- Fondo Grid Tech (Azul muy tenue sobre blanco) -->
        <div class="absolute inset-0 z-0 opacity-40"
             style="background-image: linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Gradiente Radial Central (Blanco brillante en el centro, gris suave en bordes) -->
        <div class="absolute inset-0 z-0 bg-radial-gradient-light"></div>

        <!-- --- COMPONENTE CENTRAL --- -->
        <!-- Reduje el tamaño del contenedor del canvas para que el objeto se vea más contenido -->
        <div class="relative w-64 h-64 md:w-80 md:h-80 flex items-center justify-center z-10">
            
            <div ref="containerRef" class="absolute inset-0 w-full h-full z-10"></div>

            <!-- Escáner más sutil (azul transparente) -->
            <div class="absolute w-full h-[2px] bg-blue-400 shadow-[0_0_15px_#60a5fa] opacity-0 z-20 animate-scan-subtle"></div>
            
            <!-- Esquinas HUD más sutiles y de color gris azulado -->
            <div class="absolute top-0 left-0 w-6 h-6 border-t border-l border-slate-300"></div>
            <div class="absolute top-0 right-0 w-6 h-6 border-t border-r border-slate-300"></div>
            <div class="absolute bottom-0 left-0 w-6 h-6 border-b border-l border-slate-300"></div>
            <div class="absolute bottom-0 right-0 w-6 h-6 border-b border-r border-slate-300"></div>
        </div>

        <!-- --- TEXTO (Sin barra de progreso) --- -->
        <div class="relative z-20 mt-4 text-center">
            <h1 class="text-2xl font-bold tracking-widest mb-1 font-sans text-slate-900">
                ION<span class="text-blue-500">3D</span>
            </h1>
            
            <!-- Texto simple pulsante -->
            <p class="text-sm text-blue-400 font-medium tracking-wide animate-pulse uppercase">
                Cargando...
            </p>
        </div>

    </div>
</template>

<style scoped>
/* Nuevo gradiente para modo claro: Blanco al centro, gris azulado muy tenue afuera */
.bg-radial-gradient-light {
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0) 0%, rgba(241, 245, 249, 1) 90%);
}

/* Animación de escaneo más suave para el tema claro */
@keyframes scan-subtle {
    0% { top: 0%; opacity: 0; }
    15% { opacity: 0.5; }
    85% { opacity: 0.5; }
    100% { top: 100%; opacity: 0; }
}

.animate-scan-subtle {
    animation: scan-subtle 4s ease-in-out infinite;
}
</style>