<template>
    <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-xl p-8 space-y-8">
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ isEdit ? t("contacts.edit") : t("contacts.new") }}
        </h2>

        <form @submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <InputText v-model="form.nome" :placeholder="t('contacts.name')" />
                <InputText v-model="form.email" type="email" :placeholder="t('contacts.email')" />
                <InputMask v-model="form.telefone" mask="(99) 99999-9999" :placeholder="t('contacts.phone')" />

                <div class="flex items-center gap-2">
                    <InputMask v-model="form.cep" mask="99999-999" :placeholder="t('contacts.cep')" class="flex-1"
                        @blur="fetchCep" />
                    <Button icon="pi pi-search" outlined type="button" :loading="loadingCep" @click="fetchCep" />
                </div>

                <Select v-model="form.estado" :options="estados" optionLabel="label" optionValue="value"
                    :placeholder="t('contacts.state')" class="w-full" filter showClear />

                <InputText v-model="form.cidade" :placeholder="t('contacts.city')" />
                <InputText v-model="form.bairro" :placeholder="t('contacts.neighborhood')" />
                <InputText v-model="form.endereco" :placeholder="t('contacts.address')" />
                <InputText v-model="form.numero" :placeholder="t('contacts.number')" />
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <Button :label="t('contacts.cancel')" severity="secondary" outlined
                    @click="$router.push('/contacts')" />
                <Button :label="t('contacts.save')" type="submit" />
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import { useI18n } from "vue-i18n"
import { useContactsStore } from "@/modules/contacts/store"
import { estados } from "@/modules/contacts/data/estados"
import { useCep } from "@/composables/useCep"
import InputText from "primevue/inputtext"
import InputMask from "primevue/inputmask"
import Select from "primevue/select" 
import Button from "primevue/button"
import { useToast } from "primevue/usetoast"

// ✅ Tipo Contact
export interface Contact {
    id?: number
    nome: string
    email: string
    telefone: string
    cep: string
    estado: string
    cidade: string
    bairro: string
    endereco: string
    numero: string
}

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const store = useContactsStore()
const toast = useToast()

const form = ref<Contact>({
    nome: "",
    email: "",
    telefone: "",
    cep: "",
    estado: "",
    cidade: "",
    bairro: "",
    endereco: "",
    numero: "",
})

const { fetchCep, loadingCep } = useCep(form)
const isEdit = !!route.params.id

onMounted(async () => {
    if (isEdit) {
        const data = await store.get(Number(route.params.id))
        form.value = data as Contact
    }
})

async function save() {
    try {
        if (isEdit) {
            await store.update(Number(route.params.id), form.value)
        } else {
            await store.create(form.value)
        }

        toast.add({
            severity: "success",
            summary: t("contacts.success"),
            detail: t("contacts.saved_success"),
            life: 2500,
        })

        router.push("/contacts")
    } catch (error: any) {
        const message = error.response?.data?.message || t("contacts.error_generic")

        const errors = error.response?.data?.errors
        if (errors && typeof errors === "object") {
            Object.entries(errors).forEach(([field, messages]) => {
                toast.add({
                    severity: "warn",
                    summary: `${t("contacts.field")}: ${field}`,
                    detail: (messages as string[]).join(", "),
                    life: 5000,
                })
            })
        } else {
            toast.add({
                severity: "error",
                summary: t("contacts.error"),
                detail: message,
                life: 5000,
            })
        }
    }
}
</script>