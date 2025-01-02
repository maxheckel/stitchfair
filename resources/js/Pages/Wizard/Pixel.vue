<template>
    <div @click="() => {console.log('here')}" class="block float-left hover:brightness-150 cursor-pointer"  :style="{backgroundColor: backgroundColor, width: zoom*2+'px', height: zoom*2+'px'}">

    </div>
</template>

<script setup>
    const props = defineProps({
        backgroundColor: String,
        zoom: Number
    })
</script>
