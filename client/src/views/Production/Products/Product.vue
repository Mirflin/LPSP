<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { onMounted, onUnmounted, reactive, ref, computed, toRaw, watch, isRef, isReactive, unref } from 'vue'
import moment from 'moment'
import { useProductionStore } from '@/storage/production'
import { useAuthStore } from '@/storage/auth'
import ProductsTable from './ProductsTable.vue'
import {useClientsStore} from '@/storage/clients.js'
import { Button } from "@/components/ui/button"
import TimedAlert from '@/components/TimedAlert.vue'
import ProductInfo from './ProductInfo.vue'
import RefreshIcon from '@/icons/RefreshIcon.vue'
import Vue3Datatable from '@bhplugin/vue3-datatable'
import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"
import PlusIcon from '@/icons/PlusIcon.vue'


const production = useProductionStore()
const loading = ref(false)
const alert_type = ref(null)
const alert_message = ref(null)
const clients = useClientsStore()
const row = ref()
const rows = ref()
const total_rows = ref(0)
const openModalView = ref()
const isOpen = ref(false)

const datatable = ref()
const params = reactive({
  current_page: 1,
  pagesize: 10,
  sort_column: "id",
  sort_direction: "asc",
  search: ""
})

onMounted(async() => {
  production.fetch()
  await fetchData()
})

const cols = ref([
  { field: 'id', title: 'ID' },
  { field: 'drawing_nr', title: 'Drawing number', hide: false },
  { field: 'part_nr', title: 'Part number', hide: false },
  { field: 'revision', title: 'Revision', hide: false },
  { field: 'description', title: 'Description', hide: false },
  { field: 'additional_info', title: 'Additional info', hide: false },
  { field: 'weight', title: 'Weight', hide: true },
  { field: 'client.name', title: 'Client', hide: false },
  { field: 'updated_at', title: 'Updated at', hide: true },
  { field: 'created_at', title: 'Created at', hide: true },
])

const update_row = (updated_row) => {
  row.value = updated_row
  console.log(row.value)
  openModalView.value = true
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await production.fetchProducts({
      page: params.current_page,
      perPage: params.pagesize,
      sort: params.sort_column,
      direction: params.sort_direction,
      search: params.search,
    })

    rows.value = production.products
    total_rows.value = production.total_products
  } finally {
    loading.value = false
  }
}

const changeServer = (data) => {
  params.current_page = data.current_page
  params.pagesize = data.pagesize
  params.sort_column = data.field
  params.sort_direction = data.direction
  fetchData()
}

const exportTable = () => {
    let records = datatable.value.getSelectedRows()
    let columns = datatable.value.getColumnFilters()
    if (!records?.length) {
        records = production.products
    }
    const filename = 'Products_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime();
    const coldelimiter = ','
    const linedelimiter = '\n'
    let result = cols.value
        .filter((d) => !d.hide)
        .map((d) => {
            return d.title
        })
        .join(coldelimiter)
    result += linedelimiter
    records.map((item) => {
        cols.value
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
            delete item['name']
            delete item.client_id
            columns.forEach(colum => {
                if(colum.hide){
                    delete item[colum.field]
                }
                if(colum.title == "Client"){
                    item.client = item.client.name
                }
                if(columns.title == "Children"){
                  delete item.children
                }
                if(columns.title == "Files"){
                  delete item.files
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
        XLSX.utils.book_append_sheet(wb, ws, 'Products')

        const COL_WIDTH = 100;
        let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9]
        if(!ws["!cols"]) ws["!cols"] = []

        COL_INDEXES.forEach(COL_INDEX => {
            if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
            ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
        });

        if(!ws["!rows"]) ws["!rows"] = []

        if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
        ws["!rows"][0].hpx = 30;

        ws['!autofilter'] = { ref:"A1:I1" };

        XLSX.writeFile(wb, filename+'.xlsx', {cellStyles: true})
};


const currentPageTitle = 'Products'
</script>

<template>
    <AdminLayout>
      <TimedAlert
        :type="alert_type"
        :message="alert_message"
      />
      
      <PageBreadcrumb :pageTitle="currentPageTitle" />

      <Modal v-if="openModalView">
        <template #body>
          <ProductInfo @update="update_row" @close="openModalView = false" @open="openModalView = true" :product="row"></ProductInfo>
        </template>
      </Modal>

      <div>
        <div class="mb-5 relative flex justify-between">
          <div class="flex gap-5 items-center">
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
            <Button @click="exportTable" class="bg-green-500 hover:bg-green-300">Export to excel</Button>
          </div>
            <div class="flex items-center gap-5">
              <RouterLink to="/product-create">
                <PlusIcon class="text-green-500 scale-300 rounded-md hover:bg-gray-100 transition"></PlusIcon>
              </RouterLink>
              <button @click="fetchData; datatable.reset()" class="p-2 rounded-md hover:bg-gray-100 transition">
                <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition" />
              </button>
              <input
                type="text"
                v-model="params.search"
                placeholder="Global search"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>

            <ul v-if="isOpen" class="absolute left-0 mt-13 p-2.5 min-w-[150px] bg-white rounded shadow-md space-y-1 z-20 dark:bg-dark-900 h-auto w-40 rounded-lg border border-gray-200 py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 dark:border-gray-800 dark:bg-gray-800 dark:text-white dark:placeholder:text-white/30 dark:focus:border-brand-800">
              <li v-for="col in cols" :key="col.field">
                  <label class="flex items-center gap-2 w-full cursor-pointer">
                      <input type="checkbox" class="form-checkbox" :checked="!col.hide" @change="col.hide = !$event.target.checked" />
                      <span>{{ col.title }}</span>
                  </label>
              </li>
            </ul>
        </div>

        <vue3-datatable
            ref="datatable"
            :rows="rows"
            :columns="cols"
            :totalRows="total_rows"
            :loading="loading"
            :pagination="true"
            :pageSize="params.pageSize"
            skin="bh-table-compact"
            :search="params.search"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :isServerMode="true"
            :rowClass="'text-sm text-gray-800 dark:text-gray-300 text-center hover:bg-gray-100 hover:cursor-pointer'" 
            @rowClick="update_row"
            @change="changeServer"
        >
            <template #created_at="data">
                <span class="text-gray-500">{{ data.value.created_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
            <template #updated_at="data">
                <span class="text-gray-500">{{ data.value.updated_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
        </vue3-datatable>
      </div>

    </AdminLayout>
</template>

<style scoped> 

</style>