<template>
    <Modal :show="replacingColor != null">
        <div class="bg-white max-w-7xl p-4">

            <div class="flex items-center w-1/2 ml-[25%] justify-between">
                <div class="min-w-40 text-center">
                    Removing Color
                    <div class="w-20 mt-2 h-20 mx-auto relative block rounded-md border border-gray-300" :style="{backgroundColor: replacingColor}">

                    </div>
                </div>
                <div class="min-w-40 text-center">
                    Replacing With
                    <div class="w-20 mt-2 h-20 mx-auto relative block rounded-md border border-gray-300" :style="{backgroundColor: withColor}">

                    </div>
                </div>
            </div>
            <div class="flex items-center mt-8">

                <div class="w-full">
                    Choose Color Replacement
                    <div class="w-full grid grid-cols-8 gap-4 mt-2">
                        <template v-for="color in variant.palette">
                        <div  class="group text-center" v-if="color !== replacingColor">
                            <div  :class="{'border-indigo-600 border-2 shadow-md': color === withColor}" @click="withColor = color" class="border border-gray-300 w-10 cursor-pointer h-10 rounded-md flex items-center justify-center" :style="{backgroundColor: color}">

                            </div>
                        </div>
                        </template>
                    </div>
                </div>

            </div>
            <div class="flex items-center mt-8 gap-4">
                <button @click="handleReplace" class="px-4 py-2 disabled:opacity-25 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                    Save
                </button>
                <button
                    @click="replacingColor = null"
                    class="px-4 py-2 bg-white text-indigo-600 rounded-md border border-indigo-600 hover:bg-indigo-50 transition-colors">
                    Cancel
                </button>
            </div>

        </div>
    </Modal>
    <h2 class="text-2xl font-bold text-indigo-800">Refine</h2>
    <div class="grid grid-cols-2">
        <div class="overflow-scroll py-16" :style="{maxHeight: pattern.height*4+'px'}">
            <div :style="{height: zoomLevel*2+'px', width: pattern.width*zoomLevel*2+'px'}" v-for="y in pattern.height" class="mx-auto">
                <template v-for="x in pattern.width">
                    <Pixel @mouseover="handleMouseOver(x, y, $event)" @dragstart="setColor(x,y)" @click="setColor(x, y)" :zoom="zoomLevel" :background-color="variant.pixels[x+','+y]"/>
                </template>
            </div>

        </div>

        <div class="relative">
            <div class="absolute top-0 left-0 -ml-16">
                <div class="p-2 rounded-md cursor-pointer bg-black bg-opacity-10 mb-1" @click="zoomLevel++">
                    <ZoomIn/>
                </div>
                <div class="p-2 rounded-md cursor-pointer bg-black bg-opacity-10" @click="zoomLevel > 1 ? zoomLevel-- : null">
                    <ZoomOut/>
                </div>

                <div class="text-xs">
                    Brush
                </div>
                <div class="p-2 rounded-md w-10 h-10 border border-gray-300 flex items-center justify-center" :style="{backgroundColor: currentColor !== 'clear' && currentColor !== 'eyedropper' ? currentColor : 'transparent'}">
                    <Pipette v-if="currentColor === 'eyedropper'"/>
                    <span v-else-if="currentColor === 'clear'" class="items-center text-2xl text-red-600" style="font-family: Arial, SansSerif">
                        x
                    </span>
                    <Brush style="mix-blend-mode: difference; color: white;" v-else/>
                </div>
            </div>

            <div class="px-8 ">
                Click color to change stitches
                <div class="grid grid-cols-10 gap-4">
                    <div v-for="color in variant.palette" class="group text-center">
                        <div @click="currentColor = color" :class="{'border-indigo-600 border-2': currentColor === color}" class="border border-gray-300 w-10 cursor-pointer h-10 rounded-md flex items-center justify-center" :style="{backgroundColor: color}">
                            <Brush :class="{'!block': currentColor === color}" class="hidden group-hover:block" style="mix-blend-mode: difference; color: white;" />
                        </div>
                        <div @click="replacingColor = color" class="underline cursor-pointer text-indigo-600 hidden text-xs group-hover:block">
                            swap
                        </div>
                    </div>
                    <div class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center text-2xl text-red-600 rounded-md" style="font-family: Arial, SansSerif" @click="currentColor = 'clear'">
                        x
                    </div>
                    <div class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center rounded-md" @click="currentColor = 'eyedropper'">
                        <Pipette/>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import Pixel from "@/Pages/Wizard/Pixel.vue";
import {ZoomIn, ZoomOut, Pipette, Brush} from "lucide-vue-next";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    variant: Object,
    pattern: Object
})
const currentColor = ref()
const zoomLevel = ref(2)
const replacingColor = ref()
const withColor = ref()
let saveTimeout = null

const setColor = (x, y) => {
    if (currentColor.value === 'clear'){
        props.variant.pixels[x+','+y]=null
        return
    }
    if (currentColor.value === 'eyedropper'){
        currentColor.value = props.variant.pixels[x+','+y]
    }
    props.variant.pixels[x+','+y]=currentColor.value
    if (saveTimeout !== null){
        clearTimeout(saveTimeout)
    }
    saveTimeout = setTimeout(() => {
        window.axios.patch(route('pattern.update', props.pattern.uuid), {
            pixels: props.variant
        })
    }, 3000)
}

const handleReplace = () =>{
    if (!withColor.value){
        alert('You must select a replacement color')
        return
    }
    for (const pixel in props.variant.pixels){
        if (props.variant.pixels[pixel] === replacingColor.value){
            props.variant.pixels[pixel] = withColor.value
        }
    }

    props.variant.palette = props.variant.palette.filter(color => color !== replacingColor.value)
    window.axios.patch(route('pattern.update', props.pattern.uuid), {
        pixels: props.variant
    })
    replacingColor.value = null
    withColor.value = null
}

const handleMouseOver = (x, y, e) => {
    if (e.buttons === 1 || e.buttons === 3){
        setColor(x, y)
    }
}
</script>
