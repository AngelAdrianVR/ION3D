<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, Head } from '@inertiajs/vue3'; // Importamos router para la redirección

// --- THREE.JS IMPORTS (Vía ESM para evitar configuración compleja) ---
import * as THREE from 'https://esm.sh/three@0.160.0';
import { GLTFLoader } from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader';

// Emitimos evento cuando la carga termina para que el padre pueda desmontar si quiere
const emit = defineEmits(['finished']);

// --- ESTADO ---
const containerRef = ref(null);
const loadingProgress = ref(0);
const isLoaded = ref(false); // Cuando llega a 100%
const isHidden = ref(false); // Para ocultar el DOM después del fade-out

// Configuración
const MINIMUM_TIME = 3000; // Tiempo mínimo en pantalla (ms) para que se vea la animación
const MODEL_URL = '/human_head.glb'; // Asegúrate que este archivo esté en /public

// Variables Three.js
let renderer, scene, camera, animationId;
let modelGroup;

// --- LÓGICA 3D ---
const initThreeJS = () => {
    if (!containerRef.value) return;

    // 1. Escena (Transparente para ver el gradiente CSS de fondo)
    scene = new THREE.Scene();

    // 2. Cámara
    const w = containerRef.value.clientWidth;
    const h = containerRef.value.clientHeight;
    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 100);
    camera.position.set(0, 0, 4); // Ajustar distancia según tamaño de la cabeza

    // 3. Renderizador
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    
    // Tone mapping para que se vea cinematográfico
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    
    containerRef.value.appendChild(renderer.domElement);

    // 4. Luces (Estilo Cyberpunk/Tech)
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.2); // Luz base tenue
    scene.add(ambientLight);

    // Luz principal (Key Light)
    const keyLight = new THREE.DirectionalLight(0xffffff, 2);
    keyLight.position.set(2, 2, 5);
    scene.add(keyLight);

    // Luz de borde (Rim Light) - Color Cyan de la marca
    const rimLight = new THREE.SpotLight(0x4cc9f0, 5);
    rimLight.position.set(-2, 1, -2);
    rimLight.lookAt(0, 0, 0);
    scene.add(rimLight);

    // 5. Cargar Modelo
    const loader = new GLTFLoader();
    const startTime = Date.now();

    loader.load(
        MODEL_URL,
        (gltf) => {
            const model = gltf.scene;
            
            // --- ACTUALIZACIÓN: ESCALA 0.3 ---
            model.scale.set(0.3, 0.3, 0.3);

            // --- ACTUALIZACIÓN: CENTRADO ---
            // Recalculamos el bounding box con la nueva escala
            const box = new THREE.Box3().setFromObject(model);
            const center = box.getCenter(new THREE.Vector3());
            
            // Movemos el modelo para que su centro visual quede en 0,0,0
            model.position.x += (model.position.x - center.x);
            model.position.y += (model.position.y - center.y);
            model.position.z += (model.position.z - center.z);

            // --- ACTUALIZACIÓN: MALLA (WIREFRAME) ---
            model.traverse((child) => {
               if (child.isMesh) {
                   // Activamos el modo wireframe (malla de líneas)
                   child.material.wireframe = true; 
                   // Color cian para que combine con ION3D
                   child.material.color.set(0x4cc9f0);
                   // Hacemos que el material sea transparente para que se vea tecnológico
                   child.material.transparent = true;
                   child.material.opacity = 0.3;
               }
            });

            scene.add(model);
            modelGroup = model;
        },
        (xhr) => {
            // Lógica de progreso híbrida (Real + Tiempo mínimo simulado)
            if (xhr.lengthComputable) {
                const percentComplete = (xhr.loaded / xhr.total) * 100;
                // No asignamos directamente para controlar la velocidad mínima en el watcher o loop
            }
        },
        (error) => {
            console.error('Error cargando human_head.glb', error);
            // Forzar finalización en caso de error para no bloquear al usuario
            loadingProgress.value = 100;
        }
    );

    // 6. Animación Loop
    const animate = () => {
        animationId = requestAnimationFrame(animate);
        
        // Rotar modelo 360 grados
        if (modelGroup) {
            modelGroup.rotation.y += 0.01; // Velocidad de giro
        }

        renderer.render(scene, camera);
    };
    animate();

    // Resize handler
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

    // Simulación de carga fluida para UX
    // Incrementa el progreso suavemente hasta 100% respetando el tiempo mínimo
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
        emit('finished'); // Avisar al padre
        
        // Esperar a que termine la transición CSS antes de redirigir
        setTimeout(() => {
            isHidden.value = true;
            // --- ACTUALIZACIÓN: REDIRECCIÓN ---
            router.visit('/inicio');
        }, 1000); 
    }, 500); // Pequeña pausa al llegar a 100%
};

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
    cancelAnimationFrame(animationId);
    if (renderer) renderer.dispose();
});
</script>

<template>
<Head title="ION3D cargando" />
    <!-- Contenedor Principal (Fixed para cubrir todo) -->
    <div v-if="!isHidden"
         class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950 text-white transition-all duration-1000 overflow-hidden"
         :class="{ 'opacity-0 pointer-events-none scale-110': isLoaded }">

        <!-- Fondo Grid Tech -->
        <div class="absolute inset-0 z-0 opacity-20"
             style="background-image: linear-gradient(rgba(76, 201, 240, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(76, 201, 240, 0.1) 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <!-- Gradiente Radial Central (Vignette) -->
        <div class="absolute inset-0 z-0 bg-radial-gradient"></div>

        <!-- --- COMPONENTE CENTRAL (ESCÁNER) --- -->
        <div class="relative w-80 h-80 md:w-96 md:h-96 flex items-center justify-center z-10">
            
            <!-- Three.js Canvas Container -->
            <div ref="containerRef" class="absolute inset-0 w-full h-full z-10"></div>

            <!-- Efecto de Haz de Luz de Escáner (Overlay CSS) -->
            <div class="absolute w-full h-1 bg-[#3677d8] shadow-[0_0_20px_#4cc9f0] opacity-100 z-20 animate-scan"></div>
            
            <!-- Elementos HUD (Interfaz) -->
            <!-- Esquinas -->
            <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-[#4cc9f0]"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-[#4cc9f0]"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-[#4cc9f0]"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-[#4cc9f0]"></div>

        </div>

        <!-- --- TEXTO Y BARRA DE PROGRESO --- -->
        <div class="relative z-20 mt-8 text-center w-64">
            <!-- Logo / Nombre -->
            <h1 class="text-3xl font-bold tracking-widest mb-2 font-mono">
                ION<span class="text-[#4cc9f0]">3D</span>
            </h1>
            
            <!-- Porcentaje -->
            <div class="flex justify-between text-xs text-slate-400 font-mono mb-1">
                <span>SYSTEM LOADING</span>
                <span>{{ loadingProgress }}%</span>
            </div>

            <!-- Barra de Progreso -->
            <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-[#4cc9f0] transition-all duration-100 ease-out shadow-[0_0_10px_#4cc9f0]"
                     :style="{ width: `${loadingProgress}%` }">
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Fondo degradado estilo oscuro */
.bg-radial-gradient {
    background: radial-gradient(circle at center, rgba(76, 201, 240, 0.05) 0%, rgba(2, 6, 23, 1) 80%);
}

/* Animación del haz de luz (Scanner Beam) */
@keyframes scan {
    0% { top: 0%; opacity: 0; }
    15% { opacity: 1; }
    85% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}

.animate-scan {
    animation: scan 3s ease-in-out infinite;
}
</style>