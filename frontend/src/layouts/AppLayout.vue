<template>
    <div class="min-h-screen flex flex-col bg-gray-100 font-sans text-gray-800">
        <Navbar />

        <main class="flex-1 py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <router-view />
            </div>
        </main>

        <Toast />
        <ConfirmDialog />
    </div>
</template>

<script setup lang="ts">
import Navbar from "@/components/layout/Navbar.vue"
import Toast from "primevue/toast"
import ConfirmDialog from "primevue/confirmdialog"
</script>