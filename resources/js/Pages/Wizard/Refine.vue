<template>
    <h2 class="text-2xl font-bold text-indigo-800">Refine</h2>
    <div class="w-full m-0 h-2 mt-0 block" v-for="y in pattern.height">
        <template v-for="x in pattern.width">
            <div class="inline-block w-2 h-2"  :style="{backgroundColor: pixelsRef[x+','+y]}">

            </div>
        </template>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";

const props = defineProps({
    variant: Object,
    pattern: Object
})
const pixelsRef = computed(() => {
    const pixels = {}
    props.variant.pixels.forEach((pixel) => {
        pixels[pixel.x+','+pixel.y] = pixel.hex
    })
    return pixels
})

console.log(props.variant)
</script>
