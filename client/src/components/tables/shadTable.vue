<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import {ref} from 'vue'
import {useProductionStore} from '@/storage/production.js'
import moment from 'moment'
import Checkbox from 'primevue/checkbox';

const selectedProducts = ref([])
const production = useProductionStore()
const props = defineProps(['cols'])
const isOpen = ref(false)

const local_cols = props.cols

</script>

<template>

    <div>
        <div class="mb-5 relative flex justify-between">
            <button type="button" class="dark:bg-dark-900 h-11 w-40 flex rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @click="isOpen = !isOpen">
                Column Chooser
                <svg
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="transition"
                    :class="{ 'rotate-180': isOpen }"
                >
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <ul v-if="isOpen" class="absolute left-0 mt-13 p-2.5 min-w-[150px] bg-white rounded shadow-md space-y-1 z-20 dark:bg-dark-900 h-auto w-40 rounded-lg border border-gray-200 py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 dark:border-gray-800 dark:bg-gray-800 dark:text-white dark:placeholder:text-white/30 dark:focus:border-brand-800">
                <li v-for="col in cols" :key="col.field">
                    <label class="flex items-center gap-2 w-full cursor-pointer">
                        <input type="checkbox" class="form-checkbox cursor-pointer" :checked="!col.hide" @change="col.hide = !$event.target.checked" />
                        <span>{{ col.header }}</span>
                    </label>
                </li>
            </ul>
        </div>

        <DataTable v-model:selection="selectedProducts" dataKey="id" :value="production.products" stripedRows removableSort tableStyle="min-width: 50rem" class="custom-datatable">
            <Column selectionMode="multiple" headerStyle="width: 3rem">
            </Column>
            <div v-for="col of props.cols">
                <Column v-if="!col.hide" sortable :key="col.field" :field="col.field" :header="col.header" class="h-12">
                    <template #body="{ data }">
                        {{ 
                            col.field.includes('.') 
                            ? col.field.split('.').reduce((o, key) => o?.[key], data) 
                            : col.field === 'created_at' ? moment(data[col.field]).format('YYYY-MM-DD HH:mm')
                            : col.field === 'updated_at' ? moment(data[col.field]).format('YYYY-MM-DD HH:mm')
                            : data[col.field]
                        }}

                    </template>
                </Column>
            </div>
        </DataTable>
    </div>

</template>

<style>
.custom-datatable .p-datatable-tbody > tr > td {
  padding: 1rem 0.75rem;
  cursor: pointer;
}
</style>