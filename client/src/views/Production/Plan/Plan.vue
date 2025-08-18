<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue';
import { ref, reactive, onMounted } from "vue"
import Vue3Datatable from "@bhplugin/vue3-datatable"
import { useProductionStore } from "@/storage/production"
import '@bhplugin/vue3-datatable/dist/style.css'
import RefreshIcon from '../../../icons/RefreshIcon.vue';

const production = useProductionStore()
const datatable = ref(null)
const loading = ref(false)

const cols = ref([
  { field: "id", title: "ID" , hide: true},
  { field: "user.name", title: "Created by", hide: true },
  { field: "po_nr", title: "PO Number", hide: false },
  { field: "po_date", title: "PO delivery", hide: false },
  { field: "client.name", title: "Client", hide: false },
  { field: "product.drawing_nr", title: "Product", hide: false },
  { field: "produced", title: "Produced", hide: true },
  { field: "sended", title: "Sended", hide: true },
  { field: "Price", title: "Price", hide: true },
  { field: "total", title: "Total", hide: false },
  { field: "invoice", title: "Invoice", hide: false },
  { field: "extra_process", title: "Extra process", hide: true },
  { field: "statuss_label", title: "Status", hide: false },
  { field: "outsource_statuss_label", title: "Outsource Status", hide: false },
  { field: "created_at", title: "Created at", hide: true },
  { field: "updated_at", title: "Updated at", hide: true },
])

const params = reactive({
  current_page: 1,
  pagesize: 10,
  sort_column: "id",
  sort_direction: "asc",
  search: ""
})

const rows = ref([])         
const total_rows = ref(0)   

const isOpen = ref(false)

onMounted(async () => {
    await fetchData()
})

const fetchData = async () => {
  loading.value = true
  try {
    const response = await production.fetchPlan({
      page: params.current_page,
      perPage: params.pagesize,
      sort: params.sort_column,
      direction: params.sort_direction,
      search: params.search,
    })

    rows.value = production.plans
    total_rows.value = production.total
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
</script>

<template>
    <AdminLayout>

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
            <div class="flex items-center gap-5">
              <button @click="fetchData" class="p-2 rounded-md hover:bg-gray-100 transition">
                <RefreshIcon class="w-5 h-5 text-gray-500 hover:text-gray-700 transition" />
              </button>
              <input
                type="text"
                v-model="params.search"
                placeholder="Global client search"
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
            :sortable="true"
            :pageSize="params.pageSize"
            skin="bh-table-compact"
            :search="params.search"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :serverMode="true"
            @change="changeServer"
        >
            <template #created_at="data">
                <span class="text-gray-500">{{ data.value.created_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
            <template #updated_at="data">
                <span class="text-gray-500">{{ data.value.updated_at ? moment(data.value.created_at).format('YYYY-MM-DD') : 'no data' }}</span>
            </template>
        </vue3-datatable>
    </AdminLayout>
</template>

<style>

</style>