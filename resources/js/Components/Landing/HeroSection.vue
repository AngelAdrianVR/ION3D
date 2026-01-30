<script>
import { NButton } from 'naive-ui';
import { Link } from '@inertiajs/vue3';
import * as THREE from 'https://esm.sh/three@0.160.0';
import { GLTFLoader } from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader';
import { onMounted, onBeforeUnmount, ref } from 'vue';

export default {
    components: { NButton, Link },
    setup() {
        const canvasContainer = ref(null);
        let renderer, scene, camera, animationId;
        let modelGroup;
        let mixer; 
        let platformGrid; 

        const MODEL_URL = '/3d_person.glb';

        const loadModel = () => {
            const loader = new GLTFLoader();

            loader.load(
                MODEL_URL,
                (gltf) => {
                    const model = gltf.scene;
                    
                    // --- AJUSTE DE ESCALA (Reducido ligeramente para la nueva tarjeta) ---
                    const s = 1.9; // Antes 2.1
                    model.scale.set(s, s, s); 

                    // Posición ajustada
                    model.position.y = -1.1; 
                    model.position.x = 0;
                    model.position.z = 0;

                    // Sombras
                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.castShadow = true;
                            child.receiveShadow = true;
                        }
                    });

                    scene.add(model);
                    modelGroup = model;

                    if (gltf.animations && gltf.animations.length) {
                        mixer = new THREE.AnimationMixer(model);
                        const action = mixer.clipAction(gltf.animations[0]); 
                        action.play();
                    }
                },
                undefined,
                (error) => console.error('Error cargando modelo:', error)
            );
        };

        onMounted(() => {
            if (!canvasContainer.value) return;

            scene = new THREE.Scene();

            const width = canvasContainer.value.clientWidth;
            const height = canvasContainer.value.clientHeight;
            
            camera = new THREE.PerspectiveCamera(35, width / height, 0.1, 100);
            camera.position.set(0, 1.2, 8.5); // Ajuste leve de cámara

            // OPTIMIZACIÓN: alpha: true puede ser costoso en algunos navegadores si hay muchos elementos detrás,
            // pero es necesario para la transparencia. Mantenemos el pixelRatio controlado.
            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(width, height);
            
            // OPTIMIZACIÓN: Limitamos el pixel ratio a 1.5 en pantallas retina para ganar FPS
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
            
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.1;
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            
            canvasContainer.value.appendChild(renderer.domElement);

            // Rejilla Polar
            const radius = 3.0;
            const sectors = 16;
            const rings = 8;
            const color1 = 0x94a3b8; 
            const color2 = 0xe2e8f0; 
            platformGrid = new THREE.PolarGridHelper(radius, sectors, rings, 64, color1, color2);
            platformGrid.position.y = -1.8; // Coincidir con los pies
            platformGrid.material.transparent = true;
            platformGrid.material.opacity = 0.4;
            scene.add(platformGrid);

            // Iluminación
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 2);
            mainLight.position.set(5, 10, 7);
            mainLight.castShadow = true;
            // OPTIMIZACIÓN: Reducir tamaño del mapa de sombras si fuera necesario, 
            // pero 1024 suele estar bien.
            mainLight.shadow.mapSize.width = 1024;
            mainLight.shadow.mapSize.height = 1024;
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xe0f2fe, 1.5);
            fillLight.position.set(-5, 0, 2);
            scene.add(fillLight);

            const rimLight = new THREE.SpotLight(0xffffff, 3);
            rimLight.position.set(0, 5, -5);
            rimLight.lookAt(0, 0, 0);
            scene.add(rimLight);

            loadModel();

            const clock = new THREE.Clock();

            const animate = () => {
                animationId = requestAnimationFrame(animate);
                const delta = clock.getDelta();

                if (mixer) mixer.update(delta);

                if (modelGroup) {
                    modelGroup.rotation.y += 0.003; 
                }
                
                if (platformGrid) {
                    platformGrid.rotation.y -= 0.001;
                }

                renderer.render(scene, camera);
            };
            animate();

            const handleResize = () => {
                if (canvasContainer.value) {
                    const w = canvasContainer.value.clientWidth;
                    const h = canvasContainer.value.clientHeight;
                    camera.aspect = w / h;
                    camera.updateProjectionMatrix();
                    renderer.setSize(w, h);
                }
            };
            window.addEventListener('resize', handleResize);
        });

        onBeforeUnmount(() => {
            cancelAnimationFrame(animationId);
            if (renderer) renderer.dispose();
        });

        return { canvasContainer };
    }
}
</script>

<template>
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden py-16 bg-[#fafafa]">
        <!-- Fondo Decorativo (Sin blur excesivo para rendimiento) -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Gradientes CSS simples en lugar de blur filters pesados -->
            <div class="absolute top-0 right-0 w-[50%] h-[50%] bg-gradient-to-bl from-blue-50/80 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-[50%] h-[50%] bg-gradient-to-tr from-cyan-50/80 to-transparent"></div>
            
            <!-- Patrón de puntos -->
            <div class="absolute inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:32px_32px] opacity-30"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 grid lg:grid-cols-2 gap-10 items-center h-full">
            
            <!-- Columna Izquierda: Texto -->
            <div class="space-y-8 text-center lg:text-left order-2 lg:order-1">
                <div class="inline-flex items-center gap-3 pl-1 pr-4 py-1 rounded-full bg-white border border-slate-200 shadow-sm animate-fade-in-up">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-900 text-white text-[10px] font-bold">3D</span>
                    <span class="text-slate-600 text-xs font-bold tracking-widest uppercase">Tecnología de escaneo e Impresión</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-bold leading-[0.95] text-slate-900 tracking-tighter animate-fade-in-up delay-100">
                    Tu mundo, <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-800 via-blue-900 to-cyan-700">
                        Digitalizado.
                    </span>
                </h1>
                
                <p class="text-xl text-slate-500 font-light max-w-lg mx-auto lg:mx-0 leading-relaxed animate-fade-in-up delay-200">
                    Transformamos objetos y personas en activos digitales de alta fidelidad con precisión milimétrica.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up delay-300 pt-4">
                    <n-button @click="$inertia.visit(route('landing.contact'))" color="black" size="large" round class="px-10 h-14 text-lg shadow-lg font-bold tracking-wide hover:scale-105 transition-transform duration-200">
                        Iniciar Proyecto
                    </n-button>
                    <n-button @click="$inertia.visit(route('landing.portfolio'))" size="large" round class="px-10 h-14 text-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-medium hover:text-slate-900 shadow-sm transition-colors duration-200">
                        Ver Galería
                    </n-button>
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-8 pt-6 opacity-60 animate-fade-in-up delay-300">
                     <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                        <span class="text-xs font-mono text-slate-500">SYSTEM READY</span>
                     </div>
                     <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                        <span class="text-xs font-mono text-slate-500">LIDAR ACTIVE</span>
                     </div>
                </div>
            </div>

            <!-- Columna Derecha: Tarjeta 3D Optimizada -->
            <!-- Reducida la altura de 750px a 600px para mejor balance -->
            <div class="relative flex items-center justify-center order-1 lg:order-2 h-[500px] lg:h-[600px] animate-float-slow">
                
                <!-- 
                     OPTIMIZACIÓN DE TARJETA:
                     1. Eliminado 'backdrop-blur-md' (Causa principal de lag).
                     2. Fondo cambiado a 'bg-white/90' para dar la misma sensación de solidez limpia sin el costo de procesamiento.
                     3. Sombra ajustada.
                -->
                <div class="relative w-full max-w-md h-full rounded-[2.5rem] overflow-hidden border border-white/80 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.08)] bg-white/90 group transition-transform duration-300">
                    
                    <!-- Brillo estático superior -->
                    <div class="absolute top-0 left-0 w-full h-1/3 bg-gradient-to-b from-white to-transparent pointer-events-none z-20 opacity-50"></div>

                    <!-- HUD Minimalista -->
                    <div class="absolute inset-0 z-30 pointer-events-none p-6 flex flex-col justify-between opacity-70">
                        <div class="flex justify-between items-start">
                            <div class="border-l-2 border-slate-800 pl-3">
                                <div class="text-[9px] font-bold tracking-[0.2em] text-slate-400 uppercase">Target</div>
                                <div class="text-xs font-bold text-slate-800">Modelo Humano</div>
                            </div>
                            <div class="flex flex-col items-end">
                                <div class="text-[9px] font-bold tracking-[0.2em] text-slate-400 uppercase">Resolución</div>
                                <div class="text-xs font-bold text-slate-800">8K Texture</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-end">
                            <div class="flex gap-2 items-center">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                                </span>
                                <span class="text-[9px] font-mono text-slate-500">SCANNING...</span>
                            </div>
                            <!-- Icono SVG estático para rendimiento -->
                            <svg class="w-6 h-6 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M4 12h16M12 4v16" stroke-linecap="round"/>
                                <circle cx="12" cy="12" r="8"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Haz de luz (Opacidad reducida para no distraer) -->
                    <div class="absolute top-0 w-full h-[1px] bg-cyan-400/50 shadow-[0_0_10px_rgba(34,211,238,0.3)] z-20 animate-scan-elegant pointer-events-none"></div>

                    <!-- Canvas Three.js -->
                    <div ref="canvasContainer" class="w-full h-full absolute inset-0 z-10"></div>
                </div>

                <!-- Elementos decorativos (Simplificados: Sin blur filters) -->
                <div class="absolute -z-10 top-16 right-[-20px] w-24 h-24 border border-slate-200/50 rounded-full animate-spin-very-slow opacity-40"></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
    transform: translateY(30px);
}
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scan-elegant {
    0% { top: 10%; opacity: 0; }
    50% { opacity: 0.5; }
    100% { top: 90%; opacity: 0; }
}
.animate-scan-elegant {
    animation: scan-elegant 5s linear infinite;
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
}

@keyframes spin-very-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-very-slow {
    animation: spin-very-slow 40s linear infinite;
}
</style>