<script>
import { NButton } from 'naive-ui';
import { Link } from '@inertiajs/vue3';

// CORRECCIÓN: Usamos 'esm.sh' en lugar de jsdelivr/unpkg.
// 'esm.sh' reescribe las importaciones internas para que el navegador 
// encuentre 'three' sin necesidad de un Import Map.
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

        // RUTA DEL ARCHIVO: 
        // Asegúrate de que 'human_body.glb' esté en la carpeta /public de tu proyecto.
        const MODEL_URL = '/human_body.glb';

        const loadModel = () => {
            const loader = new GLTFLoader();

            loader.load(
                MODEL_URL,
                (gltf) => {
                    const model = gltf.scene;
                    
                    // --- AJUSTES DE TAMAÑO Y POSICIÓN ---
                    // IMPORTANTE: Si solo ves los pies, reduce estos números.
                    // Prueba valores como 0.01 (si viene en cm) o 0.5 (si viene en m).
                    // Antes estaba en 1.5. Ahora probamos con 0.01 para hacerlo mucho más pequeño.
                    const s = 0.8; 
                    model.scale.set(s, s, s); 

                    // Ajustamos la altura para centrarlo (eje Y)
                    // Si el modelo flota, baja este número. Si está hundido, súbelo.
                    model.position.y = -1.7; 
                    
                    // Centrar horizontalmente si viene desplazado
                    model.position.x = 0;
                    model.position.z = 0;

                    // model.rotation.y = Math.PI;  // Descomentar si mira al revés

                    scene.add(model);
                    modelGroup = model;

                    // Si el modelo trae animaciones
                    if (gltf.animations && gltf.animations.length) {
                        mixer = new THREE.AnimationMixer(model);
                        const action = mixer.clipAction(gltf.animations[0]); 
                        action.play();
                    }
                },
                (xhr) => {
                    console.log((xhr.loaded / xhr.total * 100) + '% cargado');
                },
                (error) => {
                    console.error('Error cargando el modelo:', error);
                    console.error('Asegúrate de que el archivo .glb esté en la carpeta public y la ruta sea correcta.');
                }
            );
        };

        onMounted(() => {
            if (!canvasContainer.value) return;

            // 1. Escena
            scene = new THREE.Scene();
            
            // 2. Cámara
            const width = canvasContainer.value.clientWidth;
            const height = canvasContainer.value.clientHeight;
            camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
            camera.position.set(0, 0, 5); 

            // 3. Renderizador
            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.0;
            
            canvasContainer.value.appendChild(renderer.domElement);

            // 4. Iluminación
            const ambientLight = new THREE.AmbientLight(0xffffff, 1.0);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 2);
            mainLight.position.set(2, 5, 5);
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0x4cc9f0, 1.5);
            fillLight.position.set(-5, 0, 2);
            scene.add(fillLight);

            // 5. Cargar
            loadModel();

            const clock = new THREE.Clock();

            // 6. Animación
            const animate = () => {
                animationId = requestAnimationFrame(animate);
                const delta = clock.getDelta();

                if (mixer) mixer.update(delta);

                // Rotar el modelo completo lentamente
                if (modelGroup) {
                    modelGroup.rotation.y += 0.005; 
                }

                renderer.render(scene, camera);
            };
            animate();

            // 7. Resize
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
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden pt-20">
        <!-- Fondo Abstracto Animado -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] bg-gradient-to-br from-[#2f4b59]/20 to-[#4cc9f0]/20 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-[#2f4b59]/10 rounded-full blur-3xl"></div>
            <!-- Grid 3D de fondo -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(47,75,89,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(47,75,89,0.03)_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_at_center,black_40%,transparent_70%)]"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 grid md:grid-cols-2 gap-12 items-center">
            <!-- Texto -->
            <div class="space-y-8 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-[#2f4b59] text-xs font-bold tracking-widest uppercase animate-fade-in-up">
                    <span class="w-2 h-2 rounded-full bg-[#4cc9f0] animate-ping"></span>
                    Tecnología de Escaneo 3D
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold leading-tight text-slate-900 tracking-tight animate-fade-in-up delay-100">
                    Tu realidad, <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#2f4b59] to-[#4cc9f0]">Digitalizada.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-500 max-w-lg mx-auto md:mx-0 leading-relaxed animate-fade-in-up delay-200">
                    Transformamos personas y objetos en modelos 3D de alta fidelidad. Desde figuras coleccionables hasta activos para el metaverso.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start animate-fade-in-up delay-300">
                    <n-button @click="$inertia.visit(route('landing.contact'))" type="primary" size="large" round class="px-8 h-12 text-lg shadow-xl shadow-[#2f4b59]/20">
                        Iniciar Proyecto
                    </n-button>
                    <n-button @click="$inertia.visit(route('landing.portfolio'))" size="large" round ghost class="px-8 h-12 text-lg border-slate-300 text-slate-600 hover:text-[#2f4b59]">
                        Ver Galería
                    </n-button>
                </div>
            </div>

            <!-- Visual "Holográfico" -->
            <div class="relative h-[500px] flex items-center justify-center animate-float">
                <!-- Círculos concéntricos simulando escáner -->
                <div class="absolute w-[400px] h-[400px] border border-[#2f4b59]/20 rounded-full animate-[spin_10s_linear_infinite]"></div>
                <div class="absolute w-[350px] h-[350px] border border-dashed border-[#4cc9f0]/40 rounded-full animate-[spin_15s_linear_infinite_reverse]"></div>
                
                <!-- Contenedor Modelo 3D -->
                <div class="relative z-10 w-64 h-80 bg-gradient-to-b from-slate-200 to-slate-100 rounded-2xl shadow-2xl flex items-center justify-center border border-white/50 backdrop-blur-sm overflow-hidden group">
                    <!-- Efecto de barrido de luz (Scanner Light) -->
                    <div class="absolute top-0 left-0 z-20 w-full h-1 bg-[#4cc9f0] shadow-[0_0_15px_rgba(76,201,240,0.8)] animate-scan-vertical pointer-events-none"></div>
                    
                    <!-- Canvas de Three.js -->
                    <div ref="canvasContainer" class="w-full h-full absolute inset-0 z-10"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scan-vertical {
    0% { top: 0%; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
.animate-scan-vertical {
    animation: scan-vertical 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>