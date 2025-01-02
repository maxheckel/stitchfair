<template>

    <Head>
        <title>Cross Stitch Pattern Generator</title>
        <meta name="description" content="Transform Images into Cross-Stitch Patterns">
    </Head>
    <LandingLayout>
        <template #content>
            <div class="container mx-auto px-4 py-12">
                <!-- Hero Section -->
                <section class="text-center mb-16">
                    <h2 class="text-5xl font-bold mb-4 text-indigo-800">Transform Images into Cross-Stitch Patterns</h2>
                    <p class="text-xl mb-8 text-gray-600">Upload your photos and watch them become beautiful
                        cross-stitch patterns in seconds.</p>
                    <a
                        :href="route('pattern.wizard')"
                        class="inline-block px-8 py-4 bg-indigo-600 text-white rounded-full text-xl font-semibold hover:bg-indigo-700 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                    >
                        Start Your Pattern
                    </a>
                </section>

                <!-- Feature Highlights -->
                <section class="grid md:grid-cols-3 gap-12 mb-16">
                    <div
                        class="bg-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                        <UploadCloud class="w-16 h-16 text-indigo-500 mx-auto mb-4"/>
                        <h3 class="text-2xl font-bold mb-2 text-indigo-700">Easy Upload</h3>
                        <p class="text-gray-600">Upload any image to StitchFair, we'll convert it to the proper file
                            format and remove the background.</p>
                    </div>
                    <div
                        class="bg-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                        <Grid class="w-16 h-16 text-indigo-500 mx-auto mb-4"/>
                        <h3 class="text-2xl font-bold mb-2 text-indigo-700">Custom Patterns</h3>
                        <p class="text-gray-600">Choose your width, height and color count. Choose your favorite pattern
                            from our generated images.</p>
                    </div>
                    <div
                        class="bg-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                        <Download class="w-16 h-16 text-indigo-500 mx-auto mb-4"/>
                        <h3 class="text-2xl font-bold mb-2 text-indigo-700">Instant Download</h3>
                        <p class="text-gray-600">Download your pattern immediately and start stitching!</p>
                    </div>
                </section>

                <!-- Traditional Element -->
                <section class="mb-16">
                    <div class="border-4 border-indigo-200 p-8 rounded-lg relative">
                        <div
                            class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-indigo-500 -mt-4 -ml-4"></div>
                        <div
                            class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-indigo-500 -mt-4 -mr-4"></div>
                        <div
                            class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-indigo-500 -mb-4 -ml-4"></div>
                        <div
                            class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-indigo-500 -mb-4 -mr-4"></div>

                        <h2 class="text-3xl font-bold mb-4 text-center text-indigo-800">Why Choose StitchFair?</h2>
                        <ul class="list-disc list-inside text-gray-700 space-y-2">
                            <li>Fully customizable colors, width and height</li>
                            <li>Download the pattern as a PDF, get links to purchase each thread.</li>
                            <li>Choose between multiple variants of your image to get the best palette and pattern for
                                your needs.
                            </li>
                            <li>User-friendly interface for seamless experience</li>

                            <li>Dedicated customer support for all your stitching needs</li>
                        </ul>
                    </div>
                </section>

                <!-- Call to Action -->
                <section class="text-center">
                    <h2 class="text-4xl font-bold mb-4 text-indigo-800">Ready to Stitch Your Memories?</h2>
                    <p class="text-xl mb-8 text-gray-600">Join thousands of crafters who have already turned their
                        photos into beautiful cross-stitch art.</p>
                    <a
                        :href="route('pattern.wizard')"
                        class="inline-block px-8 py-4 bg-indigo-600 text-white rounded-full text-xl font-semibold hover:bg-indigo-700 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                    >
                        Create Your Pattern Now
                    </a>
                </section>
            </div>
        </template>
    </LandingLayout>


</template>
<script setup>
import {Download, Grid, UploadCloud} from "lucide-vue-next";
import LandingLayout from "@/Layouts/LandingLayout.vue";
import {Head} from "@inertiajs/vue3";
</script>
