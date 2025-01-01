<template>
    <h2 class="text-2xl font-bold text-indigo-800">Refine</h2>
    <div class="flex">
        <div class="overflow-scroll h-[800px] w-[50%]">
            <div :style="{height: zoomLevel*2+'px', width: pattern.width*zoomLevel*2+'px'}" v-for="y in pattern.height">
                <template v-for="x in pattern.width">
                    <div class="inline-block"  :style="{backgroundColor: variant.pixels[x+','+y], width: zoomLevel*2+'px', height: zoomLevel*2+'px'}">

                    </div>
                </template>
            </div>

        </div>

        <div class="w-1/2">
            oawigheoiaw
        </div>
    </div>

</template>

<script setup>
import {computed, onMounted, ref} from "vue";

const props = defineProps({
    variant: Object,
    pattern: Object
})
const currentColor = ref()
const zoomLevel = ref(2)
</script>
