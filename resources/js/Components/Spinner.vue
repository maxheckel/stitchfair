<template>
    <!-- The outer container gives us a 3D perspective -->
    <div class="scene">
        <!-- The wrapper is what we spin/rotate in 3D -->
        <div
            class="wrapper"
            :style="{ animationDuration: rotateDuration + 's' }"
        >
            <!-- Generate N slices, each masked to the PNG shape -->
            <div
                v-for="sliceIndex in slicesArray"
                :key="sliceIndex"
                class="slice"
                :style="getSliceStyle(sliceIndex)"
            ></div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Spinner',
    props: {
        imageUrl: {
            type: String,
            required: true
        },
        /**
         * Number of stacked slices to use.
         * More slices = smoother "thick" illusion, but more DOM overhead.
         */
        slices: {
            type: Number,
            default: 10
        },
        /**
         * Depth (in px) between each slice.
         */
        sliceDepth: {
            type: Number,
            default: 1
        },
        /**
         * Duration (in seconds) for one full rotation
         */
        rotateDuration: {
            type: Number,
            default: 5
        },
        /**
         * Width/Height of each slice. Adjust as needed or make them auto.
         * If you want a responsive size, handle that in CSS or additional props.
         */
        sliceWidth: {
            type: Number,
            default: 200
        },
        sliceHeight: {
            type: Number,
            default: 200
        }
    },
    computed: {
        /**
         * We'll just generate an array [0, 1, 2, ..., slices-1]
         */
        slicesArray() {
            return Array.from({ length: this.slices }, (_, i) => i)
        }
    },
    methods: {
        /**
         * Return a style object for each slice:
         *  - Place it behind the previous one using translateZ
         *  - Apply the mask and background from the PNG
         *  - The rest is handled in <style> below, but we do the inline
         *    portion here so you can pass in dynamic props if desired.
         */
        getSliceStyle(index) {
            return {
                transform: `translateZ(${index * this.sliceDepth}px)`,
                width: this.sliceWidth + 'px',
                height: this.sliceHeight + 'px',
                backgroundImage: `url('${this.imageUrl}')`,
                WebkitMaskImage: `url('${this.imageUrl}')`,
                maskImage: `url('${this.imageUrl}')`
            }
        }
    }
}
</script>

<style scoped>
/* Give a 3D perspective from the parent */
.scene {
    perspective: 800px; /* Adjust for more or less perspective */
    width: fit-content; /* Or a fixed width, up to you */
    height: fit-content;
    margin: 1rem auto;
}

/* This holds all slices and rotates in 3D */
.wrapper {
    position: relative;
    animation: spin 5s infinite linear;
    transform: rotateX(5deg); /* never purely flat */
    transform-style: preserve-3d;
}

/* Each slice is absolutely positioned at the top-left corner */
.slice {
    position: absolute;
    top: 0;
    left: 0;

    /* Show the PNG as a background and also mask it
       so only the non-transparent parts show. */
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;

    /* CSS masking to preserve the shape of the PNG */
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-position: center;
    -webkit-mask-size: contain;
    mask-repeat: no-repeat;
    mask-position: center;
    mask-size: contain;
}

/* One full spin from 0deg to 360deg */
@keyframes spin {
    0% {
        /* rotate a bit forward on X axis so it isn't flat at 0 deg Y */
        transform: rotateY(0deg) rotateX(5deg);
    }
    50% {
        transform: rotateY(180deg) rotateX(5deg);
    }
    100% {
        transform: rotateY(360deg) rotateX(5deg);
    }
}
</style>
