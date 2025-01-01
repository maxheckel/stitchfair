<script setup>
import {ref} from "vue";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import {Head} from "@inertiajs/vue3";
const currentYear = ref(new Date().getFullYear())

defineProps({
    title: String,
});
</script>

<template>

    <div class="min-h-screen bg-gradient-to-br from-indigo-200 to-pink-100 text-gray-800 font-sans">
        <!-- Header -->
        <header class="p-4 bg-white shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <ApplicationMark class="w-12"/> <h1 class="text-3xl font-bold text-indigo-600">StitchFair</h1>
                </div>

                <nav class="space-x-4">
                    <a href="#" class="hover:text-indigo-600 transition-colors">About</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Contact</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-12">
            <slot name="content"/>
        </main>

        <!-- Footer -->
        <footer class="bg-indigo-900 text-white py-8 mt-16">
            <div class="container mx-auto text-center">
                <p>&copy; {{ currentYear }} StitchFair. All rights reserved.</p>
                <div class="mt-4 space-x-4">
                    <a href="#" class="hover:text-indigo-300 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-indigo-300 transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-indigo-300 transition-colors">FAQ</a>
                </div>
            </div>
        </footer>
    </div>
</template>
