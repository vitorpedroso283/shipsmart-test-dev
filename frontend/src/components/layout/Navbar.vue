<template>
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-gray-900 text-xl">ShipSmart CRM</span>
                </div>

                <div class="flex-1 flex justify-start items-center ml-10 space-x-4">
                    <router-link to="/contacts"
                        class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-3 py-2 rounded-md font-medium text-sm">
                        {{ t("contacts.title") }}
                    </router-link>
                </div>

                <div class="flex items-center">
                    <Select v-model="locale" :options="languages" optionLabel="label" optionValue="value"
                        class="w-28 text-sm" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Select from "primevue/select"
import { useI18n } from "vue-i18n"

const { t, locale } = useI18n()

const languages = [
    { label: "🇧🇷 PT", value: "pt" },
    { label: "🇺🇸 EN", value: "en" },
]
</script>