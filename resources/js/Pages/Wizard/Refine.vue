<template>

    <Modal :show="showingAddToPalette">
        <div class="bg-white max-w-7xl p-4 relative">

            <div class="w-full grid grid-cols-8 gap-4 mt-2">
                <template v-for="thread in threads.sort((a, b) => a.hue_order - b.hue_order)">
                    <div  class="group text-center">
                        <div  :class="{'border-indigo-600 border-2 shadow-md': thread.hex === withColor}" @click="withColor = thread.hex" class="border border-gray-300 w-10 cursor-pointer h-10 rounded-md flex items-center justify-center" :style="{backgroundColor: thread.hex}">

                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="fixed top-0 ">
            awoghuueo
        </div>
    </Modal>

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
                        <template v-for="color in variant.palette.filter(val => val)">
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
    <div class="grid grid-cols-10 ">
        <div id="scrollable" :class="{'grabbable': currentTool === 'pan'}" class="col-span-8 overflow-scroll py-16"  style="max-height: calc(100vh - 250px)">
            <div :style="{height: zoomLevel*2+'px', width: pattern.width*zoomLevel*2+'px'}" v-for="y in pattern.height" class="mx-auto">
                <template v-for="x in pattern.width">
                    <Pixel :grid-color="gridColor" @mouseover="handleMouseOver(x, y, $event)" @dragstart="setColor(x,y)" @click="setColor(x, y)" :zoom="zoomLevel" :background-color="variant.pixels[x+','+y]"/>
                </template>
            </div>

        </div>

        <div class="relative col-span-2">
            <div class="absolute top-0 left-0 -ml-16">
                <div class="p-2 rounded-md border border-gray-300 cursor-pointer bg-white  mb-1" @click="zoomLevel++">
                    <ZoomIn/>
                </div>
                <div class="p-2 mb-1 rounded-md border border-gray-300 cursor-pointer bg-white " @click="zoomLevel > 1 ? zoomLevel-- : null">
                    <ZoomOut/>
                </div>

                <div class="p-2 rounded-md bg-white w-10 h-10 border border-gray-300 flex items-center justify-center" :style="{backgroundColor: currentTool === 'brush' || currentTool === 'fill' ? currentColor : 'white'}">
                    <Pipette v-if="currentTool === 'eyedropper'"/>
                    <span v-else-if="currentTool === 'clear'" class="items-center text-2xl text-red-600" style="font-family: Arial, SansSerif">
                        x
                    </span>
                    <Hand v-else-if="currentTool === 'pan'"/>
                    <PaintBucket v-else-if="currentTool === 'fill'" style="mix-blend-mode: difference; color: white;"/>
                    <Brush style="mix-blend-mode: difference; color: white;" v-else/>
                </div>
            </div>

            <div class="px-8 ">
                <div class="my-4">
                    Tools
                    <div class="grid grid-cols-6">
                        <div :class="{'border border-indigo-600': currentTool === 'brush'}" class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center rounded-md" @click="currentTool = 'brush'">
                            <Brush/>
                        </div>
                        <div :class="{'border border-indigo-600': currentTool === 'eyedropper'}" class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center rounded-md" @click="currentTool = 'eyedropper'">
                            <Pipette/>
                        </div>
                        <div :class="{'border border-indigo-600': currentTool === 'pan'}" class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center rounded-md" @click="currentTool = 'pan'">
                            <Hand/>
                        </div>

                        <div :class="{'border border-indigo-600': currentTool === 'fill'}" class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center rounded-md" @click="currentTool = 'fill'">
                            <PaintBucket/>
                        </div>

                    </div>

                </div>

                Palette
                <div class="grid grid-cols-6 gap-4">
                    <div v-for="color in variant.palette.filter(val => val)" class="group text-center">
                        <div @click="() => {currentColor = color}" :class="{'border-indigo-600 border-2': currentColor === color}" class="border border-gray-300 w-10 cursor-pointer h-10 rounded-md flex items-center justify-center" :style="{backgroundColor: color}">
                        </div>
                        <div @click="replacingColor = color" class="underline cursor-pointer text-indigo-600 hidden text-xs group-hover:block">
                            swap
                        </div>
                    </div>
                    <div class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center text-2xl text-red-600 rounded-md" style="font-family: Arial, SansSerif" @click="currentTool = 'clear'">
                        x
                    </div>
                    <div class="w-10 cursor-pointer h-10 flex justify-center border border-gray-300 items-center text-2xl text-indigo-600 rounded-md" style="font-family: Arial, SansSerif" @click="showingAddToPalette = true">
                        +
                    </div>
                </div>

                <div class="mt-4">
                    Grid Color
                    <input class="block" v-model="gridColor" type="color"/>
                </div>

            </div>

        </div>
    </div>

</template>

<script setup>
import {computed, onMounted, ref, watch} from "vue";
import Pixel from "@/Pages/Wizard/Pixel.vue";
import {ZoomIn, ZoomOut, Hand, Pipette, Brush, PaintBucket} from "lucide-vue-next";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    variant: Object,
    pattern: Object,
    threads: Array
})


const currentColor = ref()
const currentTool = ref('brush')
const zoomLevel = ref(5)
const replacingColor = ref()
const withColor = ref()
const showingAddToPalette = ref(false)
let saveTimeout = null
const gridColor = ref('#5c5c5c')

const setColor = (x, y) => {
    if (currentTool.value === 'fill'){
        fill(x, y, currentColor.value)
    }
    if (currentTool.value === 'pan'){
        return;
    }
    if (currentTool.value === 'clear'){
        props.variant.pixels[x+','+y]=null
        return
    }
    if (currentTool.value === 'eyedropper'){
        currentColor.value = props.variant.pixels[x+','+y]
        currentTool.value = 'brush'
        return
    }
    if (!currentColor.value){
        return;
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

setTimeout(() => {
    const scrollableDiv = document.getElementById('scrollable');

    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;

    scrollableDiv.addEventListener('mousedown', (e) => {
        isDragging = true;
        scrollableDiv.classList.add('dragging');
        startX = e.pageX - scrollableDiv.offsetLeft;
        startY = e.pageY - scrollableDiv.offsetTop;
        scrollLeft = scrollableDiv.scrollLeft;
        scrollTop = scrollableDiv.scrollTop;
        e.preventDefault();
    });

    scrollableDiv.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const x = e.pageX - scrollableDiv.offsetLeft;
        const y = e.pageY - scrollableDiv.offsetTop;
        const walkX = (x - startX) * 2; // Adjust for sensitivity
        const walkY = (y - startY) * 2;
        scrollableDiv.scrollLeft = scrollLeft - walkX;
        scrollableDiv.scrollTop = scrollTop - walkY;
    });

    scrollableDiv.addEventListener('mouseup', () => {
        isDragging = false;
        scrollableDiv.classList.remove('dragging');
    });

    scrollableDiv.addEventListener('mouseleave', () => {
        isDragging = false;
        scrollableDiv.classList.remove('dragging');
    });

}, 1000)
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

const fill = (startX, startY, targetColor) => {
    const startColor = props.variant.pixels[`${startX},${startY}`];
    if (!startColor || startColor === targetColor) {
        // If the starting color is undefined or already the target color, do nothing
        return;
    }

    const queue = [[startX, startY]]; // Initialize queue with the starting point

    while (queue.length > 0) {
        const [x, y] = queue.shift(); // Get the next coordinate from the queue
        const key = `${x},${y}`;

        if (props.variant.pixels[key] === startColor) {
            // Change the color
            props.variant.pixels[key] = targetColor;

            // Check adjacent cells (4-connected grid)
            queue.push([x + 1, y]);
            queue.push([x - 1, y]);
            queue.push([x, y + 1]);
            queue.push([x, y - 1]);
        }
    }
}
</script>

<style>
.grabbable *{
    cursor: grab;
}
</style>
