<template>
    <div class="bg-white shadow-sm rounded-xl p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ t("contacts.title") }}
            </h2>

            <div class="flex items-center gap-3">
                <InputText v-model="globalFilter" :placeholder="t('contacts.search')" class="pl-9 w-64 text-sm" />
                <Button :label="t('contacts.new')" icon="pi pi-plus" @click="$router.push('/contacts/new')" />
            </div>
        </div>

        <DataTable v-model:selection="selectedItems" :value="store.items" :loading="store.loading" dataKey="id"
            paginator stripedRows responsiveLayout="scroll" class="p-datatable-sm" :rows="rows"
            :rowsPerPageOptions="rowsPerPageOptions" :totalRecords="store.total" lazy
            :filters="{ global: { value: globalFilter, matchMode: 'contains' } }"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
            :currentPageReportTemplate="t('contacts.showing', { first: '{first}', last: '{last}', totalRecords: '{totalRecords}' })">
            <Column selectionMode="multiple" headerStyle="width: 3rem" />
            <Column field="nome" :header="t('contacts.name')" sortable />
            <Column field="email" :header="t('contacts.email')" sortable />
            <Column field="telefone" :header="t('contacts.phone')" />
            <Column field="cidade" :header="t('contacts.city')" />
            <Column field="estado" :header="t('contacts.state')" />

            <Column :header="t('contacts.actions')" style="width: 8rem; text-align: center">
                <template #body="{ data }">
                    <div class="flex justify-center gap-2">
                        <Button icon="pi pi-pencil" text class="text-blue-600" @click="edit(data.id)" />
                        <Button icon="pi pi-trash" text severity="danger" class="text-red-600"
                            @click="confirmDelete(data.id)" />
                    </div>
                </template>
            </Column>

            <template #paginatorend>
                <Button icon="pi pi-download" :label="t('contacts.export')" text :disabled="!selectedItems.length"
                    @click="exportSelected" />
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue"
import { useRouter } from "vue-router"
import { useI18n } from "vue-i18n"
import { useConfirm } from "primevue/useconfirm"
import { useToast } from "primevue/usetoast"
import Button from "primevue/button"
import DataTable from "primevue/datatable"
import Column from "primevue/column"
import InputText from "primevue/inputtext"
import { useContactsStore } from "../store"

const { t } = useI18n()
const router = useRouter()
const store = useContactsStore()
const confirm = useConfirm()
const toast = useToast()

const globalFilter = ref("")
const rows = ref(10)
const selectedItems = ref([])

const rowsPerPageOptions = computed(() => {
    const options = [5, 10, 20, 50]
    if (store.total > 0) options.push(store.total)
    return options
})

onMounted(() => store.fetchAll())

watch([rows, globalFilter], () => {
    const perPage = rows.value === store.total ? store.total : rows.value
    store.fetchAll({
        search: globalFilter.value,
        perPage,
    })
})

function edit(id: number) {
    router.push(`/contacts/${id}/edit`)
}

function confirmDelete(id: number) {
    confirm.require({
        message: t("contacts.confirm_delete"),
        header: t("contacts.confirm_title"),
        icon: "pi pi-exclamation-triangle",
        acceptLabel: t("contacts.yes"),
        rejectLabel: t("contacts.no"),
        accept: async () => {
            await store.remove(id)
            toast.add({
                severity: "success",
                summary: t("contacts.deleted_title"),
                detail: t("contacts.deleted_success"),
                life: 3000,
            })
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: t("contacts.cancelled_title"),
                detail: t("contacts.cancelled_action"),
                life: 3000,
            })
        },
    })
}

function exportSelected() {
    const ids = selectedItems.value.map((i: any) => i.id).join(", ")
    toast.add({
        severity: "info",
        summary: t("contacts.export"),
        detail: `${t("contacts.exporting")}: ${ids}`,
        life: 3000,
    })
}
</script>