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
        const isLoaded = ref(false); 
        let renderer, scene, camera, animationId;
        let modelGroup; // El modelo actual en escena
        let mixer; 
        let platformGrid; 
        let modelInterval; // Variable para el intervalo de cambio

        // --- CONFIGURACIÓN DE MODELOS ---
        // Ahora cada elemento es un objeto con su URL, Escala y Posición individual.
        const modelsList = [
            {
                name: 'Human Body',
                url: '/3d_person.glb',
                scale: 1.9,                     // Tamaño original
                position: { x: 0, y: -1.1, z: 0 } // Posición original
            },
            {
                name: 'Heart',
                url: '/heart.glb',
                scale: 7.5,                     // <--- MODIFICA TAMAÑO AQUÍ (Prueba valores como 0.05, 0.1, 1.0 dependiendo del modelo)
                position: { x: 0, y: 0, z: 0 }  // <--- MODIFICA POSICIÓN AQUÍ { x, y, z }
            },
            {
                name: 'Head',
                url: '/head.glb',
                scale: 0.8,                     // <--- MODIFICA TAMAÑO AQUÍ
                position: { x: 0, y: 1, z: 0 }
            },
            {
                name: 'Sofa',
                url: '/sofa.glb',
                scale: 2.0,                     // <--- MODIFICA TAMAÑO AQUÍ
                position: { x: 0, y: 0, z: 0 }
            }
        ];
        
        let currentModelIndex = 0;
        const CHANGE_INTERVAL_MS = 3000; // 3 segundos por modelo

        // Variables de control de animación
        let currentRotationSpeed = 0.15; 
        const targetRotationSpeed = 0.003; 
        const damping = 0.96; 

        // Función para cargar un modelo específico usando su configuración
        const loadModel = (modelConfig) => {
            const loader = new GLTFLoader();

            // Guardamos la rotación actual para aplicarla al siguiente modelo y que no "salte" visualmente
            const currentRotationY = modelGroup ? modelGroup.rotation.y : Math.PI * 4;

            loader.load(
                modelConfig.url,
                (gltf) => {
                    const newModel = gltf.scene;
                    
                    // 1. APLICAR ESCALA INDIVIDUAL
                    const s = modelConfig.scale;
                    newModel.scale.set(s, s, s); 

                    // 2. APLICAR POSICIÓN INDIVIDUAL
                    newModel.position.set(
                        modelConfig.position.x,
                        modelConfig.position.y,
                        modelConfig.position.z
                    );

                    // Aplicar la rotación acumulada para continuidad visual
                    newModel.rotation.y = currentRotationY;

                    // Procesamiento de materiales (Manteniendo tu estilo actual)
                    newModel.traverse((child) => {
                        if (child.isMesh) {
                            child.castShadow = true;
                            child.receiveShadow = true;
                            if (child.material) {
                                child.material.roughness = 0.4;
                                child.material.metalness = 0.2;
                            }
                        }
                    });

                    // SWAP: Remover anterior y añadir nuevo
                    if (modelGroup) {
                        scene.remove(modelGroup);
                    }
                    
                    scene.add(newModel);
                    modelGroup = newModel;

                    // Manejo de animaciones si el modelo las tiene
                    if (gltf.animations && gltf.animations.length) {
                        mixer = new THREE.AnimationMixer(newModel);
                        const action = mixer.clipAction(gltf.animations[0]); 
                        action.play();
                    } else {
                        mixer = null;
                    }

                    // Primera carga: activar UI
                    if (!isLoaded.value) {
                        setTimeout(() => {
                            isLoaded.value = true;
                        }, 300);
                    }
                },
                undefined,
                (error) => console.error(`Error cargando modelo ${modelConfig.url}:`, error)
            );
        };

        // Función para ciclar al siguiente modelo
        const cycleNextModel = () => {
            currentModelIndex = (currentModelIndex + 1) % modelsList.length;
            // Pasamos el objeto de configuración completo
            loadModel(modelsList[currentModelIndex]);
        };

        onMounted(() => {
            if (!canvasContainer.value) return;

            scene = new THREE.Scene();

            const width = canvasContainer.value.clientWidth;
            const height = canvasContainer.value.clientHeight;
            
            camera = new THREE.PerspectiveCamera(35, width / height, 0.1, 100);
            camera.position.set(0, 1.2, 10); 

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
            
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.2;
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            
            canvasContainer.value.appendChild(renderer.domElement);

            // Rejilla
            platformGrid = new THREE.PolarGridHelper(3.0, 16, 8, 64, 0x94a3b8, 0xf1f5f9);
            platformGrid.position.y = -1.8;
            platformGrid.material.transparent = true;
            platformGrid.material.opacity = 0.3;
            scene.add(platformGrid);

            // Iluminación
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 2.5);
            mainLight.position.set(5, 10, 7);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.width = 1024;
            mainLight.shadow.mapSize.height = 1024;
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xdbeafe, 1.8);
            fillLight.position.set(-5, 2, 2);
            scene.add(fillLight);

            const rimLight = new THREE.SpotLight(0xffffff, 4);
            rimLight.position.set(0, 5, -5);
            scene.add(rimLight);

            // 1. Cargar el primer modelo
            loadModel(modelsList[0]);

            // 2. Iniciar el intervalo de cambio
            modelInterval = setInterval(cycleNextModel, CHANGE_INTERVAL_MS);

            const clock = new THREE.Clock();

            const animate = () => {
                animationId = requestAnimationFrame(animate);
                const delta = clock.getDelta();

                if (mixer) mixer.update(delta);

                if (modelGroup) {
                    // Lógica de rotación (Intro rápida -> Velocidad crucero)
                    if (currentRotationSpeed > targetRotationSpeed) {
                        currentRotationSpeed *= damping;
                        if (camera.position.z > 8.5) {
                            camera.position.z -= 0.03;
                        }
                    } else {
                        currentRotationSpeed = targetRotationSpeed;
                    }
                    
                    // Giramos el grupo actual (sea cual sea el modelo cargado)
                    modelGroup.rotation.y += currentRotationSpeed;
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
            // Limpiar intervalo para evitar memory leaks
            if (modelInterval) clearInterval(modelInterval);
            if (renderer) renderer.dispose();
        });

        return { canvasContainer, isLoaded };
    }
}
</script>

<template>
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden py-16 bg-[#fcfcfc]">
        
        <!-- Fondos Ambientales -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[60%] h-[60%] bg-gradient-to-bl from-blue-50/50 via-transparent to-transparent rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[60%] h-[60%] bg-gradient-to-tr from-slate-100/50 via-transparent to-transparent rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:40px_40px] opacity-20"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 grid lg:grid-cols-2 gap-12 items-center h-full">
            
            <!-- Texto con transiciones suaves controladas por isLoaded -->
            <div class="space-y-10 text-center lg:text-left order-2 lg:order-1 transition-all duration-1000 transform"
                 :class="[isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
                
                <div class="inline-flex items-center gap-3 pl-1 pr-4 py-1.5 rounded-full bg-white border border-slate-100 shadow-sm">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-[10px] font-black tracking-tighter">PRO</span>
                    <span class="text-slate-500 text-[11px] font-bold tracking-[0.2em] uppercase">Vanguardia en Digitalización 3D</span>
                </div>
                
                <h1 class="text-6xl lg:text-8xl font-extrabold leading-[0.9] text-slate-900 tracking-tighter">
                    Precisión <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">
                        Tangible.
                    </span>
                </h1>
                
                <p class="text-xl text-slate-400 font-light max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Convertimos la realidad en datos de alta fidelidad. Escaneo profesional e impresión de vanguardia para proyectos que exigen perfección.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start pt-4">
                    <n-button @click="$inertia.visit(route('landing.contact'))" color="#0f172a" size="large" round class="px-12 h-16 text-lg shadow-xl shadow-blue-500/10 font-bold hover:scale-105 transition-all">
                        Iniciar Escaneo
                    </n-button>
                    <n-button @click="$inertia.visit(route('landing.portfolio'))" size="large" round class="px-12 h-16 text-lg border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-semibold shadow-sm">
                        Explorar Galería
                    </n-button>
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-10 pt-8 border-t border-slate-100 max-w-sm">
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">LATENCIA</div>
                        <div class="text-sm font-mono font-medium text-slate-500">0.02ms</div>
                     </div>
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">TOLERANCIA</div>
                        <div class="text-sm font-mono font-medium text-slate-500">±0.01mm</div>
                     </div>
                     <div class="group">
                        <div class="text-[10px] font-bold text-slate-300 mb-1 group-hover:text-blue-500 transition-colors">ESTADO</div>
                        <div class="text-sm font-mono font-medium text-green-500">ONLINE</div>
                     </div>
                </div>
            </div>

            <!-- Contenedor 3D -->
            <div class="relative flex items-center justify-center order-1 lg:order-2 h-[550px] lg:h-[700px]">
                
                <!-- Tarjeta Principal -->
                <div class="relative w-full max-w-lg h-full rounded-[3rem] overflow-hidden border border-white bg-white/40 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.06)] group transition-all duration-700"
                     :class="[isLoaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95']">
                    
                    <!-- HUD Elements -->
                    <div class="absolute inset-0 z-30 pointer-events-none p-10 flex flex-col justify-between">
                        <div class="flex justify-between items-start opacity-40 group-hover:opacity-100 transition-opacity duration-500">
                            <div class="space-y-1">
                                <div class="w-8 h-[1px] bg-slate-400"></div>
                                <div class="text-[9px] font-black text-slate-900 uppercase tracking-widest">Digital Twin v2.0</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Coord_X</div>
                                <div class="text-[11px] font-mono text-slate-800">124.99.32</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-end">
                            <div class="flex gap-4 items-center bg-white/80 py-2 px-4 rounded-2xl border border-slate-50 backdrop-blur-sm shadow-sm">
                                <div class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                </div>
                                <span class="text-[10px] font-black text-slate-700 tracking-widest uppercase">Escaneando Malla...</span>
                            </div>
                            <div class="w-12 h-12 rounded-full border border-slate-100 flex items-center justify-center bg-white/50 backdrop-blur-sm">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Efecto de barrido de luz mejorado -->
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-400/30 via-transparent to-transparent z-20 pointer-events-none translate-y-full animate-scanner-elegant"></div>
                    
                    <!-- Brillo de cristal -->
                    <div class="absolute top-[-20%] left-[-20%] w-[140%] h-[140%] bg-gradient-to-br from-white/20 via-transparent to-transparent pointer-events-none z-20"></div>

                    <!-- Canvas -->
                    <div ref="canvasContainer" class="w-full h-full absolute inset-0 z-10"></div>
                </div>

                <!-- Adornos Geométricos -->
                <div class="absolute -z-10 -bottom-10 -right-10 w-64 h-64 bg-slate-50 rounded-full mix-blend-multiply opacity-50"></div>
                <div class="absolute -z-10 -top-5 -left-5 w-32 h-32 border border-slate-100 rounded-full animate-spin-very-slow"></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Animación del escáner elegante */
@keyframes scanner-elegant {
    0% { transform: translateY(100%); opacity: 0; }
    20% { opacity: 0.4; }
    80% { opacity: 0.4; }
    100% { transform: translateY(-100%); opacity: 0; }
}
.animate-scanner-elegant {
    animation: scanner-elegant 6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes spin-very-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-very-slow {
    animation: spin-very-slow 60s linear infinite;
}

/* Tipografía y suavizado */
h1 {
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
}

/* Contenedor de la tarjeta con efecto flotante */
.animate-float-subtle {
    animation: float 10s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
</style>