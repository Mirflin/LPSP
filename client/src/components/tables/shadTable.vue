<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import {ref, isReactive, isRef, unref, toRaw, onMounted, watch, computed} from 'vue'
import {useProductionStore} from '@/storage/production.js'
import moment from 'moment'
import Checkbox from 'primevue/checkbox';
import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"
import {useClientsStore} from '@/storage/clients.js'
import Input from '../ui/input/Input.vue';
import RefreshIcon from '@/icons/RefreshIcon.vue';
import PlusIcon from '@/icons/PlusIcon.vue';
import TimedAlert from '../TimedAlert.vue';
import { FilterIcon } from 'lucide-vue-next';

const selectedProducts = ref([])
const production = useProductionStore()
const props = defineProps(['cols'])
const isOpen = ref(false)
const loading = ref(false)
const filters = ref({
    global: { value: null},
    drawing_nr: { value: null},
    part_nr: { value: null},
    client: { value: null},
    id: { value: null},
});

const parse_to_excel = async() => {
    let records = production.products
    let columns = props.cols
    if (!records?.length) {
        records = rows.value3
    }

    const coldelimiter = ','
    const linedelimiter = '\n'
    let result = props.cols
        .filter((d) => !d.hide)
        .map((d) => {
            return d.title
        })
        .join(coldelimiter)
    result += linedelimiter
    records.map((item) => {
        props.cols
            .filter((d) => !d.hide)
            .map((d, index) => {
                if (index > 0) {
                    result += coldelimiter
                }
                const val = d.field.split('.').reduce((acc, part) => acc && acc[part], item)
                result += val
            });
        result += linedelimiter
    });

    if (result === null) return;

    const plainData = records.map(item => {
        columns.forEach(colum => {
            if(colum.header == "Client"){
                delete item.client_id
                delete item.name
                item.client = item.client.name
            }
        });
        if(isRef(item)) {
            return toRaw(unref(item))
        }
        if(isReactive(item)) {
            return toRaw(item)
        }
        return item
    })
    let ws = XLSX.utils.json_to_sheet(plainData);
    let wb = XLSX.utils.book_new() 
    console.log(ws)
    XLSX.utils.book_append_sheet(wb, ws, 'Clients')

    const COL_WIDTH = 100;
    let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9, 10,11,12,13]
    if(!ws["!cols"]) ws["!cols"] = []

    COL_INDEXES.forEach(COL_INDEX => {
        if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
        ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
    });

    if(!ws["!rows"]) ws["!rows"] = []

    if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
    ws["!rows"][0].hpx = 30;

    ws['!autofilter'] = { ref:"A1:Q1" };

    XLSX.writeFile(wb, 'clients.xlsx', {cellStyles: true})
}

onMounted(async() => {
    if(!production.products.length){
        fetch()
    }
})

const fetch = async() => {
    loading.value = true
    const response = await production.fetch()
    if(response){
        console.error("Error fetching prod!")
    }
    loading.value = false
}

const clearFilter = () => {
    filters.value = {
        global: { value: null},
        drawing_nr: { value: null},
        part_nr: { value: null},
        client: { value: null},
        id: { value: null},
    };
};

</script>

<template>
    <div>
        <div class="mb-5 relative flex justify-between">
            <div>
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
                    <li v-for="col in props.cols" :key="col.field">
                        <label class="flex items-center gap-2 w-full cursor-pointer">
                            <input type="checkbox" class="form-checkbox cursor-pointer" :checked="!col.hide" @change="col.hide = !$event.target.checked" />
                            <span>{{ col.header }}</span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mb-5 flex justify-between pl-4 pr-4">
            <Button class="bg-green-400" @click="parse_to_excel">EXCEL</Button>
            <div class="flex items-center gap-5">
                <Button type="button" label="Clear" variant="outlined" @click="clearFilter">
                    <FilterIcon></FilterIcon>
                </Button>
                <RouterLink to="/product-create">
                    <PlusIcon class="w-9 h-auto  text-green-500 hover:text-green-700 transition" />
                </RouterLink>
                <Button class="bg-white hover:bg-gray-100" @click="fetch">
                    <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition cursor-pointer" />
                </Button>
            </div>
        </div>

        <DataTable v-model:filters="filters" filterDisplay="menu" scrollable :globalFilterFields="['drawing_nr', 'part_nr', 'client.name', 'id', 'created_at', 'updated_at']" v-model:selection="selectedProducts" dataKey="id" :value="production.products" removableSort tableStyle="min-width: 50rem" :loading="loading" class="custom-datatable" v-if="!loading">
            <template #header>
                <div class="flex justify-start p-2">
                    <div class="flex items-center w-full">
                        <p class="w-35">Global search: </p>
                        <Input v-model="filters.global.value" placeholder="Keyword Search" />
                    </div>
                </div>
            </template>

            <template #empty> No customers found. </template>
            <template #loading> Loading customers data. Please wait. </template>

            <div v-for="col of props.cols">
                <Column v-if="!col.hide" sortable :key="col.field" filterField="" :field="col.field" :header="col.header" class="h-10">
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

        <div 
          v-else
          class="flex justify-center items-center"
        >
          <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
              <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full">
              </div>
          </div>
      </div>
    </div>

</template>

<style>
.custom-datatable .p-datatable-tbody > tr > td {
  padding: 0.5rem 0.75rem;
}
.custom-datatable .p-datatable-tbody .p-row-even{
    cursor: pointer;
}
.custom-datatable .p-datatable-tbody .p-row-odd{
    cursor: pointer;
}

.custom-datatable .p-datatable-tbody .p-row-even:hover{
    background-color: rgb(219, 219, 219);
}
.custom-datatable .p-datatable-tbody .p-row-odd:hover{
    background-color: rgb(219, 219, 219);
}
.custom-datatable .p-datatable-thead{
    background-color: white !important;
}
</style>