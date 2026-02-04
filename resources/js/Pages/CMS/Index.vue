<template>
  <AppLayout title="Gestión de Contenido">
    <template #header>
      <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
        Gestión Web
      </h2>
    </template>

    <div class="py-8 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <n-config-provider :theme-overrides="themeOverrides">
          <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-4 md:p-6">
            
            <n-tabs type="segment" animated size="large">
              
              <!-- ================= TAB 1: PAQUETES ================= -->
              <n-tab-pane name="packages" tab="Paquetes">
                <div class="flex justify-end mb-6">
                  <!-- FIX: Asegurar paréntesis explícitos para no pasar el evento click -->
                  <button @click="openPackageModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2 text-sm md:text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                    <span class="hidden md:inline">Nuevo Paquete</span>
                    <span class="md:hidden">Nuevo</span>
                  </button>
                </div>

                <!-- Lista de Paquetes (Cards Responsive) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <div v-for="pkg in packages" :key="pkg.id" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col relative group hover:shadow-md transition-shadow">
                    
                    <!-- Status -->
                    <div class="absolute top-4 right-4 z-10">
                       <n-tag :type="pkg.is_active ? 'success' : 'error'" round size="small">
                         {{ pkg.is_active ? 'Activo' : 'Inactivo' }}
                       </n-tag>
                    </div>

                    <!-- Cover Image con onClick Lightbox -->
                    <div class="w-full h-48 rounded-xl bg-gray-100 overflow-hidden mb-4 cursor-zoom-in relative" @click="openLightbox(pkg.images)">
                        <img v-if="pkg.preview_image" :src="pkg.preview_image" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div v-if="pkg.images && pkg.images.length > 1" class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-md">
                            +{{ pkg.images.length - 1 }}
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 text-lg mb-1">{{ pkg.title }}</h4>
                    
                    <!-- Mostrar Precios (Rango o Fijo) -->
                    <div class="text-blue-600 font-bold mb-2">
                        <span v-if="pkg.pricing_options && pkg.pricing_options.length > 0 && pkg.pricing_options[0]">
                            Desde ${{ formatPrice(pkg.pricing_options[0].price) }}
                        </span>
                        <span v-else>
                            ${{ formatPrice(pkg.price) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 line-clamp-2 flex-grow">{{ pkg.description }}</p>
                    
                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50 mt-4">
                      <button @click="openPackageModal(pkg)" class="flex-1 py-2 rounded-lg bg-gray-50 text-gray-600 font-medium text-sm hover:bg-gray-100 transition-colors">Editar</button>
                      <button @click="deletePackage(pkg.id)" class="px-3 py-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </n-tab-pane>

              <!-- ================= TAB 2: PORTAFOLIO ================= -->
              <n-tab-pane name="portfolio" tab="Portafolio">
                <div class="flex justify-end mb-6">
                  <button @click="openPortfolioModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2 text-sm md:text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                    <span class="hidden md:inline">Nuevo Item</span>
                    <span class="md:hidden">Nuevo</span>
                  </button>
                </div>

                <!-- Grid Portafolio -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div v-for="item in portfolio" :key="item.id" class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    <!-- Imagen con Lightbox -->
                    <div class="h-48 w-full bg-gray-100 cursor-zoom-in relative overflow-hidden" @click="openLightbox([item.media_object])">
                       <img v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                       <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                       
                       <!-- Badges -->
                       <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-md text-white px-2 py-1 rounded-lg text-xs font-bold pointer-events-none">
                         {{ item.category }}
                       </div>
                       <div v-if="item.is_featured" class="absolute top-2 right-2 text-yellow-400 bg-white/90 rounded-full p-1 shadow-sm pointer-events-none">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                       </div>
                    </div>
                    
                    <!-- Acciones Rápidas -->
                    <div class="p-3 bg-white">
                        <h4 class="font-bold text-gray-800 text-sm truncate">{{ item.title }}</h4>
                        <div class="flex justify-between items-center mt-2">
                            <button @click="openPortfolioModal(item)" class="text-xs font-bold text-indigo-600 hover:bg-indigo-50 px-2 py-1 rounded">EDITAR</button>
                            <button @click="deletePortfolio(item.id)" class="text-xs font-bold text-red-500 hover:bg-red-50 px-2 py-1 rounded">BORRAR</button>
                        </div>
                    </div>
                  </div>
                </div>
              </n-tab-pane>

            </n-tabs>
          </div>
        </n-config-provider>

      </div>
    </div>

    <!-- ================= MODAL PAQUETES ================= -->
    <n-modal v-model:show="showPackageModal" transform-origin="center">
      <div class="bg-white rounded-2xl w-[95%] max-w-2xl shadow-2xl p-6 fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 m-0 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex justify-between items-center">
          {{ isEditingPackage ? 'Editar Paquete' : 'Crear Nuevo Paquete' }}
          <button @click="showPackageModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
        </h3>
        
        <form @submit.prevent="submitPackage">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            
            <!-- Título -->
            <div class="col-span-2">
               <label class="block text-sm font-medium text-gray-700 mb-1">Título del Paquete</label>
               <n-input v-model:value="packageForm.title" placeholder="Ej: Servicio de Escaneo Avanzado" size="large" />
               <span v-if="packageForm.errors.title" class="text-red-500 text-xs">{{ packageForm.errors.title }}</span>
            </div>
            
            <!-- Descripción -->
            <div class="col-span-2">
               <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
               <n-input type="textarea" v-model:value="packageForm.description" placeholder="Describe brevemente el servicio..." />
            </div>

            <!-- SECCIÓN: OPCIONES Y PRECIOS (Dinámico) -->
            <div class="col-span-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                <label class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Opciones y Precios
                </label>
                
                <div class="space-y-3">
                    <div v-for="(option, index) in packageForm.pricing_options" :key="index" class="flex gap-3 items-center bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex-grow">
                             <n-input v-model:value="option.label" placeholder="Ej: 10 cm, Resolución Alta" size="medium" />
                        </div>
                        <div class="w-32">
                             <n-input-number v-model:value="option.price" :show-button="false" placeholder="0.00" size="medium">
                                <template #prefix>$</template>
                             </n-input-number>
                        </div>
                        <button type="button" @click="removePriceOption(index)" class="text-red-400 hover:text-red-600 p-1" v-if="packageForm.pricing_options.length > 1">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                    
                    <button type="button" @click="addPriceOption" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 mt-2">
                        + Agregar opción
                    </button>
                </div>
            </div>

            <!-- GESTIÓN DE IMÁGENES -->
            <div class="col-span-2 mt-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imágenes del Paquete</label>
              
              <!-- Galería de imágenes existentes (Con opción de eliminar) -->
              <div v-if="packageForm.existing_images?.length" class="flex gap-3 overflow-x-auto pb-2 mb-3">
                <div v-for="(img, idx) in packageForm.existing_images" :key="img.id" class="relative w-24 h-24 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 group">
                  <img :src="img.url" class="w-full h-full object-cover" />
                  <!-- Botón Eliminar Imagen Individual -->
                  <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <button type="button" @click="deleteExistingMedia(img.id, idx)" class="bg-red-500 text-white p-1.5 rounded-full hover:bg-red-600 shadow-lg transform hover:scale-110 transition-transform">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                      </button>
                  </div>
                </div>
              </div>

              <!-- Input File Multiple -->
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-colors relative">
                  <input type="file" multiple @change="handlePackageFiles" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                  <div class="text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span v-if="packageForm.images?.length" class="text-green-600 font-medium">{{ packageForm.images.length }} nuevos archivos seleccionados</span>
                    <span v-else class="text-xs">Click para subir nuevas imágenes (Múltiple)</span>
                  </div>
              </div>
            </div>
            
            <!-- Features -->
            <div class="col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200 mt-2">
               <label class="block text-sm font-bold text-gray-700 mb-2">Características</label>
               <n-dynamic-input v-model:value="packageForm.features" placeholder="Ej: Entrega en 24 horas" :min="1" />
            </div>

            <!-- Switches -->
            <div class="col-span-2 flex flex-col sm:flex-row gap-4 mt-2 border-t pt-4">
              <div class="flex items-center gap-2">
                <n-switch v-model:value="packageForm.is_active" />
                <span class="text-sm text-gray-600">Visible en Web</span>
              </div>
              <div class="flex items-center gap-2">
                <n-switch v-model:value="packageForm.is_promo" />
                <span class="text-sm text-gray-600">Es Promoción</span>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="showPackageModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancelar</button>
            <button type="submit" :disabled="packageForm.processing" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-50">
              {{ isEditingPackage ? 'Guardar Cambios' : 'Crear Paquete' }}
            </button>
          </div>
        </form>
      </div>
    </n-modal>

    <!-- ================= MODAL PORTAFOLIO ================= -->
    <n-modal v-model:show="showPortfolioModal" transform-origin="center">
      <div class="bg-white rounded-2xl w-[95%] max-w-lg shadow-2xl p-6 fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 m-0">
        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex justify-between items-center">
          {{ isEditingPortfolio ? 'Editar Item' : 'Nuevo Item' }}
           <button @click="showPortfolioModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
        </h3>

        <form @submit.prevent="submitPortfolio">
          <div class="space-y-4">
            <div>
               <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
               <n-input v-model:value="portfolioForm.title" placeholder="Ej: Prototipo Motor" size="large" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
              <n-select v-model:value="portfolioForm.category" :options="categoryOptions" size="large" />
            </div>

            <div class="grid grid-cols-2 gap-4">
               <div>
                 <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                 <n-input-number v-model:value="portfolioForm.sort_order" size="large" />
               </div>
               <div class="flex items-center h-full pt-6">
                 <div class="flex items-center gap-2">
                    <n-switch v-model:value="portfolioForm.is_featured" />
                    <span class="text-sm text-gray-600">Destacado</span>
                 </div>
               </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imagen</label>
              <!-- Imagen Actual -->
              <div v-if="isEditingPortfolio && !portfolioFilePreview && portfolioForm.current_image_url" class="mb-2 w-full h-40 rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                <img :src="portfolioForm.current_image_url" class="w-full h-full object-contain" />
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-colors relative">
                  <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                  
                  <div v-if="portfolioFilePreview" class="relative z-10">
                    <img :src="portfolioFilePreview" class="h-32 object-contain rounded-md shadow-sm" />
                    <p class="text-xs text-green-600 mt-2">Nueva imagen lista para subir</p>
                  </div>
                  <div v-else class="text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-xs">Click para reemplazar imagen</span>
                  </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="showPortfolioModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancelar</button>
            <button type="submit" :disabled="portfolioForm.processing" class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 disabled:opacity-50">
              {{ isEditingPortfolio ? 'Guardar Cambios' : 'Crear' }}
            </button>
          </div>
        </form>
      </div>
    </n-modal>

    <!-- ================= LIGHTBOX (FULL SCREEN VIEWER) ================= -->
    <div v-if="showLightbox" class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-xl flex items-center justify-center animate-fade-in" @click.self="closeLightbox">
        
        <!-- Botón Cerrar -->
        <button @click="closeLightbox" class="absolute top-6 right-6 text-white/70 hover:text-white z-50 p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <!-- Navegación -->
        <button v-if="lightboxImages.length > 1" @click.stop="prevImage" class="absolute left-4 text-white p-3 rounded-full bg-white/10 hover:bg-white/20 hover:scale-110 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <button v-if="lightboxImages.length > 1" @click.stop="nextImage" class="absolute right-4 text-white p-3 rounded-full bg-white/10 hover:bg-white/20 hover:scale-110 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>

        <!-- Imagen Principal -->
        <div class="relative max-w-[90vw] max-h-[85vh]">
            <img :src="currentLightboxImage.url" class="max-w-full max-h-[85vh] rounded-md shadow-2xl object-contain" />
            <p v-if="currentLightboxImage.title" class="text-center text-white mt-4 font-medium">{{ currentLightboxImage.title }}</p>
        </div>
        
        <!-- Indicador de página -->
        <div v-if="lightboxImages.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/50 text-sm font-mono bg-black/50 px-3 py-1 rounded-full">
            {{ currentImageIndex + 1 }} / {{ lightboxImages.length }}
        </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { NConfigProvider, NTabs, NTabPane, NModal, NInput, NInputNumber, NSwitch, NDynamicInput, NSelect, NTag } from 'naive-ui';

const props = defineProps({
  packages: Array,
  portfolio: Array,
});

// ================= ESTADO Y LÓGICA: LIGHTBOX =================
const showLightbox = ref(false);
const lightboxImages = ref([]); // Array de objetos {url, title}
const currentImageIndex = ref(0);

const openLightbox = (images) => {
    if (!images || images.length === 0) return;
    // Normalizar entrada a array de objetos con .url
    lightboxImages.value = Array.isArray(images) ? images : [images];
    currentImageIndex.value = 0;
    showLightbox.value = true;
};

const closeLightbox = () => {
    showLightbox.value = false;
};

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % lightboxImages.value.length;
};

const prevImage = () => {
    currentImageIndex.value = (currentImageIndex.value - 1 + lightboxImages.value.length) % lightboxImages.value.length;
};

const currentLightboxImage = computed(() => {
    return lightboxImages.value[currentImageIndex.value] || {};
});

// ================= ESTADO Y LÓGICA: PAQUETES =================
const showPackageModal = ref(false);
const isEditingPackage = ref(false);

const packageForm = useForm({
  id: null,
  title: '',
  description: '',
  features: [''],
  pricing_options: [{ label: '', price: 0 }], // NUEVO: Array para precios
  is_active: true,
  is_promo: false,
  images: [], // Archivos nuevos
  existing_images: [], // Imágenes para mostrar y borrar
});

const handlePackageFiles = (e) => {
    packageForm.images = Array.from(e.target.files);
};

const addPriceOption = () => {
    packageForm.pricing_options.push({ label: '', price: 0 });
};

const removePriceOption = (index) => {
    if (packageForm.pricing_options.length > 1) {
        packageForm.pricing_options.splice(index, 1);
    }
};

const formatPrice = (value) => {
    return new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2 }).format(value);
};

const openPackageModal = (pkg = null) => {
  packageForm.reset();
  packageForm.clearErrors();
  packageForm.images = []; 

  // FIX: Verificación robusta. Asegurarnos que 'pkg' sea un objeto válido y no un Evento del mouse.
  // Esto previene que al hacer clic en "Nuevo", entre en modo edición erróneamente.
  if (pkg && typeof pkg === 'object' && pkg.id) {
    isEditingPackage.value = true;
    packageForm.id = pkg.id;
    packageForm.title = pkg.title;
    packageForm.description = pkg.description;
    packageForm.features = pkg.features || [''];
    
    // Clonamos profundamente para evitar problemas de referencia o strings no parseados
    let options = [];
    try {
       // Si el backend envía un objeto/array, lo clonamos. Si no, usamos default.
       if (pkg.pricing_options && Array.isArray(pkg.pricing_options) && pkg.pricing_options.length > 0) {
           options = JSON.parse(JSON.stringify(pkg.pricing_options));
       } else {
           // Fallback por si llega mal formado o vacío
           options = [{ label: 'Precio Base', price: parseFloat(pkg.price || 0) }];
       }
    } catch(e) {
       options = [{ label: 'Precio Base', price: parseFloat(pkg.price || 0) }];
    }

    packageForm.pricing_options = options;
        
    packageForm.is_active = Boolean(pkg.is_active);
    packageForm.is_promo = Boolean(pkg.is_promo);
    packageForm.existing_images = pkg.images || []; 
  } else {
    // MODO CREACIÓN
    isEditingPackage.value = false;
    packageForm.id = null; // IMPORTANTE: Resetear ID explícitamente
    packageForm.features = [''];
    packageForm.pricing_options = [{ label: '', price: 0 }];
    packageForm.existing_images = [];
  }
  showPackageModal.value = true;
};

const submitPackage = () => {
  // FIX: Verificación doble de ID para evitar error 405 (PUT a url sin ID)
  if (isEditingPackage.value && packageForm.id) {
    // EDITAR: Usamos POST con _method: PUT para soportar archivos
    packageForm.transform((data) => ({
      ...data,
      _method: 'PUT',
    })).post(route('cms.packages.update', packageForm.id), {
      onSuccess: () => showPackageModal.value = false,
      preserveScroll: true
    });
  } else {
    // CREAR: Usamos POST normal.
    // Transform opcional para asegurar que no se envíe ID basura
    packageForm.transform((data) => {
        const { id, ...rest } = data;
        return rest;
    }).post(route('cms.packages.store'), {
      onSuccess: () => showPackageModal.value = false,
      preserveScroll: true
    });
  }
};

const deletePackage = (id) => {
  if(confirm('¿Estás seguro de eliminar este paquete?')) {
    router.delete(route('cms.packages.destroy', id));
  }
};

// Eliminar imagen individualmente
const deleteExistingMedia = (mediaId, index) => {
    if(confirm('¿Eliminar esta imagen permanentemente?')) {
        router.delete(route('media.delete-file', mediaId), {
            preserveScroll: true,
            onSuccess: () => {
                // Eliminar del array local para feedback visual inmediato
                packageForm.existing_images.splice(index, 1);
            }
        });
    }
};

// ================= ESTADO Y LÓGICA: PORTAFOLIO =================
const showPortfolioModal = ref(false);
const isEditingPortfolio = ref(false);
const portfolioFilePreview = ref(null);
const categoryOptions = [
  { label: 'Impresión 3D', value: 'Impresión' },
  { label: 'Escaneo 3D', value: 'Escaneo' },
  { label: 'Modelado 3D', value: 'Modelado' }
];

const portfolioForm = useForm({
  id: null,
  title: '',
  category: 'Impresión',
  sort_order: 0,
  is_featured: false,
  image: null,
  current_image_url: null,
});

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    portfolioForm.image = file;
    portfolioFilePreview.value = URL.createObjectURL(file);
  }
};

const openPortfolioModal = (item = null) => {
  portfolioForm.reset();
  portfolioForm.clearErrors();
  portfolioFilePreview.value = null;
  
  if (item && item.id) {
    isEditingPortfolio.value = true;
    portfolioForm.id = item.id;
    portfolioForm.title = item.title;
    portfolioForm.category = item.category;
    portfolioForm.sort_order = item.sort_order;
    portfolioForm.is_featured = Boolean(item.is_featured);
    portfolioForm.current_image_url = item.image_url;
  } else {
    isEditingPortfolio.value = false;
    portfolioForm.id = null;
    portfolioForm.sort_order = 0;
  }
  showPortfolioModal.value = true;
};

const submitPortfolio = () => {
  if (isEditingPortfolio.value && portfolioForm.id) {
    // CORRECCIÓN: Eliminamos el transform con _method: 'PUT'
    // El error 405 indica que la ruta espera POST, no PUT.
    // Como estamos enviando FormData (archivos), Inertia usa POST automáticamente,
    // y Laravel recibirá la petición correctamente sin necesidad de spoofing si la ruta es POST.
    portfolioForm.post(route('cms.portfolio.update', portfolioForm.id), {
      onSuccess: () => showPortfolioModal.value = false,
      preserveScroll: true
    });
  } else {
    portfolioForm.post(route('cms.portfolio.store'), {
      onSuccess: () => showPortfolioModal.value = false,
      preserveScroll: true
    });
  }
};

const deletePortfolio = (id) => {
  if(confirm('¿Eliminar este item del portafolio?')) {
    router.delete(route('cms.portfolio.destroy', id));
  }
};

// ================= THEME =================
const themeOverrides = {
  common: { primaryColor: '#007AFF', borderRadius: '12px' },
  Tabs: { 
    tabColorSegment: '#f3f4f6',
    tabTextColorActiveSegment: '#007AFF', 
    tabTextColorSegment: '#6b7280',
    tabFontWeightActive: '700'
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>