<script setup lang="ts">
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import { useContactsStore } from "@/modules/contacts/store"
import { estados } from "@/modules/contacts/data/estados"
import { useCep } from "@/composables/useCep"
import InputText from "primevue/inputtext"
import InputMask from "primevue/inputmask"
import Dropdown from "primevue/dropdown"
import Button from "primevue/button"
import { useToast } from "primevue/usetoast"

const route = useRoute()
const router = useRouter()
const store = useContactsStore()
const toast = useToast()

const form = ref({
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
        Object.assign(form.value, data)
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
            summary: "Sucesso",
            detail: "Contato salvo com sucesso!",
            life: 2500,
        })

        router.push("/contacts")
    } catch (error: any) {
        const message =
            error.response?.data?.message || "Ocorreu um erro ao salvar o contato."

        const errors = error.response?.data?.errors
        if (errors && typeof errors === "object") {
            Object.entries(errors).forEach(([field, messages]) => {
                toast.add({
                    severity: "warn",
                    summary: `Campo: ${field}`,
                    detail: (messages as string[]).join(", "),
                    life: 5000,
                })
            })
        } else {
            toast.add({
                severity: "error",
                summary: "Erro",
                detail: message,
                life: 5000,
            })
        }
    }
}
</script>

<template>
    <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-xl p-8 space-y-8">
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ isEdit ? "Editar contato" : "Novo contato" }}
        </h2>

        <form @submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <InputText v-model="form.nome" placeholder="Nome" />
                <InputText v-model="form.email" type="email" placeholder="E-mail" />
                <InputMask v-model="form.telefone" mask="(99) 99999-9999" placeholder="Telefone" />

                <div class="flex items-center gap-2">
                    <InputMask v-model="form.cep" mask="99999-999" placeholder="CEP" class="flex-1" @blur="fetchCep" />
                    <Button icon="pi pi-search" outlined type="button" :loading="loadingCep" @click="fetchCep"
                        class="shrink-0" />
                </div>

                <Dropdown v-model="form.estado" :options="estados" optionLabel="label" optionValue="value"
                    placeholder="Estado" filter class="w-full" />
                <InputText v-model="form.cidade" placeholder="Cidade" />
                <InputText v-model="form.bairro" placeholder="Bairro" />
                <InputText v-model="form.endereco" placeholder="Endereço" />
                <InputText v-model="form.numero" placeholder="Número" />
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <Button label="Cancelar" severity="secondary" outlined @click="$router.push('/contacts')" />
                <Button label="Salvar" type="submit" />
            </div>
        </form>
    </div>
</template>
