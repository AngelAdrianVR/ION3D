<script>
import * as THREE from 'https://esm.sh/three@0.160.0';
import { GLTFLoader } from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader';
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

export default {
    name: 'ThreeDViewer',
    props: {
        // Lista de modelos: cada uno { name, url, scale, position: { x, y, z } }
        models: {
            type: Array,
            required: true,
        },
        // Intervalo de cambio automático en ms (0 = desactivado)
        changeInterval: {
            type: Number,
            default: 4000,
        },
        // Relación de aspecto (ancho/alto), por defecto se calcula del contenedor
        aspectRatio: {
            type: Number,
            default: null,
        },
        // Posición inicial de la cámara
        cameraPosition: {
            type: Object,
            default: () => ({ x: 0, y: 1.2, z: 10 }),
        },
        // FOV de la cámara
        cameraFov: {
            type: Number,
            default: 35,
        },
        // Velocidad de rotación crucero
        rotationSpeed: {
            type: Number,
            default: 0.003,
        },
        // Exposición tonal
        toneMappingExposure: {
            type: Number,
            default: 1.2,
        },
    },
    emits: ['loaded', 'model-changed'],
    setup(props, { emit }) {
        const canvasContainer = ref(null);
        const isLoaded = ref(false);

        let renderer, scene, camera, animationId;
        let modelGroup;
        let mixer;
        let platformGrid;
        let modelInterval;
        let currentModelIndex = 0;
        let currentRotationSpeed = 0.15;
        const targetRotationSpeed = props.rotationSpeed;
        const damping = 0.96;

        const loadModel = (modelConfig) => {
            const loader = new GLTFLoader();
            const currentRotationY = modelGroup ? modelGroup.rotation.y : (150 * Math.PI / 180);

            loader.load(
                modelConfig.url,
                (gltf) => {
                    const newModel = gltf.scene;

                    const s = modelConfig.scale;
                    newModel.scale.set(s, s, s);

                    newModel.position.set(
                        modelConfig.position.x,
                        modelConfig.position.y,
                        modelConfig.position.z
                    );

                    newModel.rotation.y = currentRotationY;

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

                    if (modelGroup) {
                        scene.remove(modelGroup);
                    }

                    scene.add(newModel);
                    modelGroup = newModel;

                    if (gltf.animations && gltf.animations.length) {
                        mixer = new THREE.AnimationMixer(newModel);
                        const action = mixer.clipAction(gltf.animations[0]);
                        action.play();
                    } else {
                        mixer = null;
                    }

                    if (!isLoaded.value) {
                        setTimeout(() => {
                            isLoaded.value = true;
                            emit('loaded');
                        }, 300);
                    }

                    emit('model-changed', { index: currentModelIndex, config: modelConfig });
                },
                undefined,
                (error) => console.error(`Error cargando modelo ${modelConfig.url}:`, error)
            );
        };

        const cycleNextModel = () => {
            if (!props.models || props.models.length === 0) return;
            currentModelIndex = (currentModelIndex + 1) % props.models.length;
            loadModel(props.models[currentModelIndex]);
        };

        const goToModel = (index) => {
            if (!props.models || index < 0 || index >= props.models.length) return;
            currentModelIndex = index;
            loadModel(props.models[index]);
        };

        const initScene = () => {
            if (!canvasContainer.value) return;

            scene = new THREE.Scene();

            const width = canvasContainer.value.clientWidth;
            const height = canvasContainer.value.clientHeight;

            const aspect = props.aspectRatio || (width / height);
            camera = new THREE.PerspectiveCamera(props.cameraFov, aspect, 0.1, 100);
            camera.position.set(
                props.cameraPosition.x,
                props.cameraPosition.y,
                props.cameraPosition.z
            );

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.2));
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = props.toneMappingExposure;
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
            mainLight.shadow.mapSize.width = 512;
            mainLight.shadow.mapSize.height = 512;
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xdbeafe, 1.8);
            fillLight.position.set(-5, 2, 2);
            scene.add(fillLight);

            const rimLight = new THREE.SpotLight(0xffffff, 4);
            rimLight.position.set(0, 5, -5);
            scene.add(rimLight);

            if (props.models && props.models.length > 0) {
                loadModel(props.models[0]);
            }

            if (props.changeInterval > 0 && props.models && props.models.length > 1) {
                modelInterval = setInterval(cycleNextModel, props.changeInterval);
            }

            const clock = new THREE.Clock();

            const animate = () => {
                animationId = requestAnimationFrame(animate);
                const delta = clock.getDelta();

                if (mixer) mixer.update(delta);

                if (modelGroup) {
                    if (currentRotationSpeed > targetRotationSpeed) {
                        currentRotationSpeed *= damping;
                        if (camera.position.z > 8.5) {
                            camera.position.z -= 0.03;
                        }
                    } else {
                        currentRotationSpeed = targetRotationSpeed;
                    }
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
                    camera.aspect = props.aspectRatio || (w / h);
                    camera.updateProjectionMatrix();
                    renderer.setSize(w, h);
                }
            };
            window.addEventListener('resize', handleResize);
        };

        onMounted(() => {
            initScene();
        });

        onBeforeUnmount(() => {
            cancelAnimationFrame(animationId);
            if (modelInterval) clearInterval(modelInterval);
            if (renderer) renderer.dispose();
        });

        // Reactividad si cambian los modelos
        watch(() => props.models, (newModels) => {
            if (newModels && newModels.length > 0 && scene) {
                currentModelIndex = 0;
                loadModel(newModels[0]);
                if (modelInterval) clearInterval(modelInterval);
                if (props.changeInterval > 0 && newModels.length > 1) {
                    modelInterval = setInterval(cycleNextModel, props.changeInterval);
                }
            }
        }, { deep: true });

        return { canvasContainer, isLoaded, goToModel };
    },
};
</script>

<template>
    <div ref="canvasContainer" class="w-full h-full absolute inset-0 z-10"></div>
</template>
