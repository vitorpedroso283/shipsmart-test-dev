<template>
    <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-xl p-8 space-y-8">
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ isEdit ? t("contacts.edit") : t("contacts.new") }}
        </h2>

        <form @submit.prevent="handleSubmit(onSubmit)" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <InputText v-model="values.nome" :placeholder="t('contacts.name')" />
                    <small class="text-red-500">{{ errors.nome }}</small>
                </div>

                <div>
                    <InputText v-model="values.email" type="email" :placeholder="t('contacts.email')" />
                    <small class="text-red-500">{{ errors.email }}</small>
                </div>

                <div>
                    <InputMask v-model="values.telefone" mask="(99) 99999-9999" :placeholder="t('contacts.phone')" />
                    <small class="text-red-500">{{ errors.telefone }}</small>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex-1">
                        <InputMask v-model="values.cep" mask="99999-999" :placeholder="t('contacts.cep')"
                            @blur="fetchCep" />
                        <small class="text-red-500">{{ errors.cep }}</small>
                    </div>
                    <Button icon="pi pi-search" outlined type="button" :loading="loadingCep" @click="fetchCep" />
                </div>

                <div>
                    <Dropdown v-model="values.estado" :options="estados" optionLabel="label" optionValue="value"
                        :placeholder="t('contacts.state')" filter class="w-full" />
                    <small class="text-red-500">{{ errors.estado }}</small>
                </div>

                <div>
                    <InputText v-model="values.cidade" :placeholder="t('contacts.city')" />
                    <small class="text-red-500">{{ errors.cidade }}</small>
                </div>

                <div>
                    <InputText v-model="values.bairro" :placeholder="t('contacts.neighborhood')" />
                    <small class="text-red-500">{{ errors.bairro }}</small>
                </div>

                <div>
                    <InputText v-model="values.endereco" :placeholder="t('contacts.address')" />
                    <small class="text-red-500">{{ errors.endereco }}</small>
                </div>

                <div>
                    <InputText v-model="values.numero" :placeholder="t('contacts.number')" />
                    <small class="text-red-500">{{ errors.numero }}</small>
                </div>
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
import { useRoute, useRouter } from "vue-router"
import { useI18n } from "vue-i18n"
import { useContactsStore } from "@/modules/contacts/store"
import { estados } from "@/modules/contacts/data/estados"
import { useCep } from "@/composables/useCep"
import InputText from "primevue/inputtext"
import InputMask from "primevue/inputmask"
import Dropdown from "primevue/dropdown"
import Button from "primevue/button"
import { useToast } from "primevue/usetoast"
import { useForm } from "vee-validate"
import * as yup from "yup"
import type { Contact } from "@/modules/contacts/types"

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const store = useContactsStore()
const toast = useToast()

// ✅ Esquema Yup com base no tipo Contact
const contactSchema = yup.object({
    nome: yup.string().required(t("contacts.required")),
    email: yup.string().email(t("contacts.invalid_email")).required(t("contacts.required")),
    telefone: yup.string().required(t("contacts.required")),
    cep: yup.string().required(t("contacts.required")),
    estado: yup.string().required(t("contacts.required")),
    cidade: yup.string().required(t("contacts.required")),
    bairro: yup.string().required(t("contacts.required")),
    endereco: yup.string().required(t("contacts.required")),
    numero: yup.string().required(t("contacts.required")),
})

const { handleSubmit, errors, values, setValues } = useForm<Contact>({
    validationSchema: contactSchema,
    initialValues: {
        nome: "",
        email: "",
        telefone: "",
        cep: "",
        estado: "",
        cidade: "",
        bairro: "",
        endereco: "",
        numero: "",
    },
})

const { fetchCep, loadingCep } = useCep(values)
const isEdit = !!route.params.id

if (isEdit) {
    store.get(Number(route.params.id)).then((data) => {
        setValues(data)
    })
}

const onSubmit = handleSubmit(async (formData: Contact) => {
    try {
        if (isEdit) {
            await store.update(Number(route.params.id), formData)
        } else {
            await store.create(formData)
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
})
</script>
