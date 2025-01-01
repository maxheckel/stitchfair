<template>
    <LandingLayout>
        <template #content>
            <img :src="image" id="imageid" class="hidden"/>
            <div class="min-h-screen">
                <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Progress bar -->
                    <div class="bg-indigo-100 h-2">
                        <div
                            class="bg-indigo-600 h-full transition-all duration-500 ease-in-out"
                            :style="{ width: `${((currentStep - 1) / 2) * 100}%` }"
                        ></div>
                    </div>

                    <!-- Step indicator -->
                    <div class="flex justify-between px-8 py-4 border-b border-indigo-100">
                        <div
                            v-for="step in 3"
                            :key="step"
                            class="flex flex-col items-center"
                            :class="{ 'text-indigo-600': step <= currentStep, 'text-gray-400': step > currentStep }"
                        >
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-300"
                                :class="{ 'border-indigo-600 bg-indigo-600 text-white': step <= currentStep, 'border-gray-300': step > currentStep }"
                            >
                                {{ step }}
                            </div>
                            <span class="mt-2 text-sm font-medium">{{ stepTitles[step - 1] }}</span>
                        </div>
                    </div>

                    <!-- Step content -->
                    <div class="p-8">
                        <div v-if="loadingImage" class="flex-row justify-center text-center">
                            <Spinner :slice-width="100" :slice-height="100" :slice-depth="0.05" :slices="200"
                                     style="width: 100px; height: 100px" :image-url="route('index')+'/img/mark.png'"/>
                            Uploading & Preparing Image
                        </div>

                        <div v-else>
                            <!-- Step 1: Upload and Crop -->
                            <div v-if="currentStep === 1" class="space-y-6">
                                <h2 class="text-2xl font-bold text-indigo-800">Upload and Crop Your Image</h2>
                                <div
                                    v-if="!uploadedImage"
                                    class="border-2 border-dashed border-indigo-300 rounded-lg p-8 text-center cursor-pointer hover:border-indigo-500 transition-colors"
                                    @click="triggerFileInput"
                                >
                                    <UploadCloud class="w-16 h-16 text-indigo-400 mx-auto mb-4"/>
                                    <p class="text-indigo-600 font-medium">Click to upload an image or drag and drop</p>
                                    <p class="text-sm text-gray-500 mt-2">Supported formats: PNG, JPG, GIF (max 5MB)</p>
                                </div>
                                <input
                                    type="file"
                                    ref="fileInput"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleFileUpload"
                                />

                                <div v-if="uploadedImage" class="mt-6">
                                    <Cropper
                                        class="cropper"
                                        :src="uploadedImage"
                                        :stencil-props="{
                                        aspectRatio: 1
                                      }"
                                        @change="onChange"
                                    />
                                    <div class="mt-4 flex justify-end space-x-4">

                                        <label class="flex items-center gap-2">
                                            <input v-model="removeBg" type="checkbox"> Automatically remove background
                                        </label>
                                        <button
                                            @click="resetImage"
                                            class="px-4 py-2 bg-white text-indigo-600 rounded-md border border-indigo-600 hover:bg-indigo-50 transition-colors"
                                        >
                                            Reset
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Step 2: Choose Specifications -->
                            <div v-if="currentStep === 2" class="space-y-6">
                                <h2 class="text-2xl font-bold text-indigo-800">Choose Pattern Specifications</h2>
                                <div class="flex gap-4">
                                    <div>
                                        <img v-show="!encodingImage" @load="encodingImage = false" :src="croppedImage" class="max-w-2x w-80"/>
                                        <div v-show="encodingImage" class="w-full flex items-center justify-center">
                                            <div class="text-center">
                                                <Spinner :slice-width="100" :slice-height="100" :slice-depth="0.05"
                                                         :slices="200" style="width: 100px; height: 100px"
                                                         :image-url="route('index')+'/img/mark.png'"/>
                                                Loading Image
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="width" class="block text-sm font-medium text-gray-700 mb-1">Width
                                                    (stitches)</label>
                                                <input
                                                    type="number"
                                                    id="width"
                                                    @blur="saveAndLoadVariants"
                                                    v-model="patternWidth"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                                    min="10"
                                                    max="500"
                                                />
                                            </div>
                                            <div>
                                                <label for="height"
                                                       class="block text-sm font-medium text-gray-700 mb-1">Height
                                                    (stitches)</label>
                                                <input
                                                    @blur="saveAndLoadVariants"
                                                    type="number"
                                                    id="height"
                                                    v-model="patternHeight"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                                    min="10"
                                                    max="500"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label for="colorCount"
                                                   class="block mt-4 text-sm font-medium text-gray-700 mb-1">Color Count
                                                ({{ colorCount }} colors)</label>
                                            <input
                                                @mouseup="saveAndLoadVariants"
                                                type="range"
                                                id="colorCount"
                                                v-model="colorCount"
                                                class="w-full"
                                                min="2"
                                                max="100"
                                            />
                                            <div class="flex justify-between text-sm text-gray-500">
                                                <span>2 colors</span>
                                                <span class="font-bold">{{ colorCount }} colors</span>
                                                <span>100 colors</span>
                                            </div>


                                        </div>
                                        <div v-if="!loadingVariants">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div
                                                    v-for="(variant, i) in variants"
                                                    :key="i"
                                                    class="brightness-150 border-2 rounded-lg p-2 cursor-pointer transition-all duration-300 hover:shadow-lg"
                                                    :class="{ 'border-indigo-500 ring-2 ring-indigo-300': selectedVariant === i, 'border-gray-200 hover:border-indigo-300': selectedVariant !== i }"
                                                    @click="() => {selectedVariant = i; chosenVariant = variant}"
                                                >
                                                    <img :src="'data:image/png;base64,'+variant.base64PngImage"
                                                         :alt="`Pattern variant ${i}`" class="w-full h-auto"/>
                                                </div>


                                            </div>


                                            <button
                                                @click="getVariants"
                                                class="px-4 py-2 bg-white text-indigo-600 rounded-md border border-indigo-600 hover:bg-indigo-50 transition-colors mt-4"

                                            >
                                                {{ variants.length > 0 ? 'Regenerate' : 'Generate Pattern' }}
                                            </button>
                                        </div>

                                        <div v-else class="w-full flex items-center justify-center">
                                            <div class="text-center">
                                                <Spinner :slice-width="100" :slice-height="100" :slice-depth="0.05"
                                                         :slices="200" style="width: 100px; height: 100px"
                                                         :image-url="route('index')+'/img/mark.png'"/>
                                                Loading Variants
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Select Variant -->
                        <div v-if="currentStep === 3" >
                            <Refine v-if="chosenVariant" :variant="chosenVariant" :pattern="pattern"/>

                        </div>
                    </div>

                    <!-- Navigation buttons -->
                    <div class="px-8 py-4 bg-gray-50 flex justify-between" v-if="!loadingImage">
                        <button
                            v-if="currentStep > 1"
                            @click="currentStep--"
                            class="px-4 py-2 bg-white disabled:opacity-25 text-indigo-600 rounded-md border border-indigo-600 hover:bg-indigo-50 transition-colors"
                        >
                            Previous
                        </button>
                        <button
                            v-if="currentStep < 3"
                            @click="currentStep++"
                            class="px-4 py-2 disabled:opacity-25 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                            :disabled="!canProceed"
                        >
                            Next
                        </button>
                        <button
                            v-if="currentStep === 3"
                            @click="finishWizard"
                            class="px-4 py-2 disabled:opacity-25 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                            :disabled="!canProceed"
                        >
                            Create Pattern
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </LandingLayout>
</template>

<script setup>
import {ref, computed, onMounted, watch} from 'vue'
import {UploadCloud} from 'lucide-vue-next'
import {Cropper} from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import LandingLayout from "@/Layouts/LandingLayout.vue";
import Spinner from "@/Components/Spinner.vue";
import {router} from "@inertiajs/vue3";
import {string} from "three/tsl";
import Refine from "@/Pages/Wizard/Refine.vue";

const props = defineProps({
    image: String,
    pattern: Object
})

const currentStep = ref(props.pattern ? 2 : 1)

const stepTitles = ['Upload & Crop', 'Specifications', 'Refine']

const fileInput = ref(null)
const uploadedImage = ref(null)
const croppedImage = ref(null)
const patternWidth = ref(props.pattern?.width ?? 100)
const patternHeight = ref(props.pattern?.width ?? 100)
const colorCount = ref(props.pattern?.color_count ?? 20)
const selectedVariant = ref(null)
const cropperRef = ref(null)
const removeBg = ref(false)
const loadingImage = ref(false)
const loadingVariants = ref(false)
const encodingImage = ref(false)
const variants = ref([])
const chosenVariant = ref(null)

const getVariants = () => {
    loadingVariants.value = true
    chosenVariant.value = null
    selectedVariant.value = null
    window.axios.get(route('pattern.variants', props.pattern.uuid)).then((res) => {
        loadingVariants.value = false
        variants.value = res.data
    })
}

onMounted(() => {
    if (props.pattern) {
        getVariants()
        encodingImage.value = true
        croppedImage.value = route('pattern.original', props.pattern.uuid)
        uploadedImage.value = croppedImage.value

    }
})

const saveAndLoadVariants = () => {
    loadingVariants.value = true
    if (patternWidth.value > 500){
        alert('Width and height cannot exceed 500 stitches')
        patternWidth.value = 500;
    }
    if (patternHeight.value > 500){
        alert('Width and height cannot exceed 500 stitches')
        patternHeight.value = 500;
    }

    window.axios.patch(route('pattern.update', props.pattern.uuid), {
        color_count: colorCount.value,
        width: patternWidth.value,
        height: patternHeight.value
    }).then(() => {
        getVariants()
    })
}
watch(currentStep, (current, previous) => {

    if (current === 2 && previous === 1) {
        cropImage()
        if (!props.pattern) {
            loadingImage.value = true;
            const formData = new FormData();
            formData.append('image', croppedImage.value)
            if (removeBg.value === true) {
                formData.append("remove_background", true)
            }
            formData.append('color_count', colorCount.value)
            formData.append('width', patternWidth.value)
            formData.append('height', patternHeight.value)
            window.axios.post(route('pattern.store'), formData, {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }).then((res) => {
                console.log('here')
                if (res.data.pattern.uuid) {
                    router.visit(route('pattern.show', res.data.pattern.uuid))
                }
            }).catch((err) => {

            })
        } else {
            loadingImage.value = true;
            const formData = new FormData();
            formData.append('image', croppedImage.value)
            if (removeBg.value === true) {
                formData.append("remove_background", true)
            }
            window.axios.patch(route('pattern.update', props.pattern.uuid), {
                image: croppedImage.value,
                remove_background: removeBg.value
        }).finally(() => {

                window.location.reload()
            })
        }


    }

})

const triggerFileInput = () => {
    fileInput.value.click()
}

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            uploadedImage.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const onChange = (cropper) => {
    cropperRef.value = cropper

}

const cropImage = () => {
    if (cropperRef.value) {
        const canvas = cropperRef.value.canvas
        croppedImage.value = canvas.toDataURL()
        uploadedImage.value = croppedImage.value
    }
}

const resetImage = () => {
    uploadedImage.value = null
    croppedImage.value = null
}

const canProceed = computed(() => {
    if (currentStep.value === 1) return uploadedImage.value !== null
    if (currentStep.value === 2) {
        return patternWidth.value > 0 && patternHeight.value > 0 && chosenVariant.value !== null
    }
    if (currentStep.value === 3) return selectedVariant.value !== null
    return false
})

const finishWizard = () => {
    console.log('Wizard completed with the following data:', {
        image: croppedImage.value || uploadedImage.value,
        width: patternWidth.value,
        height: patternHeight.value,
        colorCount: colorCount.value,
        selectedVariant: selectedVariant.value
    })
}
</script>

<style>
.cropper {
    height: 400px;
    background: #DDD;
}
</style>

