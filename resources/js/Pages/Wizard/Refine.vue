<template>
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
                <div class="p-2 rounded-md w-10 h-10 border border-gray-300 flex items-center justify-center" :style="{backgroundColor: currentColor != 'clear' && currentColor != 'eyedropper' ? currentColor : 'transparent'}">
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
                    <div v-for="color in variant.palette">
                        <div @click="currentColor = color" :class="{'border-indigo-600 border-4': currentColor === color}" class="border border-gray-300 w-10 cursor-pointer h-10 rounded-md" :style="{backgroundColor: color}"/>
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

const props = defineProps({
    variant: Object,
    pattern: Object
})
const currentColor = ref()
const zoomLevel = ref(2)
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

const handleMouseOver = (x, y, e) => {
    if (e.buttons === 1 || e.buttons === 3){
        setColor(x, y)
    }
}
</script>
