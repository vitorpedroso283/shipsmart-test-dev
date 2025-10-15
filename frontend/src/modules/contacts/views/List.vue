<template>
    <div class="bg-white shadow-sm rounded-xl p-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="text-2xl font-semibold text-gray-800">Contatos</h2>

            <div class="flex items-center gap-3">
                <span class="relative">
                    <InputText v-model="globalFilter" placeholder="Buscar..." class="pl-9 w-64 text-sm" />
                </span>
                <Button label="Novo" icon="pi pi-plus" @click="$router.push('/contacts/new')" />
            </div>
        </div>

        <!-- Tabela -->
        <DataTable v-model:selection="selectedItems" :value="store.items" :loading="store.loading" dataKey="id"
            paginator stripedRows responsiveLayout="scroll" class="p-datatable-sm" :rows="rows"
            :rowsPerPageOptions="rowsPerPageOptions" :totalRecords="store.total" lazy
            :filters="{ global: { value: globalFilter, matchMode: 'contains' } }"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} contatos">
            <Column selectionMode="multiple" headerStyle="width: 3rem" />

            <Column field="nome" header="Nome" sortable />
            <Column field="email" header="E-mail" sortable />
            <Column field="telefone" header="Telefone" />
            <Column field="cidade" header="Cidade" />
            <Column field="estado" header="UF" />

            <Column header="Ações" style="width: 8rem; text-align: center">
                <template #body="{ data }">
                    <div class="flex justify-center gap-2">
                        <Button icon="pi pi-pencil" text class="text-blue-600" @click="edit(data.id)" />
                        <Button icon="pi pi-trash" text severity="danger" class="text-red-600"
                            @click="confirmDelete(data.id)" />
                    </div>
                </template>
            </Column>

            <!-- botão de exportação -->
            <template #paginatorend>
                <Button icon="pi pi-download" label="Exportar CSV" text :disabled="!selectedItems.length"
                    @click="exportSelected" />
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue"
import { useRouter } from "vue-router"
import { useConfirm } from "primevue/useconfirm"
import { useToast } from "primevue/usetoast"
import Button from "primevue/button"
import DataTable from "primevue/datatable"
import Column from "primevue/column"
import InputText from "primevue/inputtext"
import { useContactsStore } from "../store"

const router = useRouter()
const store = useContactsStore()
const confirm = useConfirm()
const toast = useToast()

const globalFilter = ref("")
const rows = ref(10)
const selectedItems = ref([])

const rowsPerPageOptions = computed(() => {
    const options = [5, 10, 20, 50]
    if (store.total > 0) {
        options.push(store.total)
    }
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
        message: "Deseja realmente excluir este contato?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        acceptLabel: "Sim",
        rejectLabel: "Não",
        accept: async () => {
            await store.remove(id)
            toast.add({
                severity: "success",
                summary: "Contato excluído",
                detail: "O contato foi removido com sucesso.",
                life: 3000,
            })
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Ação cancelada",
                detail: "Nenhuma exclusão foi realizada.",
                life: 3000,
            })
        },
    })
}

// Simula exportação
function exportSelected() {
    const ids = selectedItems.value.map((i: any) => i.id).join(", ")
    toast.add({
        severity: "info",
        summary: "Exportar CSV",
        detail: `Exportando contatos selecionados: ${ids}`,
        life: 3000,
    })
}
</script>