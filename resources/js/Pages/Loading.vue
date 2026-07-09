<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, Head } from '@inertiajs/vue3';

// --- THREE.JS IMPORTS ---
import * as THREE from 'https://esm.sh/three@0.160.0';

const emit = defineEmits(['finished']);

// --- ESTADO ---
const containerRef = ref(null);
const loadingProgress = ref(0);
const isLoaded = ref(false);
const isHidden = ref(false);

// Configuración
const MINIMUM_TIME = 3000;

// Variables Three.js
let renderer, scene, camera, animationId;
let mainGroup, clock;
let panels = [];
let decorItems = [];

// --- COLORES DE DECORACIÓN MODERNOS (paleta interiorismo) ---
const PANEL_COLORS = [0xA8B5A2, 0xD4C5B9, 0xB8C5D6, 0xC9A89C, 0xA2B5C8, 0xD5CFC0];

// --- POSICIONES EN GRID 2x3 ---
const PANEL_GRID = [
    { x: -1.1, y: 0.55 },
    { x: 0, y: 0.55 },
    { x: 1.1, y: 0.55 },
    { x: -1.1, y: -0.55 },
    { x: 0, y: -0.55 },
    { x: 1.1, y: -0.55 },
];

// --- CREAR UN PANEL DECORATIVO ---
function createPanel(colorHex, pos, index) {
    const group = new THREE.Group();

    // Cuerpo del panel
    const geo = new THREE.BoxGeometry(0.9, 0.55, 0.04);
    const mat = new THREE.MeshStandardMaterial({
        color: colorHex,
        roughness: 0.35,
        metalness: 0.05,
    });
    const body = new THREE.Mesh(geo, mat);
    group.add(body);

    // Marco / borde sutil
    const edgeGeo = new THREE.EdgesGeometry(geo);
    const edgeMat = new THREE.LineBasicMaterial({
        color: 0x999999,
        transparent: true,
        opacity: 0.3,
    });
    const edges = new THREE.LineSegments(edgeGeo, edgeMat);
    group.add(edges);

    group.position.set(pos.x, pos.y, 0);

    return {
        group,
        body,
        targetIndex: index,
        floatOffset: Math.random() * Math.PI * 2,
    };
}

// --- INICIALIZAR THREE.JS ---
const initThreeJS = () => {
    if (!containerRef.value) return;

    clock = new THREE.Clock();

    // 1. Escena
    scene = new THREE.Scene();

    // 2. Cámara
    const w = containerRef.value.clientWidth;
    const h = containerRef.value.clientHeight;
    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 100);
    camera.position.set(0, 0.1, 4.5);

    // 3. Renderizador
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;

    containerRef.value.appendChild(renderer.domElement);

    // 4. Iluminación
    const ambientLight = new THREE.AmbientLight(0xffffff, 1.0);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.5);
    keyLight.position.set(2, 3, 5);
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0xc8dce8, 1.0);
    fillLight.position.set(-2, -1, 2);
    scene.add(fillLight);

    const rimLight = new THREE.SpotLight(0x3b82f6, 2.5);
    rimLight.position.set(-3, 1, -3);
    scene.add(rimLight);

    // 5. Grupo principal
    mainGroup = new THREE.Group();
    scene.add(mainGroup);

    // 6. Suelo — grid decorativo
    const gridHelper = new THREE.GridHelper(4.5, 20, 0xd0d0d0, 0xeaeaea);
    gridHelper.position.y = -1.4;
    mainGroup.add(gridHelper);

    // Plano de suelo semitransparente
    const floorGeo = new THREE.PlaneGeometry(4.5, 4.5);
    const floorMat = new THREE.MeshStandardMaterial({
        color: 0xfafafa,
        roughness: 1,
        metalness: 0,
        transparent: true,
        opacity: 0.5,
        side: THREE.DoubleSide,
    });
    const floor = new THREE.Mesh(floorGeo, floorMat);
    floor.rotation.x = -Math.PI / 2;
    floor.position.y = -1.4;
    mainGroup.add(floor);

    // 7. Crear paneles decorativos
    PANEL_GRID.forEach((pos, i) => {
        const panel = createPanel(PANEL_COLORS[i], pos, i);
        mainGroup.add(panel.group);
        panels.push(panel);
    });

    // 8. Pequeños elementos decorativos orbitantes
    const decorColors = [0xE8C9A0, 0xB8C8D8, 0xC8D8C8, 0xE0D0C0];
    for (let i = 0; i < 4; i++) {
        const sphereGeo = new THREE.SphereGeometry(0.06, 16, 16);
        const sphereMat = new THREE.MeshStandardMaterial({
            color: decorColors[i],
            roughness: 0.3,
            metalness: 0.2,
        });
        const sphere = new THREE.Mesh(sphereGeo, sphereMat);
        sphere.userData = {
            angle: (i / 4) * Math.PI * 2,
            radius: 1.9 + Math.random() * 0.3,
            speed: 0.25 + Math.random() * 0.2,
            yBase: (Math.random() - 0.5) * 0.8,
        };
        mainGroup.add(sphere);
        decorItems.push(sphere);
    }

    // 9. Bucle de animación
    let swapTimer = 0;
    const SWAP_INTERVAL = 2.5;

    const animate = () => {
        animationId = requestAnimationFrame(animate);

        const dt = Math.min(clock.getDelta(), 0.1);
        const time = clock.elapsedTime;

        // Rotación suave de todo el conjunto
        mainGroup.rotation.y += dt * 0.18;

        // --- INTERCAMBIO DE PANELES ---
        swapTimer += dt;
        if (swapTimer >= SWAP_INTERVAL) {
            swapTimer = 0;
            // Elegir dos paneles al azar para intercambiar
            const i = Math.floor(Math.random() * panels.length);
            let j = Math.floor(Math.random() * panels.length);
            while (j === i) j = Math.floor(Math.random() * panels.length);

            const temp = panels[i].targetIndex;
            panels[i].targetIndex = panels[j].targetIndex;
            panels[j].targetIndex = temp;
        }

        // --- ACTUALIZAR POSICIONES DE PANELES (lerp) ---
        const lerpFactor = 0.12;
        panels.forEach(panel => {
            const target = PANEL_GRID[panel.targetIndex];

            panel.group.position.x += (target.x - panel.group.position.x) * lerpFactor;
            panel.group.position.y += (target.y - panel.group.position.y) * lerpFactor;

            // Micro-flotación vertical
            const floatY = Math.sin(time * 1.8 + panel.floatOffset) * 0.025;
            panel.group.position.y += floatY;

            // Sutil balanceo
            const rotZ = Math.sin(time * 1.2 + panel.floatOffset) * 0.04;
            panel.group.rotation.z += (rotZ - panel.group.rotation.z) * 0.1;
        });

        // --- ACTUALIZAR ELEMENTOS DECORATIVOS ---
        decorItems.forEach(item => {
            item.userData.angle += dt * item.userData.speed;
            item.position.x = Math.cos(item.userData.angle) * item.userData.radius;
            item.position.z = Math.sin(item.userData.angle) * item.userData.radius * 0.35 - 0.1;
            item.position.y = item.userData.yBase + Math.sin(time * 0.8 + item.userData.angle) * 0.15;
        });

        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', handleResize);
};

// --- REDIMENSIONAR ---
const handleResize = () => {
    if (!containerRef.value || !camera || !renderer) return;
    const w = containerRef.value.clientWidth;
    const h = containerRef.value.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
};

// --- CICLO DE VIDA VUE ---
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
    panels = [];
    decorItems = [];
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
            <h1 class="text-2xl font-bold tracking-widest mb-1 font-sans text-[#2f4b59]">
                NODO
            </h1>
            
            <!-- Texto simple pulsante -->
            <p class="text-sm text-blue-700 font-medium tracking-wide animate-pulse uppercase">
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